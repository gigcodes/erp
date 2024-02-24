<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Illuminate\Http\RedirectResponse;
use Session;
use Response;
use App\Setting;
use App\Social\SocialAd;
use Illuminate\View\View;
use App\Social\SocialAdset;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Models\SocialAdAccount;
use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;
use JanuSoftware\Facebook\Exception\SDKException;

class SocialAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        $ads_data = SocialAd::orderby('id', 'desc');
        $ads_data = $ads_data->get();

        $configs = SocialAdAccount::pluck('name', 'id');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $ads = SocialAd::orderby('id', 'desc')->with('account.storeWebsite', 'adset', 'creative');
        } else {
            $ads = SocialAd::latest()->with('account.storeWebsite', 'adset', 'creative');
        }

        if (! empty($request->date)) {
            $ads->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->name)) {
            $ads->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (! empty($request->config_name)) {
            $ads->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->adset_name)) {
            $ads->whereIn('ad_set_name', $request->adset_name);
        }

        $ads = $ads->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.ads.data', compact('ads', 'configs', 'ads_data'))->render(),
                'links' => (string) $ads->render(),
            ]);
        }

        return view('social.ads.index', compact('ads', 'configs', 'ads_data'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialAd';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $configs = SocialAdAccount::pluck('name', 'id');

        return view('social.ads.create', compact('configs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     *
     * @throws SDKException
     */
    public function store(Request $request)
    {
        $ad = SocialAd::create([
            'config_id' => $request->config_id,
            'name' => $request->name,
            'adset_id' => $request->adset_id,
            'creative_id' => $request->adcreative_id,
            'status' => $request->status,
            'ad_creative_name' => $request->ad_creative_name,
            'ad_set_name' => $request->ad_set_name,
        ]);
        $data['name'] = $request->input('name');
        $data['adset_id'] = $request->input('adset_id');
        $data['status'] = $request->input('status');
        $data['creative'] = json_encode(['creative_id' => $request->input('adcreative_id')]);

        $config = SocialAdAccount::find($ad->config_id);
        $fb = new FB($config->page_token);
        $this->socialPostLog($config->id, $ad->id, $config->platform, 'message', 'get page access token');
        try {
            $fb->createAd($config->ad_account_id, $data);
            $ad->update([
                'live_status' => 'SUCCESS',
            ]);

            Session::flash('message', 'Campaign created  successfully');

            return redirect()->route('social.ad.index');
        } catch (\Exception $e) {
            $ad->update([
                'status' => 'ERROR',
            ]);
            $this->socialPostLog($config->id, $ad->id, $config->platform, 'error', $e);
            Session::flash('message', $e);

            return redirect()->route('social.ad.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'status' => 'required',
        ]);
        $config = SocialAd::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $config = SocialAd::findorfail($request->id);
        $config->delete();

        return Response::json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAd')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    public function getpost(Request $request)
    {
        $postData = SocialConfig::where('ad_account_id', $request->id)->with('posts')->first()->toArray();

        return response()->json($postData['posts']);
    }

    public function getAdsets(Request $request)
    {
        $adsets = SocialAdset::where('config_id', $request->id)->get()->toArray();
        $adCreatives = SocialAdCreative::where('config_id', $request->id)->get()->toArray();

        return response()->json(['adsets' => $adsets, 'adcreatives' => $adCreatives]);
    }
}
