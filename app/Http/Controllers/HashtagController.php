<?php

namespace App\Http\Controllers;

use App\Account;
use App\HashTag;
use App\Services\Instagram\Hashtags;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

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
            'name' => 'required'
        ]);

        $hashtag = new HashTag();
        $hashtag->hashtag = $request->get('name');
        $hashtag->rating = $request->get('rating');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hashtag, Request $request)
    {
        $maxId = '';
        if ($request->has('maxId')) {
            $maxId = $request->get('maxId');
        }

        $hashtags = new Hashtags();
        $hashtags->login();

        [$medias, $maxId] = $hashtags->getFeed($hashtag, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag);
        $relatedHashtags = $hashtags->getRelatedHashtags($hashtag);

        $accounts = Account::all();

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
        if (is_numeric($id)) {
            $hashtag = HashTag::findOrFail($id);
            $txt = $hashtag->hashtag;
        }

        $hashtag = $txt;
        $hashtags = new Hashtags();
        $hashtags->login();

        [$medias, $maxId] = $hashtags->getFeed($hashtag, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag);
        return view('instagram.hashtags.grid', compact('medias', 'hashtag', 'media_count', 'maxId'));
    }

    public function loadComments($mediaId) {
        $instagram = new Instagram();
        $instagram->login('rishabh.aryal', 'R1shabh@123');
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
}
