<?php

namespace App\Http\Controllers;

use App\Post;
use App\Account;
use App\LogRequest;
use UnsplashSearch;
use App\ChatMessage;
use App\InstagramPosts;
use Plank\Mediable\Media;
use App\StoreSocialContent;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Jobs\InstaSchedulePost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class InstagramPostsController extends Controller
{
    public function index(Request $request)
    {
    }

    public function post(Request $request)
    {
        $images = $request->get('images', false);
        $mediaIds = $request->get('media_ids', false);

        $productArr = null;
        if ($images) {
            $productIdsArr = \DB::table('mediables')
                ->whereIn('media_id', json_decode($images))
                ->where('mediable_type', 'App\Product')
                ->pluck('mediable_id')
                ->toArray();

            if (! empty($productIdsArr)) {
                $productArr = \App\Product::select('id', 'name', 'sku', 'brand')->whereIn('id', $productIdsArr)->get();
            }
        }

        $mediaIdsArr = null;
        if ($mediaIds) {
            $mediaIdsArr = \DB::table('mediables')
                ->whereIn('media_id', explode(',', $mediaIds))
                ->where('mediable_type', 'App\StoreWebsite')
                ->get();
        }
        $accounts = \App\Account::where('platform', 'instagram')->where('status', 1)->get();

        $query = Post::query();

        if ($request->acc) {
            $query = $query->where('id', $request->acc);
        }
        if ($request->comm) {
            $query = $query->where('comment', 'LIKE', '%' . $request->comm . '%');
        }
        if ($request->tags) {
            $query = $query->where('hashtags', 'LIKE', '%' . $request->tags . '%');
        }
        if ($request->loc) {
            $query = $query->where('location', 'LIKE', '%' . $request->loc . '%');
        }
        if ($request->select_date) {
            $query = $query->whereDate('created_at', $request->select_date);
        }
        $posts = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));

        $used_space = 0;
        $storage_limit = 0;
        $contents = StoreSocialContent::query();
        $contents = $contents->get();
        $records = [];
        foreach ($contents as $site) {
            if ($site) {
                if ($site->hasMedia(config('constants.media_tags'))) {
                    foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                        $records[] = [
                            'id' => $media->id,
                            'extension' => strtolower($media->extension),
                            'file_name' => $media->filename,
                            'mime_type' => $media->mime_type,
                            'size' => $media->size,
                            'thumb' => $media->getUrl(),
                            'original' => $media->getUrl(),
                        ];
                    }
                }
            }
        }

        $imagesHtml = '';
        if (isset($productArr) && count($productArr)) {
            foreach ($productArr as $product) {
                foreach ($product->media as $media) {
                    $imagesHtml .= '<div class="media-file">    <label class="imagecheck m-1">        <input name="media[]" type="checkbox" value="' . $media->id . '" data-original="' . $media->getUrl() . '" class="imagecheck-input">        <figure class="imagecheck-figure">            <img src="' . $media->getUrl() . '" alt="' . $product->name . '" class="imagecheck-image" style="cursor: default;">        </figure>    </label><p style="font-size: 11px;"></p></div>';
                }
            }
        }

        if (isset($mediaIdsArr) && ! empty($mediaIdsArr)) {
            foreach ($mediaIdsArr as $image) {
                $media = Media::where('id', $image->media_id)->get();
                if (! empty($media)) {
                    $imagesHtml .= '<div class="media-file">    <label class="imagecheck m-1">        <input name="media[]" type="checkbox" value="' . $media[0]->getkey() . '" data-original="' . $media[0]->getUrl() . '" class="imagecheck-input">        <figure class="imagecheck-figure">            <img src="' . $media[0]->getUrl() . '" alt="Images" class="imagecheck-image" style="cursor: default;">        </figure>    </label><p style="font-size: 11px;"></p></div>';
                }
            }
        }

        return view('instagram.post.create', compact('accounts', 'records', 'used_space', 'storage_limit', 'posts', 'imagesHtml'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function createPost(Request $request)
    {
        //resizing media
        $all = $request->all();

        if ($request->media) {
            foreach ($request->media as $media) {
                $mediaFile = Media::where('id', $media)->first();
                $image = self::resize_image_crop($mediaFile, 640, 640);
            }
        }

        if ($request->postId) {
            $userPost = InstagramPosts::find($request->postId);
            foreach ($userPost->getMedia('instagram') as $media) {
                $image = self::resize_image_crop($media, 640, 640);
                $mediaPost = $media->id;
                break;
            }
        }

        if (! isset($mediaPost)) {
            $mediaPost = $request->media;
        }

        if (empty($request->location)) {
            $location = '';
        } else {
            $location = $request->location;
        }

        if (empty($request->hashtags)) {
            $hashtag = '';
        } else {
            $hashtag = $request->hashtags;
        }

        $post = new Post();
        $post->account_id = $request->account;
        $post->type = $request->type;
        $post->caption = $request->caption . ' ' . $hashtag;
        $ig = [
            'media' => $mediaPost,
            'location' => $location,
        ];
        $post->ig = json_encode($ig);
        $post->location = $location;
        $post->hashtags = $hashtag;
        $post->scheduled_at = $request->scheduled_at;
        $post->save();
        $newPost = Post::find($post->id);

        $media = json_decode($newPost->ig, true);

        $ig = [
            'media' => $media['media'],
            'location' => $location,
            'hashtag' => $hashtag,
        ];
        $newPost->ig = $ig;

        if ($request->scheduled === '1') {
            $diff = strtotime($request->scheduled_at) - strtotime(now());
            InstaSchedulePost::dispatch($newPost)->onQueue('InstaSchedulePost')->delay($diff);

            return redirect()->back()->with('message', __('Your post schedule has been saved'));
        }

        // Publish Post on instagram
        if (new PublishPost($newPost)) {
            $this->createPostLog($newPost->id, 'success', 'Your post has been published');

            if ($request->ajax()) {
                return response()->json('Your post has been published', 200);
            } else {
                return redirect()->route('post.index')
                    ->with('success', __('Your post has been published'));
            }
        } else {
            $this->createPostLog($newPost->id, 'error', 'Post failed to published');
            if ($request->ajax()) {
                return response()->json('Post failed to published', 200);
            } else {
                return redirect()->route('post.index')
                    ->with('error', __('Post failed to published'));
            }
        }
    }

    public function publishPost(Request $request, $id)
    {
    }

    public function grid(Request $request)
    {
    }

    /**
     * @SWG\Post(
     *   path="/instagram/post",
     *   tags={"Instagram"},
     *   summary="post instagram",
     *   operationId="post-instagram",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function apiPost(Request $request)
    {
    }

    /**
     * @SWG\Get(
     *   path="/instagram/send-account/{token}",
     *   tags={"Instagram"},
     *   summary="get instagram account details",
     *   operationId="get-instagram-account-details",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendAccount($token)
    {
    }

    /**
     * @SWG\Get(
     *   path="/instagram/get-comments-list/{username}",
     *   tags={"Instagram"},
     *   summary="get instagram comments list",
     *   operationId="get-instagram-comment-list",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getComments($username)
    {
    }

    /**
     * @SWG\Post(
     *   path="/instagram/comment-sent",
     *   tags={"Instagram"},
     *   summary="send instagram comments",
     *   operationId="send-instagram-comment",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function commentSent(Request $request)
    {
    }

    /**
     * @SWG\Get(
     *   path="/instagram/get-hashtag-list",
     *   tags={"Instagram"},
     *   summary="Get instagram hashtag list",
     *   operationId="get-instagram-hashtag-list",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getHashtagList()
    {
    }

    /**
     * @SWG\Post(
     *   path="/local/instagram-post",
     *   tags={"Local"},
     *   summary="Save Local instagram post",
     *   operationId="save-local-instagram-post",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function saveFromLocal(Request $request)
    {
    }

    public function viewPost(Request $request)
    {
        $accounts = Account::where('platform', 'instagram')->whereNotNull('proxy')->get();

        $data = Post::whereNotNull('id')->paginate(10);

        return view('instagram.post.index', compact(
            'accounts',
            'data'
        ));
    }

    public function users(Request $request)
    {
    }

    /**
     * @SWG\Get(
     *   path="/local/instagram-user-post",
     *   tags={"Local"},
     *   summary="Get Local instagram user post",
     *   operationId="get-local-instagram-user-post",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function getUserForLocal()
    {
    }

    public function instagramUserLogs()
    {
    }

    public function userPost($id)
    {
    }

    public function resizeToRatio()
    {
    }

    public function resize_image_crop($image, $width, $height)
    {
    }

    public function hashtag(Request $request, $word)
    {
        if (strlen($word) >= 3) {
            $url = sprintf('https://api.ritekit.com/v1/stats/auto-hashtag?post=' . $word . '&maxHashtags=50&hashtagPosition=auto?&client_id=7b3d825c32da1a4eb611bf1eba9706165cfe61a098ae');
            $response = SocialHelper::curlGetRequest($url);

            return $response;
            if ($response->post) {
                return $response->post;
            } else {
                return false;
            }
        }
    }

    public function getHastagifyApiToken()
    {
        $token = \Session()->get('hastagify');
        if ($token) {
            return $token;
        } else {
            $consumerKey = env('HASTAGIFY_CONSUMER_KEY');
            $consumerSecret = env('HASTAGIFY_CONSUMER_SECRET');

            \Log::error(' hashtagify credentials: ' . $consumerKey . ', ' . $consumerSecret);
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);

            $data = [
                'grant_type' => 'client_credentials',
                'client_id' => $consumerKey,
                'client_secret' => $consumerSecret,
            ];
            $url = 'https://api.hashtagify.me/oauth/token';

            $response = Http::post($url, $data)->withHeaders([
                'cache-control' => 'no-cache',
            ]);

            $responseData = $response->json();

            LogRequest::log($startTime, $url, 'POST', json_encode('grant_type=client_credentials&client_id=' . $consumerKey . '&client_secret=' . $consumerSecret),
                $responseData,
                $response->status(),
                InstagramPostsController::class, 'getHastagifyApiToken');

            if ($response->failed) {
                Log::error(' hashtagify response ' . $response);
                Log::error(' hashtagify error' . $response->body);
            } else {
                \Session()->put('hastagify', $responseData['access_token']);

                return $responseData['access_token'];
            }
        }
    }

    public function updateHashtagPost(Request $request)
    {
    }

    public function getImages(Request $request)
    {
        if ($request->type == 'user') {
            $number = rand(1, 500);
            $response = UnsplashSearch::users($request->keyword, ['page' => $number]);
            $content = $response->getContents();
            $lists = json_decode($content);
            $images = [];
            foreach ($lists->results as $list) {
                $images[] = $list->urls->full;
            }

            return $images ? $images : null;
        } elseif ($request->type == 'collection') {
            $number = rand(1, 500);
            $response = UnsplashSearch::collections($request->keyword, ['page' => $number]);
            $content = $response->getContents();
            $lists = json_decode($content);

            $images = [];
            foreach ($lists->results as $list) {
                $images[] = $list->cover_photo->urls->full;
            }

            return $images ? $images : null;
        } else {
            $number = rand(1, 500);
            $response = UnsplashSearch::photos($request->keyword, ['page' => $number, 'order_by' => 'latest']);
            $content = $response->getContents();
            $lists = json_decode($content);

            $images = [];
            foreach ($lists->results as $list) {
                $images[] = $list->urls->full;
            }

            return $images ? $images : null;
        }
    }

    public function getCaptions()
    {
        $captionArray = [];

        $captions = \App\Caption::all();

        foreach ($captions as $caption) {
            $captionArray[] = ['id' => $caption->id, 'caption' => $caption->caption];
        }

        return $captionArray;
    }

    public function postMultiple(Request $request)
    {
    }

    public function acceptRequest(Request $request)
    {
    }

    public function sendRequest(Request $request)
    {
    }

    public function history(Request $request)
    {
    }

    public function messageQueue(Request $request)
    {
    }

    public function messageQueueSetting(Request $request)
    {
    }

    public function messageQueueApprove(Request $request)
    {
    }

    public function messageQueueApproved(Request $request)
    {
        $chatMessage = ChatMessage::find($request->chat_id);
        $chatMessage->is_queue = 1;
        $result = $chatMessage->save();

        if ($result) {
            return response()->json(['message' => 'Approved Successfully']);
        }

        return response()->json(['error' => 'Failed to change status']);
    }
}
