<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Session;
use Response;
use App\Setting;
use App\LogRequest;
use Facebook\Facebook;
use App\Social\SocialAd;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use App\Http\Controllers\Controller;

class SocialAdsController extends Controller
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
        $ads_data = SocialAd::orderby('id', 'desc');
        $ads_data = $ads_data->get();

        $configs = \App\Social\SocialConfig::pluck('name', 'id');
        $adsets = \App\Social\SocialAdset::pluck('name', 'ref_adset_id')->where('ref_adset_id', '!=', '');

        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $ads = SocialAd::orderby('id', 'desc');
        } else {
            $ads = SocialAd::latest();
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

        $websites = \App\StoreWebsite::select('id', 'title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.ads.data', compact('ads', 'configs', 'adsets', 'ads_data'))->render(),
                'links' => (string) $ads->render(),
            ], 200);
        }

        return view('social.ads.index', compact('ads', 'configs', 'adsets', 'ads_data'));
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $configs = \App\Social\SocialConfig::pluck('name', 'id');

        return view('social.ads.create', compact('configs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new SocialAd;
        $post->config_id = $request->config_id;
        $post->name = $request->name;
        $post->adset_id = $request->adset_id;
        $post->creative_id = $request->adcreative_id;
        $post->status = $request->status;
        $post->ad_creative_name = $request->ad_creative_name;
        $post->ad_set_name = $request->ad_set_name;
        $post->save();

        $data['name'] = $request->input('name');
        $data['adset_id'] = $request->input('adset_id');
        $data['status'] = $request->input('status');
        $data['creative'] = json_encode(['creative_id' => $request->input('adcreative_id')]);

        $config = SocialConfig::find($post->config_id);

        $this->fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' => $config->api_secret,
            'default_graph_version' => 'v15.0',
        ]);
        $this->user_access_token = $config->token;
        $this->ad_acc_id = $config->ads_manager;

        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'get page access token');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        if ($this->ad_acc_id != '') {
            if ($config->platform == 'facebook') {
                try {
                    $data['access_token'] = $this->user_access_token;
                    // $url = 'https://graph.facebook.com/v15.0/act_723851186073937/ads';
                    $url = 'https://graph.facebook.com/v15.0/' . $this->ad_acc_id . '/ads';

                    // Call to Graph api here
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                    $resp = curl_exec($curl);
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'response->create ad', $resp);
                    $resp = json_decode($resp); //response deocded
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsController::class, 'store');

                    if (isset($resp->error->message)) {
                        $post->live_status = 'error';
                        $post->save();
                        Session::flash('message', $resp->error->message);
                    } else {
                        $post->live_status = 'sucess';
                        $post->save();
                        Session::flash('message', 'Campaign created  successfully');
                    }

                    return redirect()->route('social.ad.index');
                } catch (Exception $e) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
                    Session::flash('message', $e);

                    return redirect()->route('social.ad.index');
                }
            } else {
                try {
                    $data['access_token'] = $this->user_access_token;
                    $url = 'https://graph.facebook.com/v15.0/' . $this->ad_acc_id . '/ads';

                    // Call to Graph api here
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                    $resp = curl_exec($curl);
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'response->create ad', $resp);
                    $resp = json_decode($resp); //responsee deocded
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdsController::class, 'store');

                    if (isset($resp->error->message)) {
                        Session::flash('message', $resp->error->message);
                    } else {
                        Session::flash('message', 'Campaign created  successfully');
                    }

                    return redirect()->route('social.ad.index');
                } catch (Exception $e) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
                    Session::flash('message', $e);

                    return redirect()->route('social.ad.index');
                }
            }
        } else {
            Session::flash('message', 'problem in getting ad account or token');

            return redirect()->route('social.ad.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialAd  $SocialAd
     * @return \Illuminate\Http\Response
     */
    public function show(SocialAd $SocialAd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialAd  $SocialAd
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
     * Update the specified resource in storage.
     *
     * @param  \App\SocialAd  $SocialAd
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialAd $SocialAd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialAd  $SocialAd
     * @return \Illuminate\Http\Response
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
            $url = sprintf('https://graph.facebook.com/v15.0//me/adaccounts?access_token=' . $token);
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
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialAd')->orderBy('created_at', 'desc')->get();

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
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-getInstaID', $resp);
        $resp = json_decode($resp, true); //response decode
        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, \App\Http\Controllers\SocialAdsController::class, 'getInstaID');
        if (isset($resp['instagram_business_account'])) {
            return $resp['instagram_business_account']['id'];
        }

        return '';
    }

    public function getpost(Request $request)
    {
        $config = \App\Social\SocialConfig::find($request->id);
        $postData = $this->getPostData($config);

        return response()->json($postData);
    }

    public function getPostData($config)
    {
        $token = $config->token;
        $this->fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' => $config->api_secret,
            'default_graph_version' => 'v15.0',
        ]);

        $this->ad_acc_id = $config->ads_manager;

        $url = "https://graph.facebook.com/v15.0/$this->ad_acc_id?fields=adsets{name,id},adcreatives{id,name}&limit=100&access_token=$token";
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
        $resp = json_decode($resp, true); //response decoded

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, \App\Http\Controllers\SocialAdsController::class, 'getPostData');
        if (isset($resp['error'])) {
            return ['type' => 'error', 'message' => $resp['error']['message']];
        } else {
            return ['type' => 'success', 'message' => $resp];
        }
    }
}
