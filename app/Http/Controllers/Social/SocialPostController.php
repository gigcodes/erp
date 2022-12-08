<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Social\SocialConfig;
use App\Social\SocialPost;
use App\Social\SocialPostLog;
use Auth;
use Crypt;
use Facebook\Facebook;
use Illuminate\Http\Request;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Response;
use Session;

class SocialPostController extends Controller
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

    public function index(Request $request, $id)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $query = SocialPost::where('config_id', $id);

            $posts = $query->orderby('id', 'desc')->paginate(Setting::get('pagination'));
        } else {
            $posts = SocialPost::where('config_id', $id)->latest()->paginate(Setting::get('pagination'));
        }
        $websites = \App\StoreWebsite::select('id', 'title')->get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string) $posts->render(),
            ], 200);
        }

        return view('social.posts.index', compact('posts', 'websites', 'id'));
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log = new SocialPostLog();
        $Log->config_id = $config_id;
        $Log->post_id = $post_id;
        $Log->platform = $platform;
        $Log->log_title = $title;
        $Log->log_description = $description;
        $Log->modal = 'SocialPost';
        $Log->save();

        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('social.posts.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store1(Request $request)
    {
        $post = new SocialPost;
        $post->config_id = $request->config_id;
        $post->caption = $request->caption;
        $post->post_body = $request->post_body;
        $post->post_by = Auth::user()->id;
        $post->save();

        $config = SocialConfig::find($post->config_id);
        $fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' => $config->api_secret,
            'default_graph_version' => 'v12.0',
        ]);

        $data['caption'] = $post->caption;
        $data['published'] = 'true';
        //$data['access_token']=$config->page_token;
        $pageToken = $this->getPageAccessToken($config, $fb, $post->id);
        //    dd($pageToken);

        $data['message'] = $post->post_body;
        $data['post_body'] = $post->post_body;
        $url = '/'.$config->page_id.'/feed';
        $multiPhotoPost['attached_media'] = [];
        if ($request->file('source')) {
            $file = $request->file('source');

            $name = time().'.'.$file->extension();

            $file->move(public_path().'/social_images/', $name);
            $imagePath = public_path().'/social_images/'.$name;
            $data['source'] = $fb->fileToUpload($imagePath);
            $url = '/'.$config->page_id.'/photos';
            $post->image_path = '/social_images/'.$name;
            $post->save();
        }
        try {
            $response = $fb->post($url, $data, $pageToken)->getGraphNode()->asArray();
            \Log::info($response);
            if ($response['id']) {
                $post->status = 1;
                if (isset($response['post_id'])) {
                    $post->ref_post_id = $response['post_id'];
                }

                $post->save();

                return redirect()->back()->withSuccess('You have successfully stored posts.');
            }
        } catch (FacebookSDKException $e) {
            \Log::info($e); // handle exception

            return redirect()->back()->withError('Error in creating post');
        }
    }

    public function store(Request $request)
    {
        $post = new SocialPost;
        $post->config_id = $request->config_id;
        $post->caption = $request->message;
        $post->post_body = $request->description;
        $post->post_by = Auth::user()->id;
        $post->save();

        $config = SocialConfig::find($post->config_id);

        $this->fb = new Facebook([
            'app_id' => $config->api_key,
            'app_secret' => $config->api_secret,
            'default_graph_version' => 'v12.0',
        ]);
        $this->page_access_token = $this->getPageAccessToken($config, $this->fb, $post->id);
        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'get page access token');

        // $request->validate([
        // 	'message' => 'required',
        // 	'source.*' => 'mimes:jpeg,bmp,png,gif,tiff,jpg',
        // //	'video' =>'mimes:3g2,3gp,3gpp,asf,avi,dat,divx,dv,f4v,flv,gif,m2ts,m4v,mkv,mod,mov,mp4,mpe, mpeg,mpeg4,mpg,mts,nsv,ogm,ogv,qt,tod,tsvob,wmv',

        // ]);

        // Message
        $message = $request->input('message');
        if ($this->page_access_token != '') {
            if ($config->platform == 'facebook') {
                $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'comes to facebook condition');
//            dd("sss");
                if ($request->hasFile('source')) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to Image upload');
                    //	dd("Ddddd");
                    // Description
                    $data['caption'] = ($request->input('description')) ? $request->input('description') : '';
                    $data['published'] = 'false';
                    $data['access_token'] = $this->page_access_token;
                    try {
                        foreach ($request->file('source') as $key => $source) {
                            $data['source'] = $this->fb->fileToUpload($source);

                            // post multi-photo story
                            $multiPhotoPost['attached_media['.$key.']'] = '{"media_fbid":"'.$this->fb->post('/me/photos', $data)->getGraphNode()->asArray()['id'].'"}';
                        }

                        // Uploading Multi story facebook photo
                        $multiPhotoPost['access_token'] = $this->page_access_token;
                        $multiPhotoPost['message'] = $message;
                        if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                            $post->posted_on = $request->input('date');
                            $post->save();

                            $multiPhotoPost['published'] = 'false';
                            $multiPhotoPost['scheduled_publish_time'] = strtotime($request->input('date'));
                        }
                        $resp = $this->fb->post('/me/feed', $multiPhotoPost)->getGraphNode()->asArray();

                        if (isset($resp->error->message)) {
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $resp->error->message);
                            Session::flash('message', $resp->error->message);
                        } else {
                            $post->status = 1;
                            if (isset($resp['post_id'])) {
                                $post->ref_post_id = $resp['post_id'];
                            }

                            $post->save();
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                            Session::flash('message', 'Content Posted successfully');
                        }
                    } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
                        \Log::info($e); // handle exception
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
                    }
                }	// Video Case
                elseif ($request->hasFile('video1')) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to video upload');
                    try {
                        $data['title'] = ''.trim($message).'';

                        $data['description'] = ''.trim($request->input('description')).'';

                        $data['source'] = $this->fb->videoToUpload(''.trim($request->file('video1')).'');

                        if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                            $post->posted_on = $request->input('date');
                            $post->save();
                            $data['published'] = 'false';
                            $data['scheduled_publish_time'] = strtotime($request->input('date'));
                        }
                        $resp = $this->fb->post('/me/videos', $data, $this->page_access_token)->getGraphNode()->asArray()['id'];

                        if (isset($resp->error->message)) {
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', 'post faild');
                            Session::flash('message', $resp->error->message);
                        } else {
                            $post->status = 1;
                            if (isset($resp['post_id'])) {
                                $post->ref_post_id = $resp['post_id'];
                            }

                            $post->save();
                            Session::flash('message', 'Content Posted successfully');
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                        }
                    } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
                    }
                }
                // Simple Post Case
                else {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to text post');

                    $data['description'] = $request->input('description');
                    $data['message'] = $message;
                    $data['access_token'] = $this->page_access_token;
                    if ($request->has('date') && $request->input('date') > date('Y-m-d')) {
                        $post->posted_on = $request->input('date');
                        $post->save();
                        $data['published'] = 'true';
                        $data['scheduled_publish_time'] = strtotime($request->input('date'));
                    }
                    try {
                        $resp = $this->fb->post('/me/feed', $data)->getGraphNode()->asArray();

                        if (isset($resp->error->message)) {
                            Session::flash('message', $resp->error->message);
                        } else {
                            $post->status = 1;
                            if (isset($resp['post_id'])) {
                                $post->ref_post_id = $resp['post_id'];
                            }

                            $post->save();
                            Session::flash('message', 'Content Posted successfully');
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                        }
                    } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
                        // handle exception
                    }
                }
            } else {
                $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'comes to insta condition');
                $insta_id = $this->getInstaID($config, $this->fb, $post->id);
                if ($insta_id != '') {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'get-insta-id', $insta_id);
                    $images = [];

                    /*foreach($request->file('source') as $key =>$source)
                      {
                          dd($source);
                          $filename = str_random(40).'_'.$source[0]->getClientOriginalName();

                            $source->move(public_path().'/social_images/', $filename);
                //        $path = $request->files[$key]->store('social_images');
                          $path =  asset('social_images/'.$filename);
                           $media_id = $this->addMedia($config,$post,$path,$insta_id);

                          dd($path);


                      }*/

                    if ($request->hasfile('source')) {
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'come to image', 'source');
                        foreach ($request->file('source') as $image) {
                            $media = MediaUploader::fromSource($image)
                                ->toDirectory('social_images/'.floor($post->id / config('constants.image_per_folder')))
                                ->upload();
                            $post->attachMedia($media, config('constants.media_tags'));
                        }
                    }
                    if ($request->hasfile('video1')) {
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'come to video', 'video');
                        $media = MediaUploader::fromSource($request->file('video1'))
                            ->toDirectory('social_images/'.floor($post->id / config('constants.image_per_folder')))
                            ->upload();
                        $post->attachMedia($media, config('constants.media_tags'));
                    }

                    if ($post->getMedia(config('constants.media_tags'))->first()) {
                        $this->socialPostLog($config->id, $post->id, $config->platform, 'come to getMedia', 'find media');
                        foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
                            $mediaurl = $file->getUrl();
                            $media_id = $this->addMedia($config, $post, $mediaurl, $insta_id);
                            if (! empty($media_id)) {
                                $res = $this->publishMedia($config, $post, $media_id, $insta_id);
                            }
                            if (! empty($res)) {
                                $post->status = 1;
                                $post->save();
                            }
                        }
                    }

                    //    $mediaurl="https://images.unsplash.com/photo-1550330562-b055aa030d73?ixlib=rb-1.2.1";
                }
            }
        } else {
            return redirect()->back()->withError('Error in creating post');
        }

        return redirect()->route('social.post.index', $config->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialPost  $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function show(SocialPost $SocialPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialPost  $SocialPost
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SocialPost  $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialPost $SocialPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialPost  $SocialPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $config = SocialPost::findorfail($request->id);
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
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->'.$e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->'.$e->getMessage());
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
                $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get token->'.$e->getMessage());
            }
        }
    }

    public function history(Request $request)
    {
        $logs = SocialPostLog::where('post_id', $request->post_id)->where('modal', 'SocialPost')->orderBy('created_at', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }

    private function getInstaID($config, $fb, $post_id)
    {
        $token = $config->token;
        $page_id = $config->page_id;
        $url = "https://graph.facebook.com/v12.0/$page_id?fields=instagram_business_account&access_token=$token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 0);
        $resp = curl_exec($ch);
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
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response publishMedia', $resp);
        $resp = json_decode($resp, true);

        if (isset($resp['id'])) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'publishMedia', $resp['id']);

            return $resp['id'];
        }

        return '';
    }
}
