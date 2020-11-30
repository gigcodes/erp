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
use App\StoreSocialContent;
use UnsplashSearch;
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;



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


    public function post(Request $request)
    {
        //$accounts = \App\Account::where('platform','instagram')->whereNotNull('proxy')->where('status',1)->get();
        $accounts = \App\Account::where('platform','instagram')->where('status',1)->get();

        //$posts = Post::where('status', 1)->get();
        
        $query = Post::query();
        
        if($request->acc){
            $query = $query->where('id', $request->acc);
        }
        if($request->comm){
            $query = $query->where('comment', 'LIKE','%'.$request->comm.'%');
        }
        if($request->tags){
            $query = $query->where('hashtags', 'LIKE','%'.$request->tags.'%');
        }
        if($request->loc){
            $query = $query->where('location', 'LIKE','%'.$request->loc.'%');
        }
        $posts = $query->orderBy('id', 'asc')->paginate(25)->appends(request()->except(['page']));


        $used_space = 0;
        $storage_limit = 0;
        $contents = StoreSocialContent::query();
        $contents = $contents->get();
        $records = [];
        foreach($contents as $site) {
            if ($site) {
                    if ($site->hasMedia(config('constants.media_tags'))) {
                        foreach ($site->getMedia(config('constants.media_tags')) as $media) {                                  
                            $records[] = [
                                "id"        => $media->id,
                                'extension' => strtolower($media->extension), 
                                'file_name' => $media->filename, 
                                'mime_type' => $media->mime_type, 
                                'size' => $media->size , 
                                'thumb' => $media->getUrl() , 
                                'original' => $media->getUrl() 
                            ];
                        }
                    }
            }
        }
        return view('instagram.post.create' , compact('accounts','records','used_space','storage_limit', 'posts'))->with('i', ($request->input('page', 1) - 1) * 5);;   
    }

    public function createPost(Request $request){
        
        //resizing media 
        
        $all = $request->all();

        //dd($request->media);
        if($request->media)
        {
            foreach ($request->media as $media) {
               
                $mediaFile = Media::where('id',$media)->first();
                $image = self::resize_image_crop($mediaFile,640,640);
            }
        }
        

        $post = new Post();
        $post->account_id = $request->account;
        $post->type       = $request->type;
        $post->caption    = $request->caption;
        $ig         = [
            'media'    => $request->media,
            'location' => '',
        ];
        $post->ig       = json_encode($ig);
        $post->location = $request->location;
        $post->hashtags = $request->hashtags;
        $post->save();
        return redirect()->route('post.index')
                ->with('success', __('Your post has been saved'));

        /*if (new PublishPost($post)) {
            return redirect()->route('post.index')
                ->with('success', __('Your post has been published'));
        } else {
            return redirect()->route('post.index')
                ->with('error', __('Post failed to published'));
        }*/

    }


    public function publishPost(Request $request, $id){
       
        $post = Post::find($id);
        $media = json_decode($post->ig,true);
        $ig         = [
            'media'    => $post->media,
            'location' => '',
        ];
        $post->ig = $ig;
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
                        if(isset($postJson['keyword'])){
                            $influencer->keyword = $postJson['keyword'];
                        }
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

    public function resizeToRatio()
    {
        
    }

    public  function resize_image_crop($image,$width,$height) {
        
        $newImage = $image;
        $type = $image->mime_type;
        
        if($type == 'image/jpeg'){
            $src_img = imagecreatefromjpeg($image->getAbsolutePath());    
        }elseif($type == 'image/png'){
            $src_img = imagecreatefrompng($image->getAbsolutePath());
        }elseif ($type == 'image/gif') {
            $src_img = imagecreatefromgif($image->getAbsolutePath());
        }
        
        $image = $src_img;
        $w = imagesx($image); //current width
        
        $h = @imagesy($image); //current height
        
        if ((!$w) || (!$h)) { $GLOBALS['errors'][] = 'Image could not be resized because it was not a valid image.'; return false; }
        if (($w == $width) && ($h == $height)) { return $image; } //no resizing needed

        //try max width first...
        $ratio = $width / $w;
        $new_w = $width;
        $new_h = $h * $ratio;

        //if that created an image smaller than what we wanted, try the other way
        if ($new_h < $height) {
            $ratio = $height / $h;
            $new_h = $height;
            $new_w = $w * $ratio;
        }

        $image2 = imagecreatetruecolor ($new_w, $new_h);
        imagecopyresampled($image2,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

        //check to see if cropping needs to happen

        $image3 = imagecreatetruecolor($width, $height);
        if ($new_h > $height) { //crop vertically
            $extra = $new_h - $height;
            $x = 0; //source x
            $y = round($extra / 2); //source y
            imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        else {
            $extra = $new_w - $width;
            $x = round($extra / 2); //source x
            $y = 0; //source y
            imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        imagedestroy($image2);
        imagejpeg($image3,$newImage->getAbsolutePath());
        return $image3;
     

    }

    public function hashtag(Request $request, $word)
    {
        //$arr = array("hashtag","Hashtag","HASHTAG","HashTag","haShtag","HASHtag");
        //echo json_encode(array_values($arr),JSON_FORCE_OBJECT);exit; 

        if($word)
        {
            //$response = $this->getHastagifyApiToken();

            $response['access_token'] = 1;
            if(isset($response['access_token']))
            {
                $json = $this->getHashTashSuggestions($response['access_token'], $word);

                $arr = json_decode($json, true);
                $instaTags = [];
                if(isset($arr['code']) && $arr['code']=='404')
                {
                    //handle for error
                }else{
                    if(isset($arr['hashtag']))
                    {
                        $relatedTags = $arr['hashtag']['variants'];
                        
                        foreach($relatedTags as $tag)
                        {
                            if(ctype_digit($tag) && (int) $tag > 0)
                            {
                                continue;
                            }
                            $instaTags[] = $tag;
                        }
                    }
                }
                echo json_encode(array_values($instaTags));
            }
        }
    }


    public function getHastagifyApiToken()
    {
        //Update Parameters
        //$clientCredentials = '';
        $consumerKey = env('HASTAGIFY_CONSUMER_KEY');
        $consumerSecret = env('HASTAGIFY_CONSUMER_SECRET');
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.hashtagify.me/oauth/token",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=".$consumerKey."&client_secret=".$consumerSecret,
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          //echo "cURL Error #:" . $err;
        } else {
          return $response;
        }
    }


    public function getHashTashSuggestions($token, $word)
    {

        $json = '{"hashtag":{"name":"hashtag","tweets_count":"78386","accounts_count":"56379","related_tags":["socialmedia","1874","rt","1544","twitter","1257","hash4tag","998","instagram","997","you","925","people","909","a","889","besthashtag","862","yolo","861"],"variants":["hashtag","59382","Hashtag","14688","HASHTAG","2668","HashTag","1540","haShtag","25","HASHtag","21"],"languages":["en","37984","und","7301","es","5333","de","4309","ko","1912","fr","1665"],"countries":["US","2065","IN","471","BR","300","GB","283","TR","267","NL","216","FR","185","CA","180","ID","139","NG","135"],"users":["kanyewest","7631355611","selenagomez","3609260409","LiamPayne","3311849446","ddlovato","3094303962"],"users_tweets":["Hash4Tag","858","hashtag_keyword","690","HeAintReally","503","SaikiranAbd","194"],"days_times":["2D","588","4D","587","6I","576","3X","549","3V","549","3P","548","1R","543","2Q","535","5O","532","3R","531","1T","529","5D","519","3U","517","2O","509","4V","503","3S","495","3O","495","2U","493","3T","489","2V","487","4C","484","4B","484","5P","481","4W","480","2R","471","5B","470","4Q","469","4A","468","2P","468","1S","468","6J","462","3D","457","2T","457","5Q","456","1Q","455","5C","454","7D","451","1V","451","1D","450","4X","449","4O","449","5T","447","4S","447","2E","446","4N","445","4L","445","5V","444","5N","444","2W","444","6W","442","3N","441","3Q","440","3A","439","4U","438","4P","437","3W","437","2C","437","5W","436","1B","436","1U","435","5E","434","2B","434","4E","433","3B","431","6R","430","6Q","430","5A","426","4T","426","3C","425","2N","425","7V","423","5X","422","5U","422","5S","422","5R","421","6S","419","2S","419","6P","416","3M","415","7T","414","3E","414","7P","412","1X","411","7U","409","1P","408","1W","407","7X","405","1N","405","1O","404","7Q","401","1E","398","7S","397","7A","396","5M","391","4R","391","7W","390","2A","390","1C","390","6A","388","6O","387","2X","387","4F","386","2F","386","7R","383","5F","382","6U","381","4H","374","7B","372","4M","370","6E","369","4J","369","1A","368","6B","367","3H","367","5H","364","2L","358","6V","356","6T","353","6C","351","6D","348","6X","345","4G","340","1F","340","2M","339","6N","338","4I","335","3G","335","1K","335","7E","334","7N","332","7C","332","3L","331","7O","330","5L","330","6H","328","1M","321","7M","320","3F","319","5G","318","2H","318","3I","315","6M","314","5I","313","7F","310","3J","309","2J","308","2G","306","2I","304","5K","303","3K","303","6F","298","2K","298","1H","298","1L","295","1I","292","1G","292","6G","290","4K","287","6K","285","7G","276","6L","274","7J","271","5J","266","7H","263","7K","255","1J","255","7L","246","7I","241"],"trend_data":{"past_weeks":[{"tweets":"44","accounts":"41","total_accounts":"12740470","total_accounts_withttags":"2407951","total_tweets_withtags":3806993,"top_hashtag_tweets":"145386","top_hashtag_accounts":"76373"},{"tweets":"31","accounts":"31","total_accounts":"9217032","total_accounts_withttags":"1575904","total_tweets_withtags":2255611,"top_hashtag_tweets":"51236","top_hashtag_accounts":"41654"},{"tweets":"26","accounts":"26","total_accounts":"12393941","total_accounts_withttags":"2251306","total_tweets_withtags":3477420,"top_hashtag_tweets":"52033","top_hashtag_accounts":"35660"},{"tweets":"30","accounts":"30","total_accounts":"14560553","total_accounts_withttags":"2732662","total_tweets_withtags":4360480,"top_hashtag_tweets":"68244","top_hashtag_accounts":"55267"},{"tweets":"53","accounts":"51","total_accounts":"14559844","total_accounts_withttags":"2801554","total_tweets_withtags":4639566,"top_hashtag_tweets":"127780","top_hashtag_accounts":"58109"},{"tweets":"56","accounts":"52","total_accounts":"13257207","total_accounts_withttags":"2596043","total_tweets_withtags":4231273,"top_hashtag_tweets":"139617","top_hashtag_accounts":"64811"},{"tweets":"68","accounts":"67","total_accounts":"14746290","total_accounts_withttags":"2774628","total_tweets_withtags":4676563,"top_hashtag_tweets":"73944","top_hashtag_accounts":"58449"},{"tweets":"44","accounts":"44","total_accounts":"12701086","total_accounts_withttags":"2358923","total_tweets_withtags":3850294,"top_hashtag_tweets":"278140","top_hashtag_accounts":"70680"}],"past_days":[{"tweets":"3","total_tweets_withtags":458037,"top_hashtag_tweets":"56755"},{"tweets":"24","total_tweets_withtags":770343,"top_hashtag_tweets":"144083"},{"tweets":"11","total_tweets_withtags":741701,"top_hashtag_tweets":"83576"},{"tweets":"4","total_tweets_withtags":660921,"top_hashtag_tweets":"52932"},{"tweets":"6","total_tweets_withtags":634723,"top_hashtag_tweets":"19102"},{"tweets":null,"total_tweets_withtags":0},{"tweets":"5","total_tweets_withtags":525444,"top_hashtag_tweets":"40607"},{"tweets":"8","total_tweets_withtags":607409,"top_hashtag_tweets":"14751"},{"tweets":"10","total_tweets_withtags":636611,"top_hashtag_tweets":"18280"},{"tweets":"12","total_tweets_withtags":613075,"top_hashtag_tweets":"13615"},{"tweets":"8","total_tweets_withtags":663532,"top_hashtag_tweets":"24903"},{"tweets":"8","total_tweets_withtags":643614,"top_hashtag_tweets":"14257"},{"tweets":"3","total_tweets_withtags":335268,"top_hashtag_tweets":"8388"},{"tweets":null,"total_tweets_withtags":0}],"current_day_of_week":2},"breakout_data":[],"htdistr":["1","19628","2","11777","3","9846","4","7224","5","5959","6","3663","7","2642","8","1766","9","1286","10","1021","11","653","12","517","13","354","14","349","15","252","16","129","18","59","17","42","19","36","21","22","20","18","22","7","23","4","25","1"],"t3u":2.616538667619218,"t3r":5.964075217513331,"tweets_per_user":1.3903403749623087,"loneliness":25.040185747454903,"definitions":null,"with_related_tags_data":true}}';
        return $json;exit;


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.hashtagify.me/1.0/tag/".$word,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$ACCESS_TOKEN,
            "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          //echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }

    public function updateHashtagPost(Request $request)
    {
        $post_id = $request->get('post_id');
        $updateArr = [];


        if($request->get('account_id'))
        {
            $updateArr['account_id'] = $request->get('account_id');
        }
        if($request->get('comment'))
        {
            $updateArr['comment'] = $request->get('comment');
        }
        if($request->get('post_hashtags'))
        {
            $updateArr['hashtags'] = $request->get('post_hashtags');
        }
        if($request->get('type'))
        {
            $updateArr['type'] = $request->get('type');
        }
        Post::where('id', $post_id)->update($updateArr);
        echo json_encode(array("message"=>"Data Updated Succesfully"));
    }

    public function getImages()
    {
        $images = [];
        $keywords = ['fashion','living'];
        $number = rand(1,500);
        $response = UnsplashSearch::photos('fashion',['page' => $number, 'order_by' => 'latest']);
        $content =  $response->getContents();
        $lists = json_decode($content);

        foreach ($lists->results as $list) {
            $images[] = $list->urls->full;
        }

        return $images;

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
        if($request->account_id){

            $account = \App\Account::find($request->account_id); 

            $images = $request->imageURI;
            $captions = $request->captions;
            for ($i=0; $i < count($request->captions); $i++) { 
                $media = [];
                $imageURL = $images[$i];
                $captionId = $captions[$i];
                $caption = \App\Caption::find($captionId);

                $file = @file_get_contents($imageURL);
                $savedMedia =   MediaUploader::fromString($file)
                ->useFilename(uniqid(true))
                ->toDirectory('instagram-media')
                ->upload();
                $account->attachMedia($savedMedia, 'instagram-profile');

                //getting media id 
                $lastMedia = $account->lastMedia('instagram-profile');

                $media[] = $lastMedia->id;
                
                $post = new Post();

                $post->account_id = $account->id;
                $post->type       = 'post';
                $post->caption    = $caption->caption;
                $post->ig         = [
                    'media'    => $media,
                    'location' => '',
                ];

                $mediaFile = Media::where('id',$lastMedia->id)->first();
                $image = self::resize_image_crop($mediaFile,640,640);
                    

                if (new PublishPost($post)) {
                    sleep(10); 
                } else {
                    sleep(30);
                }
            }

            return response()->json(['Post Added Succesfully'], 200);



            
        }
        
    }

    public function likeUserPost(Request $request)
    {
       if($request->account_id){
            $account = \App\Account::find($request->account_id); 
            $instagram = new Instagram();
            $instagram->login($account->last_name, $account->password);
            $getdatas=$instagram->people->getSelfFollowing($instagram->uuid);
            $decode= json_decode($getdatas);
            $count = 0;
            $lastCount = rand(5,10);
            foreach ($decode->users as $value) {
                if($count == $lastCount){
                    break;
                }
                $getdata=$instagram->timeline->getUserFeed($value->pk);
                $decode_data= json_decode($getdata);
                // print_r($decode_data);die;
                $likePostCount = 0;
                $likePostCountLast = rand(5,10);

                foreach ($decode_data->items as $data) {
                    if($likePostCount == $likePostCountLast){
                        break;
                    }
                    sleep(rand(5,10));
                    $getdatass=$instagram->media->like($data->id,'0');
                    $likePostCount++;
                }
                $count++;
            }
            return response()->json(['Post Added Succesfully'], 200);
       }
    }

    public function acceptRequest(Request $request)
    {
       if($request->account_id){
            $account = \App\Account::find($request->account_id); 
            $instagram = new Instagram();
            $instagram->login($account->last_name, $account->password);
            $getdatas=$instagram->people->getPendingFriendships();
                    
                $decode= json_decode($getdatas);
                $count = 0;
                $lastCount = rand(5,10);
                foreach($decode->users as $getdata){
                    if($count == $lastCount){
                        break;
                    }
                    sleep(rand(5,10));
                    $getdata=$instagram->people->approveFriendship($getdata->pk);
                    $count++;
                }
            return response()->json(['Post Added Succesfully'], 200);

       }
    }

    public function sendRequest(Request $request)
    {
       if($request->account_id){
            $account = \App\Account::find($request->account_id); 
            $instagram = new Instagram();
            $instagram->login($account->last_name, $account->password);
            $pk = $instagram->people->getUserIdForName($account->last_name);
            $var =$instagram->people->getSuggestedUsers($pk);
            $data= json_decode($var);
            $count = 0;
            $lastCount = rand(5,10);
            foreach($data->users as $user){
                if($count == $lastCount){
                        break;
                }
                sleep(rand(10,30));
                $instagram->people->follow($user->pk);
                $count++;
            }

            return response()->json(['Post Added Succesfully'], 200);

       }
    }

    

}
