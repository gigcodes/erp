<?php

namespace App\Http\Controllers\Social;

use Auth;
use Crypt;
use Session;
use Response;
use App\Setting;
use Facebook\Facebook;
use App\Social\SocialAdset;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use App\Http\Controllers\Controller;
use App\LogRequest;

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
        $adsets_data = SocialAdset::orderby('id', 'desc');
        $adsets_data = $adsets_data->get();

        $configs = \App\Social\SocialConfig::pluck('name', 'id');
//        $campaingns = \App\Social\SocialCampaign::pluck('name', 'ref_campaign_id')->where('ref_campaign_id', '!=', '');
        $campaingns = \App\Social\SocialCampaign::pluck('name', 'id');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            //  $query = SocialAdset::where('config_id',$id);

            $adsets = SocialAdset::orderby('id', 'desc');
        } else {
            $adsets = SocialAdset::latest();
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

        $websites = \App\StoreWebsite::select('id', 'title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adsets.data', compact('campaingns', 'adsets', 'configs', 'adsets_data'))->render(),
                'links' => (string) $adsets->render(),
            ], 200);
        }

        return view('social.adsets.index', compact('campaingns', 'adsets', 'configs', 'adsets_data'));
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
        $campaingns = \App\Social\SocialCampaign::where('ref_campaign_id', '!=', '')->get();

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

        //   $post->post_by = Auth::user()->id;
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
        //$this->ad_acc_id = $this->getAdAccount($config, $this->fb, $post->id);
        $this->ad_acc_id = $config->ads_manager;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($this->ad_acc_id != '') {
            if ($config->platform == 'facebook') {
                try {
                    $data['access_token'] = $this->user_access_token;
                    $data['name'] = $request->input('name');
                    //	$data['destination_type']=$request->input('destination_type');
                    // $data['campaign_id'] = '23853645665760505'; //$request->input('campaign_id');
                    $data['campaign_id'] = $request->input('campaign_id');
                    $data['billing_event'] = $request->input('billing_event');
                    $data['bid_amount'] = 100;

                    //	$data['start_time']=strtotime($request->input('start_time'));
                    $data['OPTIMIZATION_GOAL'] = 'REACH';
                    //   $data['end_time'] = strtotime($request->input('end_time'));
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
                    //    dd($resp);
                    $resp = json_decode($resp); //response decoded
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'store');
                    //    dd($resp);
                    if (isset($resp->error->message)) {
                        $post->live_status = 'error';
                        //  $post->ref_campaign_id=$resp->id;
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
                    //        dd($data);
                    $data['access_token'] = $this->user_access_token;
                    $data['name'] = $request->input('name');
                    //	$data['destination_type']=$request->input('destination_type');
                    $data['campaign_id'] = $request->input('campaign_id');
                    $data['billing_event'] = $request->input('billing_event');

                    //	$data['start_time']=strtotime($request->input('start_time'));
                    //  $data['OPTIMIZATION_GOAL'] ='LINK_CLICKS';
                    //$data['billing_event'] ='IMPRESSIONS';
                    $data['end_time'] = strtotime($request->input('end_time'));
                    $data['targeting'] = json_encode(['geo_locations' => ['countries' => ['US']], 'publisher_platforms' => ['instagram']]);
                    $data['bid_amount'] = (int) $request->input('bid_amount');
                    $data['daily_budget'] = (int) $request->input('daily_budget');
                    $data['status'] = $request->input('status');

                    //    $data["bid_amount"]=1000;
                    //$data["daily_budget"]=10000;

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
                    //    dd($resp);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $resp = json_decode($resp); //response deocded
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'store');

                    //    dd($resp);
                    if (isset($resp->error->message)) {
                        // dd($resp);
                        $post->live_status = 'error';
                        //  $post->ref_campaign_id=$resp->id;
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
                    // dd($e);
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
        // $config->update($data);

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

    private function getInstaID($config, $fb, $post_id)
    {
        $token = $config->token;
        $page_id = $config->page_id;
        $url = "https://graph.facebook.com/v12.0/$page_id?fields=instagram_business_account&access_token=$token";
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);
        $resp = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'getInstaID');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-getInstaID', $resp);
        $resp = json_decode($resp, true);
        if (isset($resp['instagram_business_account'])) {
            return $resp['instagram_business_account']['id'];
        }

        return '';
    }

    private function addMedia($config, $post, $mediaurl, $insta_id)
    {
        $token = $config->token;
        $page_id = $config->page_id;
        $post_id = $post->id;
        $caption = $post->post_body;
        $postfields = "image_url=$mediaurl&caption=$caption&access_token=$token";
        $url = "https://graph.facebook.com/v12.0/$insta_id/media";
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $resp = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($postfields), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'addMedia');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-addMedia', $resp);
        $resp = json_decode($resp, true);

        if (isset($resp['id'])) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'addMedia', $resp['id']);

            return $resp['id'];
        }

        return '';
    }

    private function publishMedia($config, $post, $media_id, $insta_id)
    {
        $token = $config->token;
        $page_id = $config->page_id;
        $post_id = $post->id;
        $caption = $post->post_body;
        $postfields = "creation_id=$media_id&access_token=$token";
        $url = "https://graph.facebook.com/v12.0/$insta_id/media_publish";
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $resp = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'POST', json_encode($postfields), $resp, $httpcode, \App\Http\Controllers\SocialAdsetController::class, 'publishMedia');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response publishMedia', $resp);
        $resp = json_decode($resp, true);

        if (isset($resp['id'])) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'publishMedia', $resp['id']);

            return $resp['id'];
        }

        return '';
    }
}
