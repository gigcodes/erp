<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Social\SocialConfig;
use Crypt;
use Illuminate\Http\Request;
use Response;

class SocialConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->term || $request->date && $request->customer_support == 0) {
            $query = SocialConfig::query();

            $socialConfigs = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));
        } else {
            $query = SocialConfig::query();

            if ($request->store_website_id) {
                $query->whereIn('store_website_id', $request->store_website_id);
            }

            if ($request->user_name) {
                $query->whereIn('email', $request->user_name);
            }

            if ($request->platform) {
                $query->whereIn('platform', $request->platform);
            }
            $socialConfigs = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));
        }
        
       // $adsAccountManager = $this->getadsAccountManager();
        $websites = \App\StoreWebsite::select('id', 'title')->get();
        $user_names = SocialConfig::select('email')->distinct()->get();
        $platforms = SocialConfig::select('platform')->distinct()->get();
        $selected_website = $request->store_website_id;
        $selected_user_name = $request->user_name;
        $selected_platform = $request->platform;
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.configs.partials.data', compact('socialConfigs'))->render(),
                'links' => (string) $socialConfigs->render(),
            ], 200);
        }

        return view('social.configs.index', compact('socialConfigs', 'websites', 'user_names', 'platforms', 'selected_website', 'selected_user_name', 'selected_platform'));
    }

    public function getadsAccountManager(Request $request){

        $user_access_token = $request["token"];
        $fields = 'account_id,name,currency,balance,account_status,business_name,business_id';

        $url = 'https://graph.facebook.com/v15.0/me/adaccounts?fields='.$fields;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$user_access_token
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
 
        
        return $data['data'];
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function getfbToken(){
            
        return redirect('https://www.facebook.com/dialog/oauth?client_id=1465672917171155&redirect_uri=https://example.com&scope=manage_pages,pages_manage_posts');
            $curl = curl_init();

            $url = sprintf('https://www.facebook.com/dialog/oauth?client_id=1465672917171155&redirect_uri=https://example.com&scope=manage_pages,pages_manage_posts');
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
    }
    public function getfbTokenBack(Request $request){
      

            $code = $request['code'];
            $redirect = 'https://56e3-110-227-254-30.ngrok.io/social/config/fbtokenback';

        
            $curl = curl_init();

            $url = sprintf('https://graph.facebook.com/v15.0/oauth/access_token?client_id=559475859451724&redirect_uri='.$redirect.'&client_secret=53ecd1fd8103c478830c8fef0673087e&code='.$code);
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            $curl = curl_init();

            $url = sprintf('https://graph.facebook.com/v15.0//me/?access_token='.$response['access_token']); 

           
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $responseMe = json_decode(curl_exec($curl), true);
            curl_close($curl);

            $data['account_id'] = $responseMe['id'];
            $data['name'] = $responseMe['name'];
            $data['token'] = $response['access_token'];
            SocialConfig::create($data);
            
            return redirect()->route('social.config.index');
            
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $this->validate($request, [
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'status' => 'required',
            'page_id' => 'required',
            'page_token' => 'required',
            'webhook_token' => 'required',
        ]);
        $pageId = $request->page_id;
        $data = $request->except('_token');

        if ($request->platform == 'instagram') {
            $curl = curl_init();

            $url = sprintf('https://graph.facebook.com/v15.0/'.$request->page_id.'?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->page_token);

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
           
            if ($id = $response['instagram_business_account']['id']) {
                $data['account_id'] = $id;
            } else {
                return redirect()->back()->withError('Page Linked Account ID not found.');
            }
        } else {
            $data['account_id'] = $pageId;
        }
        $data['password'] = Crypt::encrypt($request->password);
        SocialConfig::create($data);

        return redirect()->back()->withSuccess('You have successfully stored Config.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialConfig  $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function show(SocialConfig $SocialConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialConfig  $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
            //  'email' => 'required',
            //   'password' => 'required',
            'status' => 'required',
            'page_id' => 'required',
            'page_token' => 'required',
            'webhook_token' => 'required',
        ]);
        $pageId = $request->page_id;
        $config = SocialConfig::findorfail($request->id);
        $data = $request->except('_token', 'id');
        if(isset($request->adsmanager)){
            $data['ads_manager'] =  $request->adsmanager;
        }
        
        if ($request->platform == 'instagram') {
            $curl = curl_init();

            $url = sprintf('https://graph.facebook.com/v12.0/me?fields=%s&access_token=%s', 'id,name,instagram_business_account{id,username,profile_picture_url}', $request->page_token);

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if ($id = $response['instagram_business_account']['id']) {
                $data['account_id'] = $id;
            } else {
                return redirect()->back()->withError('Page Linked Account ID not found.');
            }
        } else {
            $data['account_id'] = $pageId;
        }

        $data['password'] = Crypt::encrypt($request->password);
        $config->fill($data);
        $config->save();
        // $config->update($data);

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SocialConfig  $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialConfig $SocialConfig)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialConfig  $SocialConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialConfig::findorfail($request->id);
        $config->delete();

        return Response::json([
            'success' => true,
            'message' => ' Config Deleted',
        ]);
    }
}
