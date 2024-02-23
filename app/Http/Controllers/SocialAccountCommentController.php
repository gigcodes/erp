<?php

namespace App\Http\Controllers;

use App\Reply;
use App\LogRequest;
use App\ReplyCategory;
use App\BusinessComment;
use App\GoogleTranslate;
use App\SocialWebhookLog;
use App\Social\SocialPost;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Models\SocialComments;
use Illuminate\Database\Eloquent\Builder;

class SocialAccountCommentController extends Controller
{
    public function index(Request $request, $postId)
    {
        $post = SocialPost::where('ref_post_id', $postId)->firstOrFail();
        $search = $request->get('search');
        $comments = SocialComments::where('post_id', $post->id)->whereNull('parent_id');
        $comments = $comments->when($request->has('search'), function (Builder $builder) use ($search) {
            return $builder->where('comment_id', 'LIKE', '%' . $search . '%')->orWhere('message', 'LIKE', '%' . $search . '%');
        });

        $comments = $comments->latest()->get();
        $googleTranslate = new GoogleTranslate();
        foreach ($comments as $key => $value) {
            $translationString = $googleTranslate->translate('en', $value['message']);
            $value['translation'] = $translationString;
        }

        return view('social-account.comment', compact('post', 'comments'));
    }

    public function allcomments(Request $request)
    {
        $search = request('search', '');
        $social_config = request('social_config', '');
        $store_website_id = request('store_website_id', '');

        $totalcomments = BusinessComment::where('is_parent', 0)->count();

        $comments = BusinessComment::with('bussiness_post', 'bussiness_post.bussiness_social_configs', 'bussiness_post.bussiness_social_configs.bussiness_website')->where('is_parent', 0);

        if (! empty($search)) {
            $comments = $comments->where(function ($q) use ($search) {
                $q->where('comment_id', 'LIKE', '%' . $search . '%')->orWhere('post_id', 'LIKE', '%' . $search . '%')->orWhere('message', 'LIKE', '%' . $search . '%')->orWhere('message', 'LIKE', '%' . $search . '%');
            });
        }

        // Adding filter condition for bussiness_post.bussiness_social_configs
        if (! empty($social_config)) {
            $comments = $comments->whereHas('bussiness_post.bussiness_social_configs', function ($query) use ($social_config) {
                // Add your filter conditions for bussiness_post.bussiness_social_configs here
                $query->whereIn('social_configs.platform', $social_config);
            });
        }

        if (! empty($store_website_id)) {
            $comments = $comments->whereHas('bussiness_post.bussiness_social_configs', function ($query) use ($store_website_id) {
                // Add your filter conditions for bussiness_post.bussiness_social_configs here
                $query->whereIn('social_configs.store_website_id', $store_website_id);
            });
        }

        $comments = $comments->orderBy('comment_id', 'DESC')->paginate(25);

        $googleTranslate = new GoogleTranslate();
        $target = 'en';
        foreach ($comments as $key => $value) {
            $translationString = $googleTranslate->translate('en', $value['message']);
            $value['translation'] = $translationString;
        }

        $websites = \App\StoreWebsite::select('id', 'title')->get();
        $socialconfigs = SocialConfig::get();

        $reply_categories = ReplyCategory::select('id', 'name')
            ->with('approval_leads', 'sub_categories')
            ->where('parent_id', 0)
            ->where('id', 44)
            ->orderby('name', 'ASC')->get();

        return view('social-account.allcomment', compact('comments', 'totalcomments', 'socialconfigs', 'websites', 'reply_categories'));
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

        $googleTranslate = new GoogleTranslate();
        $target = $socialConfig['page_language'] ? $socialConfig['page_language'] : 'en';
        $translationString = $googleTranslate->translate($target, $message);
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check config id', ['data' => $configId]);

        try {
            $token = $socialConfig['token'];
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => '']);

            $comment_id = $commentId;
            $reply_message = $translationString;
            $url = 'https://graph.facebook.com/v15.0/' . $comment_id . '/replies';
            $data = ['message' => $reply_message, 'access_token' => $token];
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
            ];

            // Initialize the cURL session
            $ch = curl_init();
            curl_setopt_array($ch, $options);

            // Execute the cURL request and get the response
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            LogRequest::log($startTime, $url, 'POST', json_encode($data), json_decode($response), $httpcode, \App\Http\Controllers\SocialAccountCommentController::class, 'devCommentsReply');

            // Close the cURL session
            curl_close($ch);

            if (isset($response['id'])) {
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

    public function getEmailreplies(Request $request)
    {
        $id = $request->id;
        $emailReplies = Reply::where('category_id', $id)->orderBy('id', 'ASC')->get();

        return json_encode($emailReplies);
    }
}
