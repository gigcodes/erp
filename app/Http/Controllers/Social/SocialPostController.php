<?php

namespace App\Http\Controllers\Social;

use App\Social\SocialPost;
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




class SocialPostController extends Controller
{
    /**2
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$id)
    {
       
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {

            $query = SocialPost::where('config_id',$id);


            $posts = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));

        } else {
            $posts = SocialPost::where('config_id',$id)->latest()->paginate(Setting::get('pagination'));
        }
        $websites = \App\StoreWebsite::select('id','title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string)$posts->render()
            ], 200);
        }
     

        return view('social.posts.index',compact('posts', 'websites','id'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function create($id) {
        return view('social.posts.create',compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        

        $post =  new SocialPost;
        $post->config_id = $request->config_id;
        $post->caption = $request->caption;
        $post->post_body = $request->post_body;
        $post->post_by = Auth::user()->id;
        $post->save();

        $config =SocialConfig::find($post->config_id );
        $fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' =>  $config->api_secret,
            'default_graph_version' => 'v12.0',
            ]);

            $data['caption']= $post->caption;
			$data['published']="true";
			//$data['access_token']=$config->page_token;
            $pageToken =$this->getPageAccessToken($config,$fb);
        //    dd($pageToken);
		
         	$data['message']=$post->post_body;
             $data['post_body']=$post->post_body;
            $url = '/'.$config->page_id.'/feed';
            $multiPhotoPost['attached_media']=[];
            if($request->file('source')){
                $file=$request->file('source');

                $name=time().'.'.$file->extension();

                $file->move(public_path().'/social_images/', $name); 
                $imagePath = public_path().'/social_images/'. $name;
                $data['source']=$fb->fileToUpload($imagePath);
                $url = '/'.$config->page_id.'/photos';
                $post->image_path = '/social_images/'. $name;
                $post->save();
            }
              try {
                    $response = $fb->post($url,$data,$pageToken)->getGraphNode()->asArray();
                    \Log::info($response);
                    if($response['id']){
                        $post->status=1;
                        if(isset($response['post_id'])){
                            $post->ref_post_id=$response['post_id'];
                        }

                        $post->save();
                        return redirect()->back()->withSuccess('You have successfully stored posts.');
                    }

                } catch (FacebookSDKException $e) {
                    \Log::info($e); // handle exception
                    return redirect()->back()->withError('Error in creating post');
                    
                }
        
			



       
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SocialPost $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function show(SocialPost $SocialPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SocialPost $SocialPost
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
        $config = SocialPost::findorfail($request->id);
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
     * @param \App\SocialPost $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialPost $SocialPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SocialPost $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialPost::findorfail($request->id);
        $config->delete();
        return Response::json(array(
            'success' => true,
            'message' => ' Config Deleted'
        ));
    }
    public function getPageAccessToken($config,$fb){

        try {
            $token = $config->token;
            $page_id = $config->page_id;
             // Get the \Facebook\GraphNodes\GraphUser object for the current user.
             // If you provided a 'default_access_token', the '{access-token}' is optional.
             $response = $fb->get('/me/accounts', $token);
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    
        try {
            $pages = $response->getGraphEdge()->asArray();
            foreach ($pages as $key) {
                if ($key['id'] == $page_id) {
                    return $key['access_token'];
                }
            }
        } catch (FacebookSDKException $e) {
            dd($e); // handle exception
        }
    }
    
}
