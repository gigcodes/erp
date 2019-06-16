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

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class HashtagController extends Controller
{

    private $maxId;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hashtags = HashTag::all();

        return view('instagram.hashtags.index', compact('hashtags'));
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
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $hashtag = new HashTag();
        $hashtag->hashtag = $request->get('name');
        $hashtag->rating = $request->get('rating') ?? 8;
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

        [$medias, $maxId] = $hashtags->getFeed($hashtag, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag);
        if ($h) {
            $h->post_count = $media_count;
            $h->save();
        }
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

        $hashtagList = HashTag::all();

        $txt = $id;
        $ht = null;
        if (is_numeric($id)) {
            $hashtag = HashTag::findOrFail($id);
            $ht = $hashtag;
            $txt = $hashtag->hashtag;
        } else if ($txt == 'x') {
            $txt = $request->get('name');
        }

        $hashtag = $txt;
        $hashtags = new Hashtags();
        $hashtags->login();

        [$medias, $maxId] = $hashtags->getFeed($hashtag, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag);

        if ($ht) {
            $ht->post_count = $media_count;
            $ht->save();
        }

        krsort($medias);

        $accs = Account::where('platform', 'instagram')->where('manual_comment', 1)->get();

        $stats = CommentsStats::selectRaw('COUNT(*) as total, narrative')->where('target', $hashtag)->groupBy(['narrative'])->get();
//        $stats = CommentsStats::selectRaw('COUNT(*) as total, YEAR(created_at) as year, MONTH(created_at) as month')->where('target', $hashtag)->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))->get();

        return view('instagram.hashtags.grid', compact('medias', 'hashtag', 'media_count', 'maxId', 'stats', 'accs', 'hashtagList'));
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
        $hashtags = HashTag::get(['hashtag', 'id']);

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
            'post_id' => 'required',
            'account_id' => 'required',
            'code' => 'required',
            'author' => 'required',
            'hashtag' => 'required',
            'narrative' => 'required'
        ]);

        $acc = Account::findOrFail($request->get('account_id'));

        $instagram = new Instagram();
        $instagram->login($acc->last_name, $acc->password);
        $instagram->media->comment($request->get('post_id'), $request->get('message'));

        $stat = new CommentsStats();
        $stat->target = $request->get('hashtag');
        $stat->sender = $acc->last_name;
        $stat->comment = $request->get('message');
        $stat->post_author = $request->get('author');
        $stat->code = $request->get('code');
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
}
