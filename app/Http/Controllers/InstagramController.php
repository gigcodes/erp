<?php

namespace App\Http\Controllers;

use App\Services\Instagram\Instagram;
use Illuminate\Http\Request;

class InstagramController extends Controller
{
    private $instagram;

    public function __construct(Instagram $instagram)
    {
        $this->instagram = $instagram;

    }

    public function index() {

    }


    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * This method gives the list of posts
     * that is in Instagram account
     */
    public function showPosts(Request $request) {
        $url = null;

        if ($request->has('next') && !empty($request->get('next'))) {
            $url = substr($request->get('next'), 32);
        } else if ($request->has('previous') && !empty($request->get('previous'))) {
            $url = substr($request->get('previous'), 32);
        }

        [$posts, $paging] = $this->instagram->getMedia($url);

        return view('instagram.index', compact(
            'posts',
            'paging'
        ));
    }

    /**
     * @param Request $request
     * This method will store photo to
     * Instagram Business account
     */
    public function store(Request $request) {
        $this->validate($request, [
           'image' => 'required|image'
        ]);
    }

    public function getComments(Request $request) {
        $this->validate($request, [
            'post_id' => 'required'
        ]);

        $comments = $this->instagram->getComments($request->get('post_id'));

        return response()->json($comments);
    }

    public function postComment(Request $request) {
        $this->validate($request, [
            'message' => 'required',
            'post_id' => 'required'
        ]);

        if ($request->has('comment_id') && !empty($request->get('comment_id'))) {
            $commentId = $request->get('comment_id');
            $comment = $this->instagram->postReply($commentId, $request->get('message'));
            return response()->json($comment);
        }

        $comment = $this->instagram->postComment($request->get('post_id'), $request->get('message'));
        return response()->json($comment);
    }
}
