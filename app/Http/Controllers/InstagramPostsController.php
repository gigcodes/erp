<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\HashTag;
use App\InstagramPosts;
use App\Post;
use App\InstagramPostsComments;
use App\Setting;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use File;
use App\CommentsStats;
use App\InstagramCommentQueue;
use App\ScrapInfluencer;
use Carbon\Carbon;
use App\InstagramUsersList;
use App\Library\Instagram\PublishPost;
use Plank\Mediable\Media;

class InstagramPostsController extends Controller
{
    public function index(Request $request)
    {
        // Load posts
        if($request->hashtag){
            $posts = $this->_getFilteredInstagramPosts($request);
        }else{
            $posts = InstagramPosts::orderBy('id','desc');
        }
        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));
        // Return view
        return view('social-media.instagram-posts.index', compact('posts'));
    }


    public function post()
    {
        $accounts = \App\Account::where('platform','instagram')->whereNotNull('proxy')->where('status',1)->get();
        $used_space = 0;
        $storage_limit = 0;
        return view('instagram.post.create' , compact('accounts','used_space','storage_limit'));   
    }

    public function createPost(Request $request){
        
        $post = new Post();

        $post->account_id = $request->account;
        $post->type       = $request->type;
        $post->caption    = $request->caption;
        $post->ig         = [
            'media'    => $request->media,
            'location' => '',
        ];

        if (new PublishPost($post)) {
            return redirect()->route('post.index')
                ->with('success', __('Your post has been published'));
        } else {
            return redirect()->route('post.index')
                ->with('error', __('Post failed to published'));
        }

    }

    public function grid(Request $request)
    {
        // Load posts
        $posts = $this->_getFilteredInstagramPosts($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social-media.instagram-posts.json_grid', compact('posts'))->render(),
                'links' => (string)$posts->appends($request->all())->render()
            ], 200);
        }

        // Return view
        return view('social-media.instagram-posts.grid', compact('posts', 'request'));
    }

    private function _getFilteredInstagramPosts(Request $request) {
        // Base query
        $instagramPosts = InstagramPosts::orderBy('posted_at', 'DESC')
            ->join('hash_tags', 'instagram_posts.hashtag_id', '=', 'hash_tags.id')
            ->select(['instagram_posts.*','hash_tags.hashtag']);

        //Ignore google search result from DB
        $instagramPosts->where('source', '!=', 'google');
        
        // Apply hashtag filter
        if (!empty($request->hashtag)) {
            $instagramPosts->where('hash_tags.hashtag', str_replace('#', '', $request->hashtag));
        }

        // Apply author filter
        if (!empty($request->author)) {
            $instagramPosts->where('username', 'LIKE', '%' . $request->author . '%');
        }

        // Apply author filter
        if (!empty($request->post)) {
            $instagramPosts->where('caption', 'LIKE', '%' . $request->post . '%');
        }

        // Return instagram posts
        return $instagramPosts;
    }

    public function apiPost(Request $request)
    {
        // Get raw body
        $file = $request->file('file');

        $f = File::get($file);

        $payLoad = json_decode($f);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json'
            ], 400);
        }

        // Process input
        if (is_array($payLoad) && count($payLoad) > 0) {
            $payLoad = json_decode(json_encode($payLoad), true);

            // Loop over posts
            foreach ($payLoad as $postJson) {

                if(isset($postJson['Followers'])){
                    
                    $inf = ScrapInfluencer::where('name',$postJson['Owner'])->first();
                    if($inf == null){
                        $influencer = new ScrapInfluencer;
                        $influencer->name = $postJson['Owner'];
                        $influencer->url = $postJson['URL'];
                        $influencer->followers = $postJson['Followers'];
                        $influencer->following = $postJson['Following'];
                        $influencer->posts = $postJson['Posts'];
                        $influencer->description = $postJson['Bio'];
                        $influencer->save();
                    }
                }else{
                        // Set tag
                    $tag = $postJson[ 'Tag used to search' ];

                    // Get hashtag ID
                    $hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);
                    $hashtag->is_processed = 1;
                    $hashtag->save();

                    // Retrieve instagram post or initiate new
                    $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson[ 'URL' ]]);
                    $instagramPost->hashtag_id = $hashtag->id;
                    $instagramPost->username = $postJson[ 'Owner' ];
                    $instagramPost->caption = $postJson[ 'Original Post' ];
                    $instagramPost->posted_at = date('Y-m-d H:i:s', strtotime($postJson[ 'Time of Post' ]));
                    $instagramPost->media_type = !empty($postJson[ 'Image' ]) ? 'image' : 'other';
                    $instagramPost->media_url = !empty($postJson[ 'Image' ]) ? $postJson[ 'Image' ] : $postJson[ 'URL' ];
                    $instagramPost->source = 'instagram';
                    $instagramPost->save();

                    // Store media
                    if (!empty($postJson[ 'Image' ])) {
                        if (!$instagramPost->hasMedia('instagram-post')) {
                            $media = MediaUploader::fromSource($postJson[ 'Image' ])
                                ->toDisk('uploads')
                                ->toDirectory('social-media/instagram-posts/' . floor($instagramPost->id / 1000))
                                ->useFilename($instagramPost->id)
                                ->beforeSave(function (\Plank\Mediable\Media $model, $source) {
                                    $model->setAttribute('extension', 'jpg');
                                })
                                ->upload();
                            $instagramPost->attachMedia($media, 'instagram-post');
                        }
                    }

                    // Comments
                    if (isset($postJson[ 'Comments' ]) && is_array($postJson[ 'Comments' ])) {
                        // Loop over comments
                        foreach ($postJson[ 'Comments' ] as $comment) {
                            // Check if there really is a comment
                            if (isset($comment[ 'Comments' ][ 0 ])) {
                                // Set hash
                                $commentHash = md5($comment[ 'Owner' ] . $comment[ 'Comments' ][ 0 ] . $comment[ 'Time' ]);

                                $instagramPostsComment = InstagramPostsComments::firstOrNew(['comment_id' => $commentHash]);
                                $instagramPostsComment->instagram_post_id = $instagramPost->id;
                                $instagramPostsComment->comment_id = $commentHash;
                                $instagramPostsComment->username = $comment[ 'Owner' ];
                                $instagramPostsComment->comment = $comment[ 'Comments' ][ 0 ];
                                $instagramPostsComment->posted_at = date('Y-m-d H:i:s', strtotime($comment[ 'Time' ]));
                                $instagramPostsComment->save();
                            }
                        }
                    } 
                }
                
            }
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }


    public function sendAccount($token)
    {
      if($token != 'sdcsds'){
        return response()->json(['message' => 'Invalid Token'], 400);
      }
      $account = Account::where('platform','instagram')->where('comment_pending',1)->first();

     return response()->json(['username' => $account->last_name , 'password' => $account->password], 200);
    }

    public function getComments($username)
    {
        $account = Account::where('last_name',$username)->first();

        if($account == null && $account == ''){
             return response()->json(['result' =>  false,'message' => 'Account Not Found'], 400);
        }

        $comments = InstagramCommentQueue::select('id','post_id','message')->where('account_id',$account->id)->where('is_send',0)->take(20)->get();
        if(count($comments) != 0){
            return response()->json(['result' => true , 'comments' => $comments],200); 
        }else{
            return response()->json(['result' =>  false, 'message' => 'No messages'],200); 
        }
               

    }

    public function commentSent(Request $request)
    {
        $id = $request->id;
        $comment = InstagramCommentQueue::find($id);
        $comment->is_send = 1;
        $comment->save();

    }    

    public function getHashtagList()
    {
        $hastags = HashTag::select('id','hashtag')->where('is_processed',0)->first();

        if(!$hastags){
            $hastags = HashTag::select('id','hashtag')->where('is_processed',2)->first();
        }
        
        if(!$hastags){
            return response()->json(['hastag' => ''],200);
        }

        return response()->json(['hastag' => $hastags ],200);

    }

    public function saveFromLocal(Request $request)
    {
        // Get raw JSON
        $receivedJson = json_decode($request->getContent());
        
        //Saving post details 
        if(isset($receivedJson->post)){
            
            $checkIfExist = InstagramPosts::where('post_id', $receivedJson->post->post_id)->first();

            if(empty($checkIfExist)){
                $media             = new InstagramPosts();
                $media->post_id    = $receivedJson->post->post_id;
                $media->caption    = $receivedJson->post->caption;
                $media->user_id    = $receivedJson->post->user_id;
                $media->username   = $receivedJson->post->username;
                $media->media_type = $receivedJson->post->media_type;
                $media->code       = $receivedJson->post->code;
                $media->location   = $receivedJson->post->location;
                $media->hashtag_id = $receivedJson->post->hashtag_id;
                $media->likes = $receivedJson->post->likes;
                $media->comments_count = $receivedJson->post->comments_count;
                $media->media_url = $receivedJson->post->media_url;
                $media->posted_at = $receivedJson->post->posted_at;
                $media->save();

            if($media){
                if(isset($receivedJson->comments)){
                    $comments = $receivedJson->comments;
                        foreach ($comments as $comment) {

                            $commentEntry = InstagramPostsComments::where('comment_id', $comment->comment_id)->where('user_id', $comment->user_id)->first();

                            if (!$commentEntry) {
                                $commentEntry = new InstagramPostsComments();
                            }

                            $commentEntry = new InstagramPostsComments();
                            $commentEntry->user_id = $comment->user_id;
                            $commentEntry->name = $comment->name;
                            $commentEntry->username = $comment->username;
                            $commentEntry->instagram_post_id = $comment->instagram_post_id;
                            $commentEntry->comment_id = $comment->comment_id;
                            $commentEntry->comment = $comment->comment;
                            $commentEntry->profile_pic_url = $comment->profile_pic_url;
                            $commentEntry->posted_at = $comment->posted_at;
                            $commentEntry->save();
                    }        
                        }
                }

            if(isset($receivedJson->userdetials)){    
                $detials = $receivedJson->userdetials;
                $userList = InstagramUsersList::where('user_id',$detials->user_id)->first();
                if(empty($userList)){
                    $user = new InstagramUsersList;
                    $user->username = $detials->username;
                    $user->user_id = $detials->user_id;
                    $user->image_url = $detials->image_url;
                    $user->bio = $detials->bio;
                    $user->rating = 0;
                    $user->location_id = 0;
                    $user->because_of = $detials->because_of;
                    $user->posts = $detials->posts;
                    $user->followers = $detials->followers;
                    $user->following = $detials->following;
                    $user->location = $detials->location;
                    $user->save();
                }else{
                    if($userList->posts == ''){
                        $userList->posts = $detials->posts;
                        $userList->followers = $detials->followers;
                        $userList->following = $detials->following;
                        $userList->location = $detials->location;
                        $userList->save();
                    }
                }        
            } 


          }     

            
        }
    }

    public function viewPost(Request $request)
    {
        $accounts = Account::where('platform','instagram')->whereNotNull('proxy')->get();

        $data = Post::whereNotNull('id')->paginate(10);
        
        return view('instagram.post.index', compact(
            'accounts',
            'data'
        ));
    }


    public function users(Request $request)
    {
        $users = \App\InstagramUsersList::whereNotNull('username')->where('is_manual',1)->orderBy('id','desc')->paginate(25);
        return view('instagram.users',compact('users'));
    }


    public function getUserForLocal()
    {
        $users = \App\InstagramUsersList::select('id','user_id')->whereNotNull('username')->where('is_manual',1)->where('is_processed',0)->orderBy('id','desc')->first();
        return json_encode($users);
        
    }

    public function userPost($id)
    {
        dd($id);
    }
}
