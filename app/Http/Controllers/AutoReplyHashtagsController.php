<?php

namespace App\Http\Controllers;

use App\AutoCommentHistory;
use App\AutoReplyHashtags;
use App\Services\Instagram\Hashtags;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class AutoReplyHashtagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $h = new AutoReplyHashtags();
        $h->text = $request->get('hashtag');
        $h->type = 'hashtag';
        $h->status = 1;
        $h->save();

        return redirect()->back()->with('action', 'Comment Target hashtag added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function show($hashtag, Request $request)
    {
        $hashtag = AutoReplyHashtags::findOrFail($hashtag);

        $maxId = '';
        if ($request->has('maxId')) {
            $maxId = $request->get('maxId');
        }


        $hashtags = new Hashtags();
        $hashtags->login();

        [$medias, $maxId] = $hashtags->getFeed($hashtag->text, $maxId);
        $media_count = $hashtags->getMediaCount($hashtag->text);
        $hid = $hashtag->id;
        $hashtag = $hashtag->text;


        return view('instagram.auto_comments.prepare', compact('medias', 'media_count', 'maxId', 'hashtag', 'hid'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $hashtag = AutoReplyHashtags::findOrFail($id);

        $this->validate($request, [
           'posts' => 'required|array'
        ]);

        $medias = $request->get('posts');

        foreach ($medias as $media) {
            $h = new AutoCommentHistory();
            $h->post_code = $request->get('code_'.$media);
            $h->post_id = $media;
            $h->caption = $request->get('caption_'.$media);
            $h->auto_reply_hashtag_id = $hashtag->id;
            $h->status = 1;
            $h->save();
        }

        return redirect()->back()->with('message', 'Attached successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AutoReplyHashtags  $autoReplyHashtags
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoReplyHashtags $autoReplyHashtags)
    {
        //
    }
}
