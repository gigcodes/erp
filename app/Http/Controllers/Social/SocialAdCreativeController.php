<?php

namespace App\Http\Controllers\Social;

use App\Social\SocialAdCreative;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Setting;
use Validator;
use Crypt;
use Response;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Auth;
use Facebook\Facebook;
use App\Social\SocialConfig;
use App\Social\SocialPostLog;
use Session;



class SocialAdCreativeController extends Controller
{
    /**2
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $fb,$user_access_token,$page_access_token,$page_id,$ad_acc_id;
    public function index(Request $request)
    {
        $configs = \App\Social\SocialConfig::pluck("name","id");
        $campaingns = \App\Social\SocialCampaign::pluck("name","ref_campaign_id")->where("ref_campaign_id","!=","");
       
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {

          //  $query = SocialAdCreative::where('config_id',$id);


            $adcreatives = SocialAdCreative::orderby('id', 'desc')->paginate(Setting::get('pagination'));

        } else {
            $adcreatives = SocialAdCreative::latest()->paginate(Setting::get('pagination'));
        }
        $websites = \App\StoreWebsite::select('id','title')->get();
        
     
       
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.adcreatives.data', compact('campaingns','adcreatives','configs'))->render(),
                'links' => (string)$adcreatives->render()
            ], 200);
        }
     

        return view('social.adcreatives.index',compact('adcreatives', 'configs'));

    }
    public function socialPostLog($config_id,$post_id,$platform,$title,$description){
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;  
        $Log->log_description = $description;
        $Log->modal = "SocialAdCreative";
        $Log->save();
        return true;
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function create() {
        $configs = \App\Social\SocialConfig::pluck("name","id");
        $campaingns = \App\Social\SocialCampaign::where("ref_campaign_id","!=","")->get();
        
        return view('social.adcreatives.create',compact("configs","campaingns"));
    }

    public function getpost(Request $request) {
       $config = \App\Social\SocialConfig::find($request->id);
       $postData = $this->getPostData($config);
       return response()->json($postData);
    //   return $postData;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
	{
       
       
    $post =  new SocialAdCreative();
    $post->config_id = $request->config_id;
    $post->object_story_title = $request->object_story_title;
    $post->object_story_id = $request->object_story_id;
    $post->name = $request->name;
    $post->save();


    $data['name']=$request->input('name');
    $data['object_story_id']=$request->input('object_story_id');

    $config =SocialConfig::find($post->config_id );
  
    $this->fb = new Facebook([
        'app_id' => $config->api_key,
        'app_secret' =>  $config->api_secret,
        'default_graph_version' => 'v12.0',
    ]);
    $this->user_access_token =$config->token;
    $this->socialPostLog($config->id,$post->id,$config->platform,"message","get page access token");
    $this->ad_acc_id = $this->getAdAccount($config,$this->fb,$post->id);
  //  
    
    if( $this->ad_acc_id!= ""){

    if($config->platform == "facebook"){


        try{
    //        dd($data);
        $data['access_token']=$this->user_access_token;
        
            $url="https://graph.facebook.com/v12.0/".$this->ad_acc_id.'/adcreatives';

            // Call to Graph api here
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));


            $resp = curl_exec($curl);
            $this->socialPostLog($config->id,$post->id,$config->platform,"response->create adcreatives",$resp);
        //    dd($resp);
            $resp = json_decode($resp);
            curl_close($curl);
           
        //    dd($resp);
            if(isset($resp->error->message)){
                $post->live_status="error";
              //  $post->ref_campaign_id=$resp->id;
                $post->save();
                Session::flash('message',$resp->error->message);

            }else{
                $post->live_status="sucess";
                $post->ref_adcreative_id	=$resp->id;
                $post->save();

                Session::flash('message',"adcreative created  successfully");
            }    

            return redirect()->route('social.adcreative.index');
        }
        catch(Exception $e)
        {
            $this->socialPostLog($config->id,$post->id,$config->platform,"error",$e);
            Session::flash('message',$e);
            return redirect()->route('social.adcreative.index');
        }
    }else{
        try{
    //        dd($data);
              $data['access_token']=$this->user_access_token;
        
            $url="https://graph.facebook.com/v12.0/".$this->ad_acc_id.'/adcreatives';

            // Call to Graph api here
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));


            $resp = curl_exec($curl);
            $this->socialPostLog($config->id,$post->id,$config->platform,"response->create adcreatives",$resp);
        //    dd($resp);
            $resp = json_decode($resp);
            curl_close($curl);
            
        //    dd($resp);
            if(isset($resp->error->message)){
                $post->live_status="error";
                //  $post->ref_campaign_id=$resp->id;
                $post->save();
                Session::flash('message',$resp->error->message);

            }else{
                $post->live_status="sucess";
                $post->ref_adcreative_id	=$resp->id;
                $post->save();

                Session::flash('message',"adcreative created  successfully");
            }    

            return redirect()->route('social.adcreative.index');
        }
        catch(Exception $e)
        {
            $this->socialPostLog($config->id,$post->id,$config->platform,"error",$e);
            Session::flash('message',$e);
            return redirect()->route('social.adcreative.index');
        }
        
    }
}else{
    $post->live_status="error";
    $post->save();

    Session::flash('message',"problem in getting ad account or token");
    return redirect()->route('social.adcreative.index');
}

    
    
    


}


    /**
     * Display the specified resource.
     *
     * @param \App\SocialAdCreative $SocialAdCreative
     * @return \Illuminate\Http\Response
     */
    public function show(SocialAdCreative $SocialAdCreative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SocialAdCreative $SocialAdCreative
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
            "status"=> 'required',
        ]);
        $config = SocialAdCreative::findorfail($request->id);
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
     * @param \Illuminate\Http\Request $request
     * @param \App\SocialAdCreative $SocialAdCreative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialAdCreative $SocialAdCreative)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SocialAdCreative $SocialAdCreative
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialAdCreative::findorfail($request->id);
        $config->delete();
        return Response::json(array(
            'success' => true,
            'message' => ' Config Deleted'
        ));
    }
    public function getPageAccessToken($config,$fb,$post_id){
        $response="";

        try {
            $token = $config->token;
            $page_id = $config->page_id;
             // Get the \Facebook\GraphNodes\GraphUser object for the current user.
             // If you provided a 'default_access_token', the '{access-token}' is optional.
             $response = $fb->get('/me/accounts', $token);
             $this->socialPostLog($config->id,$post_id,$config->platform,"success","get my accounts");
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            // When Graph returns an error
            $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get accounts->".$e->getMessage());
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get accounts->".$e->getMessage());
           
        }
        if($response!= ""){
    
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                    if ($key['id'] == $page_id) {
                        return $key['access_token'];
                    }
                }
            } catch (\exception $e) {
                $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get token->".$e->getMessage());
            
            }
        }
    }
    public function getAdAccount($config,$fb,$post_id){
        $response="";

        try {
            $token = $config->token;
            $page_id = $config->page_id;
             // Get the \Facebook\GraphNodes\GraphUser object for the current user.
             // If you provided a 'default_access_token', the '{access-token}' is optional.
             $response = $fb->get('/me/adaccounts', $token);
             $this->socialPostLog($config->id,$post_id,$config->platform,"success","get my adaccounts");
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            // When Graph returns an error
            $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get adaccounts->".$e->getMessage());
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get adaccounts->".$e->getMessage());
           
        }
        if($response!= ""){
    
            try {
                $pages = $response->getGraphEdge()->asArray();
                foreach ($pages as $key) {
                   
                        return $key['id'];
                
                }
            } catch (\exception $e) {
                $this->socialPostLog($config->id,$post_id,$config->platform,"error","not get adaccounts id->".$e->getMessage());
            
            }
        }
    }
    
    
    public function history(Request $request)
    {   
        
    	$logs = SocialPostLog::where("post_id", $request->post_id)->where("modal","SocialAdCreative")->orderBy("created_at","desc")->get();
        return response()->json(["code" => 200 , "data" => $logs]);
    }
    public function getPostData($config){
        $token = $config->token;
        $page_id = $config->page_id;
        $url="https://graph.facebook.com/v12.0/$page_id?fields=posts&access_token=$token";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, 0);
        $resp = curl_exec($ch);
        $resp = json_decode($resp, true);
        if(isset($resp["error"])){
            return ["type"=>"error","message"=>$resp["error"]["message"]];
        }else{
            return ["type"=>"success","message"=>$resp];
        }
      

    }
    private function getInstaID($config,$fb,$post_id){
        $token = $config->token;
        $page_id = $config->page_id;
        $url = "https://graph.facebook.com/v12.0/$page_id?fields=instagram_business_account&access_token=$token";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, 0);
        $resp = curl_exec($ch);
        $this->socialPostLog($config->id,$post_id,$config->platform,"response-getInstaID",$resp);
        $resp = json_decode($resp, true);
        if(isset($resp["instagram_business_account"])){
            return $resp["instagram_business_account"]["id"];
        }
        return "";
       
    }
    
    

    
}