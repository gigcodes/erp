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
use App\Http\Controllers\Controller;

class SocialCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $campaigns = SocialCampaign::orderby('id', 'desc')->with('account.storeWebsite');
        } else {
            $campaigns = SocialCampaign::latest()->with('account.storeWebsite');
        }

        $configs = SocialAdAccount::pluck('name', 'id');

        if (! empty($request->date)) {
            $campaigns->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $campaigns->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $campaigns->where('name', $request->campaign_name);
        }

        if (! empty($request->objective)) {
            $campaigns->whereIn('objective_name', $request->objective);
        }

        if (! empty($request->type)) {
            $type = $request->type;
            $campaigns->where('buying_type', 'LIKE', '%' . $request->type . '%');
        }

        if (! empty($request->status)) {
            $status = $request->status;
            $campaigns->where('status', 'LIKE', '%' . $request->status . '%');
        }

        $campaigns = $campaigns->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.campaigns.data', compact('campaigns', 'configs', 'type', 'status'))->render(),
                'links' => (string) $campaigns->render(),
            ]);
        }

        return view('social.campaigns.index', compact('campaigns', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialCampaign';
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

        return view('social.campaigns.create', compact('configs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $post = SocialConfig::create([
            'config_id' => $request->config_id,
            'name' => $request->name,
            'objective_name' => $request->objective,
            'buying_type' => $request->buying_type,
            'daily_budget' => $request->daily_budget,
            'status' => $request->status,
        ]);

        $data['name'] = $request->input('name');
        $data['objective'] = $request->input('objective');
        $data['status'] = $request->input('status');
        $data['special_ad_categories '] = ['NONE'];

        if ($request->has('buying_type')) {
            $data['buying_type'] = $request->input('buying_type');
        } else {
            $data['buying_type'] = 'AUCTION';
        }
        $data['special_ad_categories'] = [];
        $config = SocialAdAccount::find($post->config_id);

        $fb = new FB($config->page_token);
        $this->socialPostLog($config->id, $post->id, 'facebook', 'message', 'get page access token');
        try {
            $response = $fb->createCampaign($config->ad_account_id, $data);
            $post->live_status = 'sucess';
            $post->ref_campaign_id = $response['id'];
            $post->save();
            Session::flash('message', 'Campaign created  successfully');

            return redirect()->route('social.campaign.index');
        } catch (\Exception $e) {
            $post->live_status = 'error';
            $post->save();
            Session::flash('message', $e);
            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);

            return redirect()->route('social.campaign.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialCampaign  $SocialCampaign
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
        $config = SocialCampaign::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialCampaign  $SocialCampaign
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $config = SocialCampaign::findorfail($request->id);
        $config->delete();

        return Response::json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialCampaign')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
