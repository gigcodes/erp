<?php

namespace App\Http\Controllers\Social;

use Auth;
use Crypt;
use Session;
use Storage;
use Response;
use App\Setting;
use Carbon\Carbon;
use App\LogRequest;
use App\StoreWebsite;
use Facebook\Facebook;
use App\GoogleTranslate;
use App\Social\SocialPost;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Facebook\PostCreateRequest;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class SocialPostController extends Controller
{
    public function index(Request $request, $id)
    {
        if ($request->number || $request->username || $request->provider || $request->customer_support || $request->customer_support == 0 || $request->term || $request->date) {
            $query = SocialPost::where('config_id', $id)->with('account');
            $posts = $query->orderby('id', 'desc');
        } else {
            $posts = SocialPost::where('config_id', $id)
                ->with('account')
                ->latest()
                ->paginate(Setting::get('pagination'));
        }
        $websites = StoreWebsite::select('id', 'title')->get();

        $posts = $posts->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => $posts->render(),
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

        $websites = StoreWebsite::select('id', 'title')->get();
        $socialconfigs = SocialConfig::get();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social.posts.data', compact('posts'))->render(),
                'links' => (string)$posts->render(),
            ], 200);
        }

        return view('social.posts.grid', compact('posts', 'websites', 'socialconfigs'));
    }

    public function deletePost(Request $request)
    {
        $post = SocialPost::where('ref_post_id', $request['post_id'])->with('account')->first();

        $pageInfoParams = [
            'endpoint_path' => $request['post_id'],
            'fields' => 'message,id,created_time',
            'access_token' => $post->account->page_token,
            'request_type' => 'DELETE',
        ];

        $response = getFacebookResults($pageInfoParams);

        if (isset($response['data']['success']) && $response['data']['success']) {
            $post->delete();

            return Response::json([
                'success' => true,
                'message' => 'Post deleted successfully',
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
     * @return View
     */
    public function create(SocialConfig $id)
    {
        if (isset($id['store_website_id'])) {
            $socialWebsiteAccount = SocialConfig::where('store_website_id', $id['store_website_id'])->get();
        }
        return view('social.posts.create', compact('id', 'socialWebsiteAccount'));
    }

    public function getImage($id)
    {
        try {
            $config = SocialConfig::find($id);

            $website = StoreWebsite::where('id', $config->store_website_id)->first();
            $media = $website->getMedia('website-image-attach');
        } catch (\Exception $e) {
            Session::flash('message', $e);

            \Log::error($e);
        }

        return view('social.posts.attach-images', compact('media'));
    }

    public function store(PostCreateRequest $request)
    {
        $page_config = SocialConfig::where('id', $request->config_id)->first();
        $hashtagsOfUse = '';
        if ($request->has('hashtags')) {
            $hashtags = explode('#', $request->input('hashtags'));
            $finalHashtags = [];
            foreach ($hashtags as $key => $hashtagi) {
                if ($hashtagi) {
                    $googleTranslate = new GoogleTranslate();
                    $target = $page_config->page_language ? $page_config->page_language : 'en';
                    $translationHashtags = $googleTranslate->translate($target, $hashtagi);
                    $finalHashtags[$key] = $translationHashtags;
                }
            }
            $hashtagsOfUse = implode(' #', $finalHashtags);
        }

        $data['message'] = $request->get('message') . ' ' . $hashtagsOfUse;
        if ($request->has('date') && $request->get('date') != null) {
            $data['published'] = false;
            $data['scheduled_publish_time'] = Carbon::parse($request->get('data'))->getTimestamp();
        }

        $pageInfoParams = [
            'endpoint_path' => $page_config->page_id . '/feed',
            'fields' => '',
            'access_token' => $page_config->page_token,
            'request_type' => 'POST',
            'data' => $data,
        ];

        $response = getFacebookResults($pageInfoParams);

        if (isset($response['data']['id'])) {
            $post = new SocialPost;
            $post->config_id = $request->config_id;
            $post->caption = $request->message;
            $post->post_body = $request->message;
            $post->ref_post_id = $response['data']['id'];
            $post->post_by = Auth::user()->id;
            $post->posted_on = $request->has('date') ? Carbon::parse($request->get('data')) : Carbon::now();
            $post->hashtag = $request->hashtags;
            $post->post_medium = 'erp';
            $post->status = 1;
            $post->save();

            Session::flash('message', 'Post created successfully');
            return redirect()->route('social.post.index', ['id' => $request->config_id]);
        } else {
            $post = new SocialPost;
            $post->config_id = $request->config_id;
            $post->caption = $request->message;
            $post->post_body = $request->message;
            $post->post_by = Auth::user()->id;
            $post->hashtag = $request->hashtags;
            $post->post_medium = 'erp';
            $post->status = 3;
            $post->save();

            Session::flash('message', 'Post created successfully');
            return redirect()->back()->withError('Unable to create post');
        }

//        try {
//            $configArray = [];
//            if (isset($request->webpage)) {
//                foreach ($request->webpage as $key => $value) {
//                    $configArray[$key] = $value;
//                }
//                array_push($configArray, $request->config_id);
//            } else {
//                $configArray[0] = $request->config_id;
//            }
//
//            foreach ($configArray as $value) {
//                $config = SocialConfig::find($value);
//                $this->page_access_token = $config->page_token;
//                $message = $request->input('message');
//
//                $googleTranslate = new GoogleTranslate();
//                $target = $config->page_language ? $config->page_language : 'en';
//                $translationString = $googleTranslate->translate($target, $message);
//                $message = $translationString;
//
//                $message = $message . ' ' . $hashtagsOfUse;
//
//                if ($this->page_access_token != '') {
//                    if ($config->platform == 'facebook') {
//                        $post = new SocialPost;
//                        $post->config_id = $request->config_id;
//                        $post->caption = $request->message;
//                        $post->post_body = $request->description;
//                        $post->post_by = Auth::user()->id;
//                        $post->hashtag = $request->hashtags;
//                        $post->save();
//
//                        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'comes to facebook condition');
//                        if ($request->hasFile('source')) {
//                            ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
//                            ini_set('max_execution_time', '-1');
//
//                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to Image upload');
//                            // Description
//                            try {
//                                foreach ($request->file('source') as $key => $source) {
//                                    $media = MediaUploader::fromSource($source)
//                                        ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
//                                        ->upload();
//                                    $post->attachMedia($media, config('constants.media_tags'));
//                                }
//
//                                if ($post->getMedia(config('constants.media_tags'))->first()) {
//                                    ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
//                                    ini_set('max_execution_time', '-1');
//
//                                    $this->socialPostLog($config->id, $post->id, $config->platform, 'come to getMedia', 'find media');
//                                    foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
//                                        $mediaurl = $file->getUrl();
//                                        //$mediaurl = 'https://www.1800flowers.com/blog/wp-content/uploads/2017/03/single-red-rose.jpg';
//                                        $post->posted_on = $request->input('date');
//                                        $post->image_path = $mediaurl;
//                                        $post->status = 2;
//                                        $post->save();
//                                    }
//                                }
//                            } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
//                                \Log::info($e); // handle exception
//                                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
//                            }
//                        }    // Video Case
//                        elseif ($request->hasFile('video1')) {
//                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to video upload');
//                            try {
//                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
//                                ini_set('max_execution_time', '-1');
//                                $access_token = $config->page_token;
//                                $page_id = $config->page_id;
//                                //  $message = $request->input('message');
//                                $media = MediaUploader::fromSource($request->file('video1'))
//                                    ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
//                                    ->upload();
//                                $post->attachMedia($media, config('constants.media_tags'));
//
//                                foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
//                                    $mediaurl = $file->getUrl();
//                                }
//                                $post->posted_on = $request->input('date');
//                                $post->image_path = $mediaurl;
//                                $post->status = 2;
//                                $post->save();
//                            } catch (\Facebook\Exceptions\FacebookResponseException   $e) {
//                                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
//                            }
//                        } elseif (isset($request->image)) {
//                            $access_token = $config->page_token;
//                            $page_id = $config->page_id;
//
//                            $image_upload_url = 'https://graph.facebook.com/' . $page_id . '/photos';
//
//                            foreach ($request->image as $key => $source) {
//                                $fbImage = [
//                                    'access_token' => $access_token,
//                                    //'url' => 'https://i.pinimg.com/736x/0f/36/31/0f3631cab4db579656cfa612cce7dca0.jpg',
//                                    'url' => $source,
//                                    'caption' => $message,
//                                ];
//
//                                $post->posted_on = $request->input('date');
//                                $post->image_path = $source;
//                                $post->status = 2;
//                                $post->save();
//                            }
//                        } // Simple Post Case
//                        else {
//                            $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'Comes to text post');
//
//                            $access_token = $config->page_token;
//                            $page_id = $config->page_id;
//
//                            $pageId = $config->page_id;
//                            $messageText = $data['message'] = $message;
//                            $apiEndpoint = 'https://graph.facebook.com/' . $pageId . '/feed?message=' . urlencode($messageText) . '&access_token=' . $access_token;
//
//                            $post->posted_on = $request->input('date');
//                            $post->status = 2;
//                            $post->save();
//                        }
//                    } else {
//                        $post = new SocialPost;
//                        $post->config_id = $request->config_id;
//                        $post->caption = $request->message;
//                        $post->post_body = $request->description;
//                        $post->post_by = Auth::user()->id;
//                        $post->hashtag = $request->hashtags;
//                        $post->save();
//
//                        $this->socialPostLog($config->id, $post->id, $config->platform, 'message', 'comes to insta condition');
//                        $insta_id = $this->getInstaID($config, $this->fb, $post->id);
//
//                        if ($insta_id != '') {
//                            $this->socialPostLog($config->id, $post->id, $config->platform, 'get-insta-id', $insta_id);
//                            $images = [];
//
//                            if ($request->hasfile('source')) {
//                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
//                                ini_set('max_execution_time', '-1');
//                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to image', 'source');
//                                foreach ($request->file('source') as $image) {
//                                    $media = MediaUploader::fromSource($image)
//                                        ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
//                                        ->upload();
//                                    $post->attachMedia($media, config('constants.media_tags'));
//                                }
//                            }
//
//                            if ($request->hasfile('video1')) {
//                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to video', 'video');
//                                $media = MediaUploader::fromSource($request->file('video1'))
//                                    ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
//                                    ->upload();
//                                $post->attachMedia($media, config('constants.media_tags'));
//                            }
//
//                            if (isset($request['image'])) {
//                                foreach ($request['image'] as $key => $value) {
//                                    $mediaurl = $value;
//                                    $media_id = $this->addMedia($config, $post, $mediaurl, $insta_id, $message);
//
//                                    if (!empty($media_id)) {
//                                        $res = $this->publishMedia($config, $post, $media_id, $insta_id);
//                                    }
//                                    if (!empty($res)) {
//                                        $post->ref_post_id = $res;
//                                        $post->status = 1;
//                                        $post->save();
//                                    }
//                                }
//                            }
//
//                            if ($post->getMedia(config('constants.media_tags'))->first()) {
//                                ini_set('memory_limit', '-1');   // Added memory limit allowing maximum memory
//                                ini_set('max_execution_time', '-1');
//
//                                $this->socialPostLog($config->id, $post->id, $config->platform, 'come to getMedia', 'find media');
//                                foreach ($post->getMedia(config('constants.media_tags')) as $i => $file) {
//                                    $mediaurl = $file->getUrl();
//                                    $media_id = $this->addMedia($config, $post, $mediaurl, $insta_id, $message);
//                                    if (!empty($media_id)) {
//                                        $res = $this->publishMedia($config, $post, $media_id, $insta_id);
//                                    }
//                                    if (!empty($res)) {
//                                        $post->ref_post_id = $res;
//                                        $post->status = 1;
//                                        $post->save();
//                                    }
//                                }
//                            }
//                        }
//                    }
//                } else {
//                    return redirect()->back()->withError('Error in creating post');
//                }
//            }
//        } catch (\Exception $e) {
//            $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e);
//            Session::flash('message', $e);
//            \Log::error($e);
//        }

        return redirect()->route('social.post.index', $config->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
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

        return redirect()->back()->withSuccess('You have successfully changed  Config');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
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
            $page_id = $config->page_id;
            $this->socialPostLog($config->id, $post_id, $config->platform, 'error', 'get token->' . $token);
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
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($resp), $httpcode, \App\Http\Controllers\SocialPostController::class, 'getInstaID');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-getInstaID', $resp);
        $resp = json_decode($resp, true);
        if (isset($resp['instagram_business_account'])) {
            return $resp['instagram_business_account']['id'];
        }

        return '';
    }

    private function addMedia($config, $post, $mediaurl, $insta_id, $message)
    {
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

        LogRequest::log($startTime, $url, 'POST', json_encode($request_params), json_decode($resp), $httpcode, \App\Http\Controllers\SocialPostController::class, 'addMedia');
        $this->socialPostLog($config->id, $post_id, $config->platform, 'response-addMedia', $resp);
        $resp = json_decode($resp, true);
        if (isset($resp['id'])) {
            $this->socialPostLog($config->id, $post_id, $config->platform, 'addMedia', $resp['id']);

            return $resp['id'];
        }

        return '';
    }
}
