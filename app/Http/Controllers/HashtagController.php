<?php

namespace App\Http\Controllers;

use App\HashTag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use InstagramAPI\Signatures;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class HashtagController extends Controller
{
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
    public function edit($id)
    {
        //
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
        $hash = HashTag::findOrFail($id);
        $hash->delete();

        return redirect()->back()->with('message', 'Hashtag has been deleted successfuly!');
    }

    public function showGrid($id) {
        $hashtag = HashTag::findOrFail($id);

        $instagram = new Instagram();
        $instagram->login('rishabh.aryal', 'R1shabh@123');
        $token = Signatures::generateUUID();
        $media = $instagram->hashtag->getFeed($hashtag->hashtag, $token);

        $medias = $media->asArray()['items'];

        $medias = array_map(function($item) use ($instagram) {


            if ($item['media_type'] === 1) {
                $media = $item['image_versions2']['candidates'][1]['url'];
            } else if ($item['media_type'] === 2) {
                $media = $item['video_versions'][0]['url'];
            } else if ($item['media_type'] === 8) {
                $crousal = $item['carousel_media'];
                $media = [];
                foreach ($crousal as $cro) {
                    if ($cro['media_type'] === 1) {
                        $media[] = [
                            'media_type' => 1,
                            'url' => $cro['image_versions2']['candidates'][0]['url']
                        ];
                    } else if ($cro['media_type'] === 2) {
                        $media[] = [
                            'media_type' => 2,
                            'url' => $cro['video_versions'][0]['url']
                        ];
                    }
                }
            }

            $comments = [];

            if (isset($item['comment_count']) && $item['comment_count']) {
                $comments = $item['preview_comments'];
            }

            return [
                'username' => $item['user']['username'],
                'media_id' => $item['id'],
                'code' => $item['code'],
                'caption' => $item['caption']['text'],
                'like_count' => $item['like_count'],
                'comment_count' => $item['comment_count'] ?? '0',
                'media_type' => $item['media_type'],
                'media' => $media,
                'comments' => $comments,
                'created_at' => Carbon::createFromTimestamp($item['taken_at'])->diffForHumans(),
            ];
        }, $medias);

        return view('instagram.hashtags.grid', compact('medias', 'hashtag'));
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
