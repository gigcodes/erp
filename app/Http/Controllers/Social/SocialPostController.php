<?php

namespace App\Http\Controllers\Social;

use Auth;
use Crypt;
use Session;
use Response;
use App\Setting;
use Carbon\Carbon;
use App\LogRequest;
use App\StoreWebsite;
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
                'links' => (string) $posts->render(),
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

    /*
     * @todo Video upload functionality has to be added
     * Move the posting strategy to queue
     */
    public function store(PostCreateRequest $request)
    {
        $added_media = [];
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

        $post = SocialPost::create([
            'config_id' => $request->config_id,
            'caption' => $request->message,
            'post_body' => $request->message,
            'post_by' => Auth::user()->id,
            'posted_on' => $request->has('date') ? Carbon::parse($request->get('data')) : Carbon::now(),
            'hashtag' => $request->hashtags,
            'post_medium' => 'erp',
            'status' => 2,
        ]);

        if ($request->has('source')) {
            $files = $request->file('source');

            foreach ($files as $file) {
                $media = MediaUploader::fromSource($file)
                    ->toDirectory('social_images/' . floor($post->id / config('constants.image_per_folder')))
                    ->toDisk('s3')->upload();
                $post->attachMedia($media, config('constants.media_tags'));
                $pageInfoParams = [
                    'endpoint_path' => $page_config->page_id . '/photos',
                    'fields' => '',
                    'access_token' => $page_config->page_token,
                    'request_type' => 'POST',
                    'data' => [
                        'url' => $media->getUrl(),
                        'published' => false,
                    ],
                ];

                $response = getFacebookResults($pageInfoParams);
                $added_media[] = ['media_fbid' => $response['data']['id']];
            }

            $data['attached_media'] = $added_media;
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
            $post->ref_post_id = $response['data']['id'];
            $post->status = 1;
            $post->save();
            $this->socialPostLog($post->config_id, $post->id, $page_config->platform, 'fb_post', $request->message);
            Session::flash('message', 'Post created successfully');

            return redirect()->route('social.post.index', ['id' => $request->config_id]);
        } else {
            $post->status = 3;
            $post->save();
            Session::flash('message', 'Post created successfully');

            return redirect()->back()->withError('Unable to create post');
        }

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
