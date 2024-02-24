<?php

namespace App\Http\Controllers\Social;

use Crypt;
use Session;
use Response;
use App\Setting;
use App\LogRequest;
use Facebook\Facebook;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Social\SocialPostLog;
use App\Social\SocialCampaign;
use App\Models\SocialAdAccount;
use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;

class SocialAdCreativeController extends Controller
{
    /**2
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $fb;

    private $user_access_token;

    private $ad_acc_id;

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
            ], 200);
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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = new SocialAdCreative();
        $post->config_id = $request->config_id;
        $post->object_story_title = $request->object_story_title;
        $post->object_story_id = $request->object_story_id;
        $post->name = $request->name;
        $post->save();

        $data['name'] = $request->input('name');
        $data['object_story_id'] = $request->input('object_story_id');

        $config = SocialConfig::find($post->config_id);

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
            try {
                $data['access_token'] = $this->user_access_token;
                //$url = 'https://graph.facebook.com/v15.0/act_723851186073937/adcreatives';
                $url = 'https://graph.facebook.com/v15.0/' . $this->ad_acc_id . '/adcreatives';

                // Call to Graph api here
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

                $resp = curl_exec($curl);
                $this->socialPostLog($config->id, $post->id, $config->platform, 'response->create adcreatives', $resp);
                $resp = json_decode($resp); //response deocded
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);

                LogRequest::log($startTime, $url, 'POST', json_encode($data), $resp, $httpcode, \App\Http\Controllers\SocialAdCreativeController::class, 'store');

                if (isset($resp->error->message)) {
                    $post->live_status = 'error';
                    $post->save();
                    Session::flash('message', $resp->error->message);
                } else {
                    $post->live_status = 'sucess';
                    $post->ref_adcreative_id = $resp->id;
                    $post->save();

                    Session::flash('message', 'adcreative created  successfully');
                }

                return redirect()->route('social.adcreative.index');
            } catch (\Exception $e) {
                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
                Session::flash('message', $e);

                return redirect()->route('social.adcreative.index');
            }
        } else {
            $post->live_status = 'error';
            $post->save();

            Session::flash('message', 'problem in getting ad account or token');

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
     * @param  \App\SocialAdCreative  $SocialAdCreative
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

    public function getPostData($config)
    {
        $token = $config->page_token;
        $page_id = $config->page_id;
        $url = "https://graph.facebook.com/v15.0/$page_id?fields=posts&access_token=$token";
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
        $resp = json_decode($resp, true); //decode resoponse

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, \App\Http\Controllers\SocialAdCreativeController::class, 'getPostData');
        if (isset($resp['error'])) {
            return ['type' => 'error', 'message' => $resp['error']['message']];
        } else {
            return ['type' => 'success', 'message' => $resp];
        }
    }
}
