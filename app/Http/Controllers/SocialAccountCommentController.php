<?php

namespace App\Http\Controllers;

use App\Reply;
use Carbon\Carbon;
use App\ReplyCategory;
use App\BusinessComment;
use App\GoogleTranslate;
use App\SocialWebhookLog;
use App\Social\SocialPost;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Services\Facebook\FB;
use App\Models\SocialComments;
use Illuminate\Database\Eloquent\Builder;

class SocialAccountCommentController extends Controller
{
    public function index(Request $request, $postId)
    {
        $post = SocialPost::where('id', $postId)->firstOrFail();
        $search = $request->get('search');
        $comments = SocialComments::where('post_id', $post->id)->whereNull('parent_id');
        $comments = $comments->when($request->has('search'), function (Builder $builder) use ($search) {
            return $builder->whereLike(['comment_id', 'message'], $search);
        });

        $comments = $comments->latest()->get();
        $googleTranslate = new GoogleTranslate();
        foreach ($comments as $key => $value) {
            $translationString = $googleTranslate->translate('en', $value['message']);
            $value['translation'] = $translationString;
        }

        return view('social-account.comment', compact('post', 'comments'));
    }

    public function sync($postId)
    {
        $post = SocialPost::where('id', $postId)->with('account')->firstOrFail();
        $fb = new FB($post->account->page_token);
        $comments = $fb->getPostComments($post->ref_post_id);

        foreach ($comments as $comment) {
            $parent = SocialComments::updateOrCreate(['comment_ref_id' => $comment['id']], [
                'commented_by_id' => $comment['from']['id'],
                'commented_by_user' => $comment['from']['name'],
                'post_id' => $post->id,
                'config_id' => $post->account->id,
                'message' => $comment['message'],
                'parent_id' => null,
                'can_comment' => $comment['can_comment'],
                'created_at' => Carbon::parse($comment['created_time']),
            ]);

            if (isset($comment['comments'])) {
                foreach ($comment['comments'] as $c) {
                    SocialComments::updateOrCreate(['comment_ref_id' => $c['id']], [
                        'commented_by_id' => $c['from']['id'],
                        'commented_by_user' => $c['from']['name'],
                        'post_id' => $post->id,
                        'config_id' => $post->account->id,
                        'message' => $c['message'],
                        'parent_id' => $parent->id,
                        'created_at' => Carbon::parse($c['created_time']),
                    ]);
                }
            }
        }

        return redirect()->back()->with('Success', 'Comments synced successfully');
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
        $message = $request->get('input');
        $base_comment = SocialComments::find($commentId);
        $socialConfig = SocialConfig::where('id', $base_comment->config_id)->first();
        try {
            SocialWebhookLog::log(SocialWebhookLog::ERROR, 'Webhook (Comment Error) => Please check log', ['data' => '']);
            $fb = new FB($socialConfig->page_token);
            $response = $fb->replyToPostComments($message, $base_comment->comment_ref_id);
            if (isset($response['id'])) {
                SocialComments::updateOrCreate(['comment_ref_id' => $response['id']], [
                    'post_id' => $base_comment->post_id,
                    'config_id' => $base_comment->config_id,
                    'message' => $message,
                    'parent_id' => $base_comment->id,
                    'user_id' => auth()->user()->id,
                ]);

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
