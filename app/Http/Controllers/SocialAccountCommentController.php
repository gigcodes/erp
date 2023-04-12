<?php

namespace App\Http\Controllers;

use App\BusinessComment;
use App\BusinessPost;
use App\Social\SocialConfig;
use App\SocialWebhookLog;
use Illuminate\Http\Request;
use App\GoogleTranslate;

class SocialAccountCommentController extends Controller
{
    public function index($postId)
    {
        //echo "Due to lake of permission we could not load comment section!!"; die();
        $post = BusinessPost::find($postId);
        $comments = BusinessComment::where('is_parent', 0)->where('post_id', $postId)->latest('time')->get();
        $googleTranslate        =   new GoogleTranslate();
        $target = 'en';
        foreach ($comments as $key => $value) {
            
            $translationString      =   $googleTranslate->translate('en', $value['message']);
            $value['translation'] = $translationString;
        }

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
        
        $googleTranslate        =   new GoogleTranslate();
        $target =$socialConfig['page_language']?$socialConfig['page_language']:'en';
        $translationString      =   $googleTranslate->translate($target, $message);
        
        SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check config id', ['data' => $configId]);
        
        try {
            $token = $socialConfig["token"];
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => '']);
            
            $comment_id = $commentId;
            $reply_message = $translationString;
            $url = 'https://graph.facebook.com/v15.0/'.$comment_id.'/replies';
            $data = array('message' => $reply_message, 'access_token' => $token);
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
            );
            
            // Initialize the cURL session
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            
            // Execute the cURL request and get the response
            $response = curl_exec($ch);
            
            // Close the cURL session
            curl_close($ch);
            
            // Process the response
           // $result = json_decode($response, true);
            
           // $token = $socialConfig["token"];
            // $url = "https://graph.facebook.com/v15.0/$commentId/replies?access_token=$token&message=$message";
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            // curl_setopt($ch, CURLOPT_POST, 1);
            // $resp = curl_exec($ch);
            // $resp = json_decode($resp, true);
            if (isset($response["id"])) {
                
                SocialWebhookLog::log(SocialWebhookLog::SUCCESS, 'Webhook (Comment Added) => Reply on Comment Successfully', ['data' => $response]);
                
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
