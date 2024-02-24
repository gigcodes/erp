<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Session;
use Response;
use App\Setting;
use App\Social\SocialAdset;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Http\Controllers\Controller;
use JanuSoftware\Facebook\Exception\SDKException;

class SocialAdsetController extends Controller
{
    /**2
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $user_access_token;

    public function index(Request $request)
    {
        $configs = SocialAdAccount::pluck('name', 'id');
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $adsets = SocialAdset::orderby('id', 'desc')->with('account.storeWebsite', 'campaign');
        } else {
            $adsets = SocialAdset::latest()->with('account.storeWebsite', 'campaign');
        }

        if (! empty($request->date)) {
            $adsets->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if (! empty($request->config_name)) {
            $adsets->whereIn('config_id', $request->config_name);
        }

        if (! empty($request->campaign_name)) {
            $adsets->whereIn('campaign_id', $request->campaign_name);
        }

        if (! empty($request->event)) {
            $adsets->whereIn('billing_event', $request->event);
        }

        if (! empty($request->name)) {
            $adsets->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (! empty($request->status)) {
            $adsets->where('status', 'LIKE', '%' . $request->status . '%');
        }

        $adsets = $adsets->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adsets.data', compact('adsets', 'configs'))->render(),
                'links' => (string) $adsets->render(),
            ], 200);
        }

        return view('social.adsets.index', compact('adsets', 'configs'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialAdset';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $configs = SocialAdAccount::pluck('name', 'id');
        $campaingns = SocialCampaign::where('ref_campaign_id', '!=', '')->get();

        return view('social.adsets.create', compact('configs', 'campaingns'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws SDKException
     */
    public function store(Request $request)
    {
        $request->validate([
            'config_id' => 'required',
            'campaign_id' => 'required',
            'name' => 'required',
            'billing_event' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'daily_budget' => 'required',
            'bid_amount' => 'nullable',
            'status' => 'required',
        ]);

        $adset = SocialAdset::create([
            'config_id' => $request->get('config_id'),
            'campaign_id' => $request->get('campaign_id'),
            'name' => $request->get('name'),
            'billing_event' => $request->get('billing_event'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'daily_budget' => $request->get('daily_budget'),
            'bid_amount' => $request->get('bid_amount'),
            'status' => $request->get('status'),
        ]);

        $data['name'] = $request->input('name');
        $data['objective'] = $request->input('objective');
        $data['status'] = $request->input('status');
        $data['special_ad_categories '] = json_encode(['NONE']);

        if ($request->has('buying_type')) {
            $data['buying_type'] = $request->input('buying_type');
        } else {
            $data['buying_type'] = 'AUCTION';
        }

        if ($request->has('daily_budget')) {
            $data['daily_budget'] = $request->input('daily_budget');
        }

        $config = SocialAdAccount::find($adset->config_id);
        $page_id = $config->page_id;
        $fb = new FB($config->page_token);
        $this->user_access_token = $config->token;
        $this->socialPostLog($config->id, $adset->id, $config->platform, 'message', 'get page access token');

        if ($config->ad_account_id != '') {
            try {
                $data['access_token'] = $this->user_access_token;
                $data['name'] = $request->input('name');
                $data['campaign_id'] = $adset->campaign->ref_campaign_id;
                $data['billing_event'] = $request->input('billing_event');
                $data['bid_amount'] = 100;

                $data['OPTIMIZATION_GOAL'] = 'REACH';
                $data['targeting'] = json_encode(['geo_locations' => ['countries' => ['US']]]);
                if ($request->has('daily_budget')) {
                    $data['daily_budget'] = (int) $request->input('daily_budget');
                }
                $data['bid_amount'] = $request->input('bid_amount');
                $data['daily_budget'] = $request->input('daily_budget');
                $data['status'] = $request->input('status');
                $data['promoted_object'] = json_encode(['page_id' => $page_id]);

                $response = $fb->createAdsets($config->ad_account_id, $data);

                $this->socialPostLog($config->id, $adset->id, $config->platform, 'response->create adset', $response);
                $adset->update([
                    'live_status' => 'ACTIVE',
                    'status' => 'ACTIVE',
                ]);
                Session::flash('message', 'adset created  successfully');

                return redirect()->route('social.adset.index');
            } catch (\Exception $e) {
                $this->socialPostLog($config->id, $adset->id, $config->platform, 'error', $e);
                Session::flash('message', 'Unable to create adset');

                return redirect()->route('social.adset.index');
            }
        } else {
            $adset->update([
                'live_status' => 'ERROR',
            ]);
            Session::flash('message', 'problem in getting ad account or token');

            return redirect()->route('social.adset.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SocialAdset  $SocialAdset
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
        $config = SocialAdset::findorfail($request->id);
        $data = $request->except('_token', 'id');
        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialAdset  $SocialAdset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialAdset::findorfail($request->id);
        $config->delete();

        return Response::json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAdset')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
