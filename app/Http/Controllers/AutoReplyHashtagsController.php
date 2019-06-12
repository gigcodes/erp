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

        $maxId = [];
        if ($request->has('maxId')) {
            $maxId = $request->get('maxId');
        }

        $country = $request->get('country');
        $hashtags = new Hashtags();
        $hashtags->login();

        $keywords = $request->get('keywords');

        $alltags = $request->get('hashtags');
        $allMedias = [];
        $allCounts = [];

        foreach ($alltags as $tag) {

            $arh = AutoReplyHashtags::where('text', $tag)->first();

            if (!$arh) {
                $arh = new AutoReplyHashtags();
                $arh->text = $tag;
                $arh->type = 'hashtag';
                $arh->save();
            }

            [$medias, $maxId] = $hashtags->getFeed($tag, $maxId[$tag] ?? '', $country, $keywords);
            $media_count = $hashtags->getMediaCount($tag);
            $allCounts[$tag] = $media_count;
            $maxIds[$tag] = $maxId;
            $allMedias = array_merge($allMedias, $medias);
        }

        $countryText = $request->get('country');

        $medias = $allMedias;

        $hashtag = implode(',', $alltags);

        return view('instagram.auto_comments.prepare', compact('medias', 'media_count', 'maxId', 'hashtag', 'countryText'));

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
        $this->validate($request, [
           'posts' => 'required|array',
        ]);

        $medias = $request->get('posts');

        foreach ($medias as $media) {
            $h = new AutoCommentHistory();
            $h->target = $request->get('hashtag_'.$media);
            $h->post_code = $request->get('code_'.$media);
            $h->post_id = $media;
            $h->caption = $request->get('caption_'.$media);
            $h->gender = $request->get('gender_'.$media);
            $h->auto_reply_hashtag_id = 1;
            $h->country = strlen($request->get('country')) > 4 ? $request->get('country') : '';
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
