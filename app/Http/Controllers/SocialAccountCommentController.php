<?php

namespace App\Http\Controllers;

use App\BusinessComment;
use App\BusinessPost;
use App\Social\SocialConfig;
use App\SocialWebhookLog;
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
    public function devCommentsReply(Request $request)
    {
        $commentId = $request->contactId;
        $configId = $request->configId;
        $message = $request->input;
        $socialConfig = SocialConfig::find($configId);
        SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check config id', ['data' => $configId]);
        
        try {

            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => '']);
            $token = $socialConfig["token"];
            $url = "https://graph.facebook.com/v12.0/$commentId/replies?access_token=$token&message=$message";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            $resp = curl_exec($ch);
            

            $resp = json_decode($resp, true);
            if (isset($resp["id"])) {
                
                SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Comment Added) => Reply on Comment Successfully', ['data' => $resp]);
                
                return response()->json([
                    'message' => 'Message sent successfully',
                ]);

            }

           
        } catch (\Exception $e) {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => $e]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
