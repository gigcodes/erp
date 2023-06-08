<?php

namespace App\Http\Controllers\Social;

use Auth;
use Crypt;
use Session;
use Storage;
use Response;
use App\Setting;
use App\StoreWebsite;
use Facebook\Facebook;
use App\GoogleTranslate;
use App\Social\SocialPost;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use App\Http\Controllers\Controller;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use App\LogRequest;

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

            $posts = $query->orderby('id', 'desc');
        } else {
            $posts = SocialPost::where('config_id', $id)->latest()->paginate(Setting::get('pagination'));
        }
        $websites = \App\StoreWebsite::select('id', 'title')->get();

        $posts = $posts->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string) $posts->render(),
            ], 200);
        }

        return view('social.posts.index', compact('posts', 'websites', 'id'));
    }

    public function translationapproval(Request $request)
    {
        $posts = SocialPost::find($request['post_id']);
        $config = SocialConfig::find($posts['config_id']);
        $data = [];
        $data['post_id'] = $request['post_id'];
        $data['caption'] = $posts['caption'];
        $data['hashtag'] = $posts['hashtag'];

        $googleTranslate = new GoogleTranslate();
        $target = $config->page_language ? $config->page_language : 'en';
        $data['caption_trans'] = $googleTranslate->translate($target, $posts['caption']);
        $data['hashtag_trans'] = $googleTranslate->translate($target, $posts['hashtag']);

        return response()->json(['code' => 200, 'data' => $data]);
    }

    public function approvepost(Request $request)
    {
        $posts = SocialPost::find($request['post_id']);

        $config = SocialConfig::find($posts['config_id']);

        if ($config['platform'] == 'facebook') {
            $access_token = $config['page_token'];
            $page_id = $config['page_id'];
            $image_upload_url = 'https://graph.facebook.com/' . $page_id . '/photos';

            $fbImage = [
                'access_token' => $access_token,
                'url' => $posts['image_path'],
                'caption' => $request['caption_trans'] . ' ' . $request['hashtag_trans'],
            ];

            $response = SocialHelper::curlPostRequest($image_upload_url, $fbImage);
            $response = json_decode($response);
            if (isset($response->error->message)) {
                Session::flash('message', $response->error->message);
            } else {
                $data['status'] = 1;
                if (isset($response->post_id)) {
                    $data['ref_post_id'] = $response->post_id;
                    $data['translation_approved_by'] = Auth::user()->name;
                }
                $posts->fill($data);
                $posts->save();

                return redirect()->back()->withSuccess('You have successfully create a post on social media!!');
            }
        }
    }

    public function grid(Request $request)
    {
        $posts = SocialPost::select('social_posts.*')->join('social_configs as sc', 'sc.id', 'social_posts.config_id')->where('social_posts.status', 1);
        if ($request->social_config) {
            $posts = $posts->whereIn('platform', $request->social_config);
        }

        if ($request->store_website_id) {
            $posts = $posts->join('store_websites as sw', 'sw.id', 'sc.store_website_id')->whereIn('config_id', $request->store_website_id);
        }

        $posts = $posts->orderby('social_posts.id', 'desc')->paginate(Setting::get('pagination'));

        $websites = \App\StoreWebsite::select('id', 'title')->get();
        $socialconfigs = SocialConfig::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string) $posts->render(),
            ], 200);
        }

        return view('social.posts.grid', compact('posts', 'websites', 'socialconfigs'));
    }

    public function viewPost(Request $request, $id)
    {
        try {
            $querys = SocialPost::where('config_id', $id)->where('ref_post_id', '!=', '')->get();
            $config = SocialConfig::find($id);
            $collection = [];
            if ($config['platform'] == 'instagram') {
                $querys = SocialPostLog::where('config_id', $id)->where('log_description', '!=', '')->where('log_title', '=', 'publishMedia')->get();

                foreach ($querys as $key => $query) {
                    $url = sprintf('https://graph.facebook.com/v15.0/' . $query['log_description'] . '?fields=caption,media_type,media_url,thumbnail_url,permalink,timestamp,username&access_token=' . $config['token']);
                    $response = SocialHelper::curlGetRequest($url);
                    if (isset($response->caption)) {
                        $collection[$key]['text'] = $response->caption;
                        $collection[$key]['url'] = $response->media_url;
                        $collection[$key]['message'] = '';
                    }
                }
            } else {
                foreach ($querys as $key => $query) {
                    $url = sprintf('https://graph.facebook.com/v15.0/' . $query['ref_post_id'] . '?fields=attachments&access_token=' . $config['page_token']);
                    $response = SocialHelper::curlGetRequest($url);
                    if (isset($response->attachments)) {
                        $collection[$key]['text'] = $response->attachments->data[0]->description;
                        $collection[$key]['url'] = $response->attachments->data[0]->media->image->src;
                        $collection[$key]['message'] = '';
                    } else {
                        $url = sprintf('https://graph.facebook.com/v15.0/' . $query['ref_post_id'] . '?access_token=' . $config['page_token']);
                        $response = SocialHelper::curlGetRequest($url);
                        $collection[$key]['text'] = '';
                        $collection[$key]['url'] = '';
                        $collection[$key]['message'] = $response->message;
                    }
                }
            }

            return view('social.posts.viewpost', compact('collection'));
        } catch(\Exception $e) {
            $this->socialPostLog($config->id, $config->id, $config->platform, 'error', $e);
            Session::flash('message', $e);
            \Log::error($e);
        }
    }

    public function deletePost(Request $request)
    {
        $query = SocialPost::where('ref_post_id', $request['post_id'])->get();
        $config = SocialConfig::find($query[0]['config_id']);

        $pageAccessToken = $config['page_token'];
        $postId = $request['post_id']; // Replace with the ID of the post you want to delete
        $apiEndpoint = 'https://graph.facebook.com/' . $postId . '?access_token=' . $pageAccessToken;
        $curlSession = curl_init($apiEndpoint);

        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curlSession);
        curl_close($curlSession);

        $responseData = json_decode($response, true);
        if ($responseData['success']) {
            $query->delete();

            return redirect()->back()->withSuccess('Post deleted sucessfully!!');

            return Response::json([
                'success' => true,
                'message' => ' Config Deleted',
            ]);
        } else {
            return false;
        }
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
        $config = SocialConfig::find($id);

        if (isset($config['store_website_id'])) {
            $socialWebsiteAccount = SocialConfig::where('store_website_id', $config['store_website_id'])->get();
        }

        return view('social.posts.create', compact('id', 'socialWebsiteAccount'));
    }

    public function getImage($id)
    {
        try {
            $config = SocialConfig::find($id);

            $website = StoreWebsite::where('id', $config->store_website_id)->first();
            $media = $website->getMedia('website-image-attach');
        } catch(\Exception $e) {
            Session::flash('message', $e);

            \Log::error($e);
        }

        return view('social.posts.attach-images', compact('media'));
    }

    /**
     * Store a newly created resource in storage.
     *
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
            'default_graph_version' => 'v15.0',
        ]);

        $data['caption'] = $post->caption;
        $data['published'] = 'true';
        //$data['access_token']=$config->page_token;
        $pageToken = $this->getPageAccessToken($config, $fb, $post->id);
        //    dd($pageToken);

        $data['message'] = $post->post_body;
        $data['post_body'] = $post->post_body;
        $url = '/' . $config->page_id . '/feed';
        $multiPhotoPost['attached_media'] = [];
        if ($request->file('source')) {
            $file = $request->file('source');

            $name = time() . '.' . $file->extension();

            $file->move(public_path() . '/social_images/', $name);
            $imagePath = public_path() . '/social_images/' . $name;
            $data['source'] = $fb->fileToUpload($imagePath);
            $url = '/' . $config->page_id . '/photos';
            $post->image_path = '/social_images/' . $name;
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
        try {
            $configArray = [];
            if (isset($request->webpage)) {
                foreach ($request->webpage as $key => $value) {
                    $configArray[$key] = $value;
                }
                array_push($configArray, $request->config_id);
            } else {
                $configArray[0] = $request->config_id;
            }

            foreach ($configArray as $value) {
                $config = SocialConfig::find($value);
                $this->page_access_token = $config->page_token;
                $message = $request->input('message');

                $googleTranslate = new GoogleTranslate();
                $target = $config->page_language ? $config->page_language : 'en';
                $translationString = $googleTranslate->translate($target, $message);
                $message = $translationString;
                $hashtagsOfUse = '';
                if (! empty($request->input('hashtags'))) {
                    $hashtags = explode('#', $request->input('hashtags'));
                    $finalHashtags = [];
                    foreach ($hashtags as $key => $hashtagi) {
                        if ($hashtagi) {
                            $googleTranslate = new GoogleTranslate();
                            $target = $config->page_language ? $config->page_language : 'en';
                            $translationHashtags = $googleTranslate->translate($target, $hashtagi);
                            $finalHashtags[$key] = $translationHashtags;
                        }
                    }
                    $hashtagsOfUse = implode(' #', $finalHashtags);
                }

                $message = $message . ' ' . $hashtagsOfUse;

                if ($this->page_access_token != '') {
                    if ($config->platform == 'facebook') {
                        $post = new SocialPost;
                        $post->config_id = $request->config_id;
                        $post->caption = $request->message;
                        $post->post_body = $request->description;
                        $post->post_by = Auth::user()->id;
                        $post->hashtag = $request->hashtags;
                        $post->save();

                        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'comes to facebook condition');
                        if ($request->hasFile('source')) {
                            ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
                            ini_set('max_execution_time', '-1');

                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to Image upload');
                            // Description
                            try {
                                foreach ($request->file('source') as $key => $source) {
                                    $media = MediaUploader::fromSource($source)
                                            ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
                                            ->upload();
                                    $post->attachMedia($media, config('constants.media_tags'));
                                }

                                if ($post->getMedia(config('constants.media_tags'))->first()) {
                                    ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
                                    ini_set('max_execution_time', '-1');

                                    $this->socialPostLog($config->id, $post->id, $config->platform, 'come to getMedia', 'find media');
                                    foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
                                        $mediaurl = $file->getUrl();
                                        //$mediaurl = 'https://www.1800flowers.com/blog/wp-content/uploads/2017/03/single-red-rose.jpg';
                                        $post->posted_on = $request->input('date');
                                        $post->image_path = $mediaurl;
                                        $post->status = 2;
                                        $post->save();
                                    }
                                }
                            } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
                                \Log::info($e); // handle exception
                                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
                            }
                        }	// Video Case
                        elseif ($request->hasFile('video1')) {
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to video upload');
                            try {
                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
                                ini_set('max_execution_time', '-1');
                                $access_token = $config->page_token;
                                $page_id = $config->page_id;
                                //  $message = $request->input('message');
                                $media = MediaUploader::fromSource($request->file('video1'))
                                    ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
                                    ->upload();
                                $post->attachMedia($media, config('constants.media_tags'));

                                foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
                                    $mediaurl = $file->getUrl();
                                }
                                $post->posted_on = $request->input('date');
                                $post->image_path = $mediaurl;
                                $post->status = 2;
                                $post->save();

                                // $uploadUrl = "https://graph-video.facebook.com/v16.0/{$page_id}/videos";
                                // $curl = curl_init($uploadUrl);
                                // curl_setopt($curl, CURLOPT_POST, true);
                                // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                // curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                                //     'file_url' =>  $mediaurl,
                                //     'access_token' => $access_token,
                                //     'description' => $message
                                // ));

                                // // execute the cURL request and handle any errors
                                // $response = curl_exec($curl);
                                // if ($response === false) {
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $response->error->message);
                                // }
                                // $response = json_decode($response);
                                // curl_close($curl);

                                // if(isset($response->id)){
                                //     $post->status = 1;
                                //     $post->ref_post_id = $response->id;
                                //     $post->save();
                                //     Session::flash('message', 'Content Posted successfully');
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                                // }else{
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $response->error->message);
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'error', 'post faild');
                                //     Session::flash('message', $response->error->message);
                                // }
                            } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
                                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
                            }
                        } elseif (isset($request->image)) {
                            $access_token = $config->page_token;
                            $page_id = $config->page_id;

                            $image_upload_url = 'https://graph.facebook.com/' . $page_id . '/photos';

                            foreach ($request->image as $key => $source) {
                                $fbImage = [
                                    'access_token' => $access_token,
                                    //'url' => 'https://i.pinimg.com/736x/0f/36/31/0f3631cab4db579656cfa612cce7dca0.jpg',
                                    'url' => $source,
                                    'caption' => $message,
                                ];

                                $post->posted_on = $request->input('date');
                                $post->image_path = $source;
                                $post->status = 2;
                                $post->save();

                                // $response = SocialHelper::curlPostRequest($image_upload_url,$fbImage);
                                // $response = json_decode($response);

                                // if (isset($response->error->message)) {
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $response->error->message);
                                // }else{

                                //     $post->posted_on = $request->input('date');
                                //     $post->status = 1;
                                //     if (isset($response->post_id)) {
                                //         $post->ref_post_id = $response->post_id;
                                //     }
                                //     $post->save();
                                //     $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                                // }
                            }
                        }
                        // Simple Post Case
                        else {
                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to text post');

                            $access_token = $config->page_token;
                            $page_id = $config->page_id;

                            $pageId = $config->page_id;
                            $messageText = $data['message'] = $message;
                            $apiEndpoint = 'https://graph.facebook.com/' . $pageId . '/feed?message=' . urlencode($messageText) . '&access_token=' . $access_token;

                            $post->posted_on = $request->input('date');
                            $post->status = 2;
                            $post->save();

                            // $curlSession = curl_init($apiEndpoint);

                            // curl_setopt($curlSession, CURLOPT_POST, true);
                            // curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

                            // $response = curl_exec($curlSession);
                            // curl_close($curlSession);

                            // $responseData = json_decode($response, true);

                            // if (isset($responseData->error->message)) {
                            //     Session::flash('message', $responseData->error->message);
                            // } else {

                            //     if (isset($responseData['id'])) {
                            //         $post->status = 1;
                            //         $post->ref_post_id = $responseData['id'];
                            //     }

                            //     $post->save();
                            //     Session::flash('message', 'Content Posted successfully');
                            //     $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                            // }
                        }
                    } else {
                        $post = new SocialPost;
                        $post->config_id = $request->config_id;
                        $post->caption = $request->message;
                        $post->post_body = $request->description;
                        $post->post_by = Auth::user()->id;
                        $post->hashtag = $request->hashtags;
                        $post->save();

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
                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
                                ini_set('max_execution_time', '-1');
                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to image', 'source');
                                foreach ($request->file('source') as $image) {
                                    $media = MediaUploader::fromSource($image)
                                        ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
                                        ->upload();
                                    $post->attachMedia($media, config('constants.media_tags'));
                                }
                            }

                            if ($request->hasfile('video1')) {
                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to video', 'video');
                                $media = MediaUploader::fromSource($request->file('video1'))
                                    ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
                                    ->upload();
                                $post->attachMedia($media, config('constants.media_tags'));
                            }

                            if (isset($request['image'])) {
                                foreach ($request['image'] as $key => $value) {
                                    $mediaurl = $value;
                                    //$mediaurl="https://th-thumbnailer.cdn-si-edu.com/i_y5C_IJJg3PLZUKzJ15hJt-C1E=/1072x720/filters:no_upscale()/https://tf-cmsv2-smithsonianmag-media.s3.amazonaws.com/filer/a9/ff/a9ff31d0-aecd-464e-80c7-873e4651cd2b/mufasa.jpeg";
                                    $media_id = $this->addMedia($config, $post, $mediaurl, $insta_id, $message);

                                    if (! empty($media_id)) {
                                        $res = $this->publishMedia($config, $post, $media_id, $insta_id);
                                    }
                                    if (! empty($res)) {
                                        $post->ref_post_id = $res;
                                        $post->status = 1;
                                        $post->save();
                                    }
                                }
                            }

                            if ($post->getMedia(config('constants.media_tags'))->first()) {
                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
                                ini_set('max_execution_time', '-1');

                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to getMedia', 'find media');
                                foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
                                    $mediaurl = $file->getUrl();
                                    // $mediaurl = 'https://www.1800flowers.com/blog/wp-content/uploads/2017/03/single-red-rose.jpg';

                                    // $mediaurl="https://thumbs.dreamstime.com/b/red-rose-4590099.jpg";
                                    $media_id = $this->addMedia($config, $post, $mediaurl, $insta_id, $message);
                                    if (! empty($media_id)) {
                                        $res = $this->publishMedia($config, $post, $media_id, $insta_id);
                                    }
                                    if (! empty($res)) {
                                        $post->ref_post_id = $res;
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
            }

            //$this->page_access_token = $this->getPageAccessToken($config, $this->fb, $post->id);
            // $this->page_access_token = $config->page_token;
            // $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'get page access token');
            // $request->validate([
            // 	'message' => 'required',
            // 	'source.*' => 'mimes:jpeg,bmp,png,gif,tiff,jpg',
            // //	'video' =>'mimes:3g2,3gp,3gpp,asf,avi,dat,divx,dv,f4v,flv,gif,m2ts,m4v,mkv,mod,mov,mp4,mpe, mpeg,mpeg4,mpg,mts,nsv,ogm,ogv,qt,tod,tsvob,wmv',

            // ]);

            // Message

            // $message = $request->input('message');
            // $message = $message . ' ' . $request->input('hashtags');
        } catch(\Exception $e) {
            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
            Session::flash('message', $e);

            \Log::error($e);
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
        $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'get token function call..');
        $response = '';

        try {
            $token = $config->token;

            // 'EAAIALK1F98IBAEOcpbWHkBG2KyDfaqNoFWpgvxBw9k5wWn2RiQYXlsQFhzoQYHp9ZCwxjuM3Y3IMKKyGVYIvn2WM3bTGBxNbRR18OtbTtJFH2kZBsZCmPDMnZBuK8QkGQbrhdKrjLYAZB1y8WNRd5CtdnoJfv6Mvk4p5fLbZAd9CbbaaBc44espdHp2obEpxdIPPhB8QoqXXD7D3TwxypmOSFLTlzcOvqdUGuqyHZA5qAZDZD';
            $page_id = $config->page_id;
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'get token->' . $token);
            //$response = $fb->get('/me/accounts', $token);

            $url = sprintf('https://graph.facebook.com/v15.0//me/accounts?access_token=' . $token);
            $response = SocialHelper::curlGetRequest($url);

            $this->socialPostLog($config->id, $post_id, $config->platform, 'success', 'get my accounts');
        } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
            // When Graph returns an error
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->' . $e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get accounts->' . $e->getMessage());
        }

        if ($response != '') {
            try {
                foreach ($response->data as $key => $value) {
                    if (isset($value->id)) {
                        if ($value->id == $page_id) {
                            $this->socialPostLog($config->id, $post_id, $config->platform, 'success', 'get account details');

                            return $value->access_token;
                        }
                    }
                }

                // foreach ($pages as $val) {
                //     if ($val['id'] == $page_id) {
                //         return $val['access_token'];
                //     }
                // }
            } catch (\Exception $e) {
                $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'not get token->' . $e->getMessage());
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
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = "https://graph.facebook.com/v15.0/$page_id?fields=instagram_business_account&access_token=$token";
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
        LogRequest::log($startTime, $url, 'GET', json_encode([]), $resp, $httpcode, \App\Http\Controllers\SocialPostController::class, 'getInstaID');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-getInstaID', $resp);
        $resp = json_decode($resp, true);
        if (isset($resp['instagram_business_account'])) {
            return $resp['instagram_business_account']['id'];
        }

        return '';
    }

    private function addMedia($config, $post, $mediaurl, $insta_id, $message)
    {
        //$mediaurl = 'https://t3.gstatic.com/licensed-image?q=tbn:ANd9GcSJ8o7X29SK1xD2JsVcP2_A0E8ZDGWV3ib5es32LHnzHQ3gu5_p9bReGNF9nxf39k-4Lumy6iEFjkQbgJg';
        $token = $config->token;
        $page_id = $config->page_id;
        $post_id = $post->id;
        $caption = $message;
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $postfields = "image_url=$mediaurl&caption=$caption&access_token=$token";
        $url = "https://graph.facebook.com/v15.0/$insta_id/media";

        $request_params = [
            'access_token' => $token,
            'image_url' => $mediaurl,
            'caption' => $caption,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        // $resp = curl_exec($ch);

        LogRequest::log($startTime, $url, 'POST', json_encode($request_params), $resp, $httpcode, \App\Http\Controllers\SocialPostController::class, 'addMedia');
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
        $request_params = [
            'access_token' => $token,
            'creation_id' => $media_id,
            'caption' => $caption,
        ];

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
        LogRequest::log($startTime, $url, 'GET', json_encode($request_params), $resp, $httpcode, \App\Http\Controllers\SocialPostController::class, 'publishMedia');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response publishMedia', $resp);
        $resp = json_decode($resp, true);

        if (isset($resp['id'])) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'publishMedia', $resp['id']);

            return $resp['id'];
        }

        return '';
    }
}
