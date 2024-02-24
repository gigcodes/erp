<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Session;
use Response;
use App\Setting;
use App\LogRequest;
use Facebook\Facebook;
use App\Social\SocialAdset;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Http\Controllers\Controller;

class SocialAdsetController extends Controller
{
    /**2
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $fb;

    private $user_access_token;

    private $page_access_token;

    private $page_id;

    private $ad_acc_id;

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

        return view('social.adsets.index', compact( 'adsets', 'configs'));
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
        $configs = \App\Social\SocialConfig::pluck('name', 'id');
        $campaingns = SocialCampaign::where('ref_campaign_id', '!=', '')->get();

        return view('social.adsets.create', compact('configs', 'campaingns'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new SocialAdset;
        $post->config_id = $request->config_id;
        $post->campaign_id = $request->campaign_id;
        $post->name = $request->name;
        $post->billing_event = $request->billing_event;
        $post->start_time = $request->start_time;
        $post->end_time = $request->end_time;
        $post->daily_budget = $request->daily_budget;
        $post->bid_amount = $request->bid_amount;
        $post->status = $request->status;

        $post->save();

        $data['name'] = $request->input('name');
        $data['objective'] = $request->input('objective');
        $data['status'] = $request->input('status');
        $data['special_ad_categories '] = ['NONE'];

        if ($request->has('buying_type')) {
            $data['buying_type'] = $request->input('buying_type');
        } else {
            $data['buying_type'] = 'AUCTION';
        }

        if ($request->has('daily_budget')) {
            $data['daily_budget'] = $request->input('daily_budget');
        }

        $config = SocialConfig::find($post->config_id);
        $page_id = $config->page_id;
        $this->fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' => $config->api_secret,
            'default_graph_version' => 'v15.0',
        ]);
        $this->user_access_token = $config->token;
        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'get page access token');
        $this->ad_acc_id = $config->ads_manager;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($this->ad_acc_id != '') {
            if ($config->platform == 'facebook') {
                try {
                    $data['access_token'] = $this->user_access_token;
                    $data['name'] = $request->input('name');
                    $data['campaign_id'] = $request->input('campaign_id');
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

                    $url = 'https://graph.facebook.com/v15.0/' . $this->ad_acc_id . '/adsets';
                    //$url = 'https://graph.facebook.com/v15.0/act_723851186073937/adsets';

                    // Call to Graph api here
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                    $resp = curl_exec($curl);
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'response->create adset', $resp);
                    $resp = json_decode($resp); //response decoded
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'store');
                    if (isset($resp->error->message)) {
                        $post->live_status = 'error';
                        $post->save();
                        Session::flash('message', $resp->error->message);
                    } else {
                        $post->live_status = 'sucess';
                        $post->ref_adset_id = $resp->id;
                        $post->save();

                        Session::flash('message', 'adset created  successfully');
                    }

                    return redirect()->route('social.adset.index');
                } catch (Exception $e) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
                    Session::flash('message', $e);

                    return redirect()->route('social.adset.index');
                }
            } else {
                try {
                    $data['access_token'] = $this->user_access_token;
                    $data['name'] = $request->input('name');
                    $data['campaign_id'] = $request->input('campaign_id');
                    $data['billing_event'] = $request->input('billing_event');
                    $data['end_time'] = strtotime($request->input('end_time'));
                    $data['targeting'] = json_encode(['geo_locations' => ['countries' => ['US']], 'publisher_platforms' => ['instagram']]);
                    $data['bid_amount'] = (int) $request->input('bid_amount');
                    $data['daily_budget'] = (int) $request->input('daily_budget');
                    $data['status'] = $request->input('status');

                    $url = 'https://graph.facebook.com/v15.0/' . $this->ad_acc_id . '/adsets';

                    // Call to Graph api here
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                    $resp = curl_exec($curl);
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'response->create adset', $resp);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $resp = json_decode($resp); //response deocded
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'store');

                    if (isset($resp->error->message)) {
                        $post->live_status = 'error';
                        $post->save();
                        Session::flash('message', $resp->error->message);
                    } else {
                        $post->live_status = 'sucess';
                        $post->ref_adset_id = $resp->id;
                        $post->save();

                        Session::flash('message', 'adset created  successfully');
                    }

                    return redirect()->route('social.adset.index');
                } catch (Exception $e) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
                    Session::flash('message', $e);

                    return redirect()->route('social.adset.index');
                }
            }
        } else {
            $post->live_status = 'error';
            $post->save();

            Session::flash('message', 'problem in getting ad account or token');

            return redirect()->route('social.adset.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialAdset  $SocialAdset
     * @return \Illuminate\Http\Response
     */
    public function show(SocialAdset $SocialAdset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialAdset  $SocialAdset
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
     * Update the specified resource in storage.
     *
     * @param  \App\SocialAdset  $SocialAdset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialAdset $SocialAdset)
    {
        //
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

    public function getPageAccessToken($config, $fb, $post_id)
    {
        $response = '';

        try {
            $token = $config->token;
            $page_id = $config->page_id;
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $response = $fb->get('/me/accounts', $token);
            $this->socialPostLog($config->id, $post_id, $config->platform, 'success', 'get my accounts');
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            // When Graph returns an error
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->' . $e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->' . $e->getMessage());
        }
        if ($response != '') {
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                    if ($key['id'] == $page_id) {
                        return $key['access_token'];
                    }
                }
            } catch (\exception $e) {
                $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get token->' . $e->getMessage());
            }
        }
    }

    public function getAdAccount($config, $fb, $post_id)
    {
        $response = '';

        try {
            $token = $config->token;
            $page_id = $config->page_id;
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            // $response = $fb->get('/me/adaccounts', $token); Old
            $url = sprintf('https://graph.facebook.com/v15.0//me/adaccounts?access_token=' . $token); //New using graph API
            $response = SocialHelper::curlGetRequest($url);
            $this->socialPostLog($config->id, $post_id, $config->platform, 'success', 'get my adaccounts');
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            // When Graph returns an error
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get adaccounts->' . $e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get adaccounts->' . $e->getMessage());
        }
        if ($response != '') {
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                    return $key['id'];
                }
            } catch (\exception $e) {
                $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get adaccounts id->' . $e->getMessage());
            }
        }
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAdset')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}
