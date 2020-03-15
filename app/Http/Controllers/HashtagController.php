<?php

namespace App\Http\Controllers;

use App\Account;
use App\CommentsStats;
use App\FlaggedInstagramPosts;
use App\HashTag;
use App\InstagramPosts;
use App\Services\Instagram\Hashtags;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;
use Plank\Mediable\Media;
use App\Setting;
use App\Jobs\InstagramComment;
use App\ScrapInfluencer;
use App\InstagramPostsComments;



Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class HashtagController extends Controller
{

    private $maxId;
    public $platformsId;

    public function __construct(Request $request){
        $this->platformsId = 1;
  }     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     * Show all the hashtags we have saved
     */
    public function index(Request $request)
    {
        if($request->term || $request->priority ){

            if($request->term != null && $request->priority == 'on'){

                 $hashtags  = HashTag::query()
                        ->where('priority',1)
                        ->where('platforms_id', $this->platformsId)
                        ->where('hashtag', 'LIKE', "%{$request->term}%")
                        ->paginate(Setting::get('pagination'));
                return view('instagram.hashtags.index', compact('hashtags'));        
            }
            if($request->priority == 'on'){
                $hashtags = HashTag::where('priority',1)->where('platforms_id', $this->platformsId)->paginate(Setting::get('pagination')); 
                return view('instagram.hashtags.index', compact('hashtags')); 
            }
            if($request->term != null){
                $hashtags  = HashTag::query()
                        ->where('hashtag', 'LIKE', "%{$request->term}%")
                        ->where('platforms_id', $this->platformsId)
                        ->paginate(Setting::get('pagination'));
                return view('instagram.hashtags.index', compact('hashtags'));        
            }
            
        }else{
            $hashtags = HashTag::where('platforms_id', $this->platformsId)->paginate(Setting::get('pagination'));
            return view('instagram.hashtags.index', compact('hashtags'));  
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * Create a new hashtag entry
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $hashtag = new HashTag();
        $hashtag->hashtag = $request->get('name');
        $hashtag->rating = $request->get('rating') ?? 8;
        $hashtag->platforms_id = $this->platformsId;
        $hashtag->save();

        return redirect()->back()->with('message', 'Hashtag created successfully!');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * Show hashtag
     */
    public function edit($hashtag, Request $request)
    {

        $h = HashTag::where('hashtag', $hashtag)->first();

        $maxId = '';
        if ($request->has('maxId')) {
            $maxId = $request->get('maxId');
        }

        $hashtags = new Hashtags();
        $hashtags->login();

        //get media for this instance + maxId (for next pagination)
        [$medias, $maxId] = $hashtags->getFeed($hashtag, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag);
        if ($h) {
            $h->post_count = $media_count;
            $h->save();
        }

        // Also get related hashtag..
        $relatedHashtags = $hashtags->getRelatedHashtags($hashtag);

        $accounts = Account::where('platform', 'instagram')->where('manual_comment', 1)->get();

        return view('instagram.hashtags.grid2', compact('medias', 'media_count', 'relatedHashtags', 'hashtag', 'accounts', 'maxId'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (is_numeric($id)) {
            $hash = HashTag::findOrFail($id);
            $hash->delete();
        } else {
            HashTag::where('hashtag', $id)->delete();
        }


        return redirect()->back()->with('message', 'Hashtag has been deleted successfuly!');
    }

    public function showGrid($id, Request $request)
    {
        
        $maxId = '';

        if ($request->has('maxId'))  {
            $maxId = $request->get('maxId');
        }

        $txt = $id;
        $ht = null;
        if (is_numeric($id)) {
            $hashtag = HashTag::findOrFail($id);
            $medias = $hashtag->instagramPost()->orderBy('id','desc')->paginate(20);
        }else{
            $hashtag = HashTag::where('hashtag','LIKE',$id)->first();
           $medias = $hashtag->instagramPost()->orderBy('id','desc')->paginate(20);
        }

        if($request->term || $request->date || $request->username || $request->caption || $request->location || $request->comment){
              $query  = InstagramPosts::query();
                if(request('term') != null) {
                $query->where('username', 'LIKE', "%{$request->term}%")
                    ->orWhere('caption', 'LIKE', "%{$request->term}%")
                    ->orWhere('location', 'LIKE', "%{$request->term}%")
                    ->orWhereHas('comments', function ($qu) use ($request) {
                      $qu->where('comment', 'LIKE', "%{$request->term}%");
                      });
                }

                if (request('username') != null) {
                $query->where('username', 'LIKE', '%' . request('username') . '%');
                }
                if (request('caption') != null) {
                    $query->where('caption', 'LIKE', '%' . request('caption') . '%');
                }
                if (request('location') != null) {
                    $query->where('location', 'LIKE', '%' . request('location') . '%');
                }

                if (request('comments') != null) {
                        $query->whereHas('comments', function ($qu) use ($request) {
                            $qu->where('comment', 'LIKE', '%' . request('comments') . '%');
                            });
                }


            $medias = $query->where('hashtag_id',$hashtag->id)->orderBy('id','desc')->paginate(20);

        }

        

        $media_count = 1;

        $hashtagList = HashTag::all();
        
        $accs = Account::where('platform', 'instagram')->where('manual_comment', 1)->get();

        $stats = CommentsStats::selectRaw('COUNT(*) as total, narrative')->where('target', $hashtag->hashtag)->groupBy(['narrative'])->get();

        if ($request->ajax()) {
           return response()->json([
                'tbody' => view('instagram.hashtags.data', compact('medias','hashtag', 'media_count', 'maxId', 'stats', 'accs', 'hashtagList'))->render(),
               'links' => (string)$medias->render()
            ], 200);
         }
        
        return view('instagram.hashtags.grid', compact('medias', 'hashtag', 'media_count', 'maxId', 'stats', 'accs', 'hashtagList'));
    }

    public function showGridComments($id, Request $request)
    {
        
        $maxId = '';

        if ($request->has('maxId'))  {
            $maxId = $request->get('maxId');
        }

        $txt = $id;
        $ht = null;
        if (is_numeric($id)) {
            $hashtag = HashTag::findOrFail($id);
        }else{
            $hashtag = HashTag::where('hashtag','LIKE',$id)->first();
        }

        $query = InstagramPostsComments::query();
        
        if($request->term){
            $query = $query->where('comment','LIKE','%'.$request->term.'%');
        }

        $query = $query->where('comment','LIKE','%'.$hashtag->hashtag.'%');
        
        $comments = $query->paginate(25);

        $accs = Account::where('platform', 'instagram')->where('manual_comment', 1)->get();
        
        if ($request->ajax()) {
           return response()->json([
                'tbody' => view('instagram.hashtags.comments.partials.data', compact('hashtag','comments','accs'))->render(),
               'links' => (string)$comments->render(),
               'total' => $comments->total(),
            ], 200);
         }

        return view('instagram.hashtags.comments.grid', compact('hashtag','comments','accs','id'));
    }

    public function loadComments($mediaId) {
        $instagram = new Instagram();
        $instagram->login('sololuxury.official', "NcG}4u'z;Fm7");
        $token = Signatures::generateUUID();

        $comments = $instagram->media->getComments($mediaId)->asArray();

        $comments['comments'] = array_map(function($comment) {
            $c = $comment['created_at'];
            $comment['created_at'] = Carbon::createFromTimestamp($comment['created_at'])->diffForHumans();
            $comment['created_at_time'] = Carbon::createFromTimestamp($c)->toDateTimeString();
            return $comment;
        }, $comments['comments']);


        return response()->json([
            'comments' => $comments['comments'] ?? [],
            'has_more_comments' => $comments['has_more_comments'] ?? false,
            'caption' => $comments['caption']
        ]);
    }

    public function sendHashtagsApi() {
        $hashtags = HashTag::where('platforms_id', $this->platformsId)->get(['hashtag', 'id']);

        return response()->json($hashtags);
    }

    public function showNotification() {
        $hashtags = new Hashtags();
        $hashtags->login();
        $maxId = '';
        $commentsFinal = [];

        do {
            $hashtagPostsAll = $hashtags->getFeed('sololuxury', $maxId);
            [$hashtagPosts, $maxId] = $hashtagPostsAll;

            foreach ($hashtagPosts as $hashtagPost) {
                $comments = $hashtagPost['comments'] ?? [];

                if ($comments === []) {
                    continue;
                }

                $postId = $hashtagPost['media_id'];
                $commentsFinal[$postId]['text'] = $hashtagPost['caption'];
                $commentsFinal[$postId]['code'] = $hashtagPost['code'];
                foreach ($comments as $comment) {
                    $createdAt = Carbon::createFromTimestamp($comment['created_at'])->diffForHumans();
                    $commentsFinal[$postId]['comments'][]    = [
                        'username' => $comment['user']['username'],
                        'text' => $comment['text'],
                        'created_at' => $createdAt,
                    ];
                }

            }

        } while($maxId!='END');

        return view('instagram.notifications', compact('commentsFinal'));

    }

    public function showProcessedComments(Request $request) {
         $posts = InstagramPosts::all();


        return view('instagram.comments', compact('posts'));
    }

    public function commentOnHashtag(Request $request) {

        $this->validate($request, [
            'message' => 'required',
            'account_id' => 'required',
            'id' => 'required',
            'hashtag' => 'required',
            'narrative' => 'required'
        ]);

        $acc = Account::findOrFail($request->get('account_id'));
        $acc->comment_pending = 1;
        $acc->save();
        
        
        // $instagram = new Instagram();
        
        // try {

        //     $instagramAdmin = Config('instagram');
        //     $senderUsername = $instagramAdmin['admin_account'];
        //     $password = $instagramAdmin['admin_password'];
        //     $instagram->login($senderUsername, $password);
        
        // } catch (\Exception $e) {
        //     $acc->last_name = env('IG_USERNAME');
        //     $instagram->login(env('IG_USERNAME'), env('IG_PASSWORD'));
        // }
        
        // $instagram->media->comment($request->get('post_id'), $request->get('message'));
        // $post = InstagramPosts::find($request->get('id'));

        InstagramComment::dispatchNow($request);
        
       
        $stat = new CommentsStats();
        $stat->target = $request->get('hashtag');
        $stat->sender = $acc->last_name;
        $stat->comment = $request->get('message');
        $stat->post_author = '';
        $stat->code = '';
        $stat->narrative = $request->get('narrative');
        $stat->save();

       

        return response()->json([
            'status' => 'success'
        ]);

    }

    public function flagMedia($id) {
        $m = new FlaggedInstagramPosts();
        $m->media_id = $id;
        $m->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function markPriority(Request $request)
    {
       // dd($request);
       $id = $request->id;
       //check if 30 limit is exceded
       $hashtags = HashTag::where('priority',1)->where('platforms_id', $this->platformsId)->get();
      
       if(count($hashtags) > 30 && $request->type == 1){
             return response()->json([
            'status' => 'error'
            ]);
       }

       $hashtag = HashTag::findOrFail($id);
       $hashtag->priority = $request->type;
       $hashtag->update(); 
       return response()->json([
            'status' => 'success'
        ]);
    }

    public function rumCommand(Request $request)
    {
        $id = $request->id;
      
     try {

       $art = \Artisan::call("hastag:instagram",['hastagId' => $id]);
       return ['success' => true, 'message' => 'Process Started Running'];
        } catch (\Exception $e) {
            return ['error' => true, 'message' => 'Something went wrong'];
        }
    }

    public function influencer(Request $request)
    {
        
        if($request->term != null){

                 $influencers  = ScrapInfluencer::query()
                        ->where('name', 'LIKE', "%{$request->term}%")
                        ->orWhere('phone', 'LIKE', "%{$request->term}%")
                        ->orWhere('website', 'LIKE', "%{$request->term}%")
                        ->orWhere('twitter', 'LIKE', "%{$request->term}%")
                        ->orWhere('facebook', 'LIKE', "%{$request->term}%")
                        ->orWhere('country', 'LIKE', "%{$request->term}%")
                        ->orWhere('email', 'LIKE', "%{$request->term}%")
                        ->paginate(25);
               
        }else{
          $influencers =  ScrapInfluencer::paginate(25);
        }
        
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('instagram.hashtags.partials.influencer-data', compact('influencers'))->render(),
                'links' => (string)$influencers->render(),
                'total' => $influencers->total(),
            ], 200);
            }

         return view('instagram.hashtags.influencers', compact('influencers'));
    }
}
