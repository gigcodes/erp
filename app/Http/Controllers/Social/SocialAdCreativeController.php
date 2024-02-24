<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Session;
use Response;
use App\Setting;
use Illuminate\View\View;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;

class SocialAdCreativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        $configs = SocialAdAccount::pluck('name', 'id');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $adcreatives = SocialAdCreative::orderby('id', 'desc')->with('account.storeWebsite');
        } else {
            $adcreatives = SocialAdCreative::latest()->with('account.storeWebsite');
        }

        if (! empty($request->date)) {
            $adcreatives->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $adcreatives->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $adcreatives->whereIn('campaign_id', $request->campaign_name);
        }

        if (! empty($request->name)) {
            $adcreatives->whereIn('name', $request->name);
        }

        $adcreatives = $adcreatives->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adcreatives.data', compact('adcreatives', 'configs'))->render(),
                'links' => (string) $adcreatives->render(),
            ]);
        }

        return view('social.adcreatives.index', compact('adcreatives', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialAdCreative';
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
        $campaingns = SocialCampaign::whereNotNull('ref_campaign_id')->get();

        return view('social.adcreatives.create', compact('configs', 'campaingns'));
    }

    public function getpost(Request $request)
    {
        $postData = SocialConfig::where('ad_account_id', $request->id)->with('posts')->first()->toArray();

        return response()->json($postData['posts']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $ad_creative = SocialAdCreative::create([
            'config_id' => $request->config_id,
            'object_story_title' => $request->object_story_title,
            'object_story_id' => $request->object_story_id,
            'name' => $request->name,

        ]);

        $data['name'] = $request->input('name');
        $data['object_story_id'] = $request->input('object_story_id');

        $config = SocialAdAccount::find($ad_creative->config_id);
        $fb = new FB($config->page_token);

        $this->socialPostLog($config->id, $ad_creative->id, $config->platform, 'message', 'get page access token');

        try {
            $fb->createAdCreatives($config->ad_account_id, $data);
            $ad_creative->update([
                'live_status' => 'ACTIVE',
            ]);
            Session::flash('message', 'adcreative created  successfully');

            return redirect()->route('social.adcreative.index');
        } catch (\Exception $e) {
            $this->socialPostLog($config->id, $ad_creative->id, $config->platform, 'error', $e);
            $ad_creative->update([
                'live_status' => 'ERROR',
            ]);
            Session::flash('message', $e);

            return redirect()->route('social.adcreative.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialAdCreative  $SocialAdCreative
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
        $config = SocialAdCreative::findorfail($request->id);
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
        $config = SocialAdCreative::findorfail($request->id);
        $config->delete();

        return Response::json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAdCreative')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
