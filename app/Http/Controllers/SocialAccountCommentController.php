<?php

namespace App\Http\Controllers;

use App\BusinessComment;
use App\BusinessPost;
use Illuminate\Http\Request;

class SocialAccountCommentController extends Controller
{
    public function index($postId)
    {
        $post = BusinessPost::find($postId);
        $comments = BusinessComment::where('is_parent', 0)->where('post_id', $postId)->latest('time')->get();

        return view('social-account.comment', compact('post', 'comments'));
    }

    public function replyComments(Request $request)
    {
        try {
            $comments = BusinessComment::where('is_parent', 1)->where('parent_comment_id', $request->id)->latest()->get();

            return response()->json(['comments' => $comments]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
