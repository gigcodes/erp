<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\HashTag;
use App\InstagramPosts;
use App\InstagramPostsComments;
use App\Setting;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use File;
use App\CommentsStats;
use App\InstagramCommentQueue;
use App\ScrapInfluencer;

class InstagramPostsController extends Controller
{
    public function index(Request $request)
    {
        // Load posts
        $posts = $this->_getFilteredInstagramPosts($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // Return view
        return view('social-media.instagram-posts.index', compact('posts'));
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

    public function getComments($username , $password)
    {
        $account = Account::where('last_name',$username)->where('password',$password)->first();

        if($account == null && $account == ''){
             return response()->json(['message' => 'Account Not Found'], 400);
        }
        $comments = InstagramCommentQueue::where('account_id',$account->id)->where('is_send',0)->get()->take(20);
        foreach ($comments as $comment) {
            $commentArray[] = $comment->message;
            $codeArray[] = str_replace(["https://www.instagram.com/p/","/"],'',$comment->getPost->location);
        }
        
        return response()->json(['comment' => $commentArray , 'code' => $codeArray ],200);        

    }

    public function commentSent(Request $request)
    {
        $comment = $request->comment;
        $postId = $request->post_id;
        $comments = InstagramCommentQueue::where('post_id',$postId)->where('message','LIKE','%'.$comment)->first().'%';
        $comments->is_send = 1;
        $comments->save();

    }    

    public function getHashtagList()
    {
        $hastags = HashTag::select('id','hashtag')->where('priority',1)->get();

        if(count($hastags) == 0){
            $hastags = HashTag::select('id','hashtag')->where('priority',2)->get();
        }

        if(count($hastags) == 0){
            return response()->json(['hastag' => []],200);
        }

        foreach ($hastags as $hastag) {
            $hastagIdArray[] = $hastag->id;
            $hastagArray[] = $hastag->hashtag;
        }
        
        return response()->json(['hastag' => $hastagArray ],200);

    }
}
