<?php

namespace App\Http\Controllers;

use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\ChatbotQuestionReply;
use App\Reply;
use App\ReplyCategory;
use App\ReplyUpdateHistory;
use App\Setting;
use App\StoreWebsitePage;
use App\WatsonAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReplyController extends Controller
{
    public function __construct()
    {
        //  $this->middleware('permission:reply-edit',[ 'only' => 'index','create','store','destroy','update','edit']);
    }

    public function index(Request $request)
    {
        $reply_categories = ReplyCategory::all();

        $replies = Reply::oldest();

        if (! empty($request->keyword)) {
            $replies->where('reply', 'LIKE', '%'.$request->keyword.'%');
        }

        if (! empty($request->category_id)) {
            $replies->where('category_id', $request->category_id);
        }

        $replies = $replies->paginate(Setting::get('pagination'));

        return view('reply.index', compact('replies', 'reply_categories'))
        ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['reply'] = '';
        $data['model'] = '';
        $data['category_id'] = '';
        $data['modify'] = 0;
        $data['reply_categories'] = ReplyCategory::all();

        return view('reply.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Reply $reply)
    {
        $this->validate($request, [
            'reply' => 'required|string',
            'category_id' => 'required|numeric',
            'model' => 'required',
        ]);

        $data = $request->except('_token', '_method');
        $data['reply'] = trim($data['reply']);
        $reply->create($data);

        if ($request->ajax()) {
            return response()->json(trim($request->reply));
        }

        return redirect()->route('reply.index')->with('success', 'Quick Reply added successfully');
    }

    public function categoryStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $category = new ReplyCategory;
        $category->name = $request->name;
        $category->save();

        return redirect()->route('reply.index')->with('success', 'You have successfully created category');
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
    public function edit(Reply $reply)
    {
        $data = $reply->toArray();
        $data['modify'] = 1;
        $data['reply_categories'] = ReplyCategory::all();

        return view('reply.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
        $this->validate($request, [
            'reply' => 'required|string',
            'model' => 'required',
        ]);

        $data = $request->except('_token', '_method');

        $reply->update($data);

        return redirect()->route('reply.index')->with('success', 'Quick Reply updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply, Request $request)
    {
        $reply->delete();
        if ($request->ajax()) {
            return response()->json(['message' => 'Deleted successfully']);
        }

        return redirect()->route('reply.index')->with('success', 'Quick Reply Deleted successfully');
    }

    public function chatBotQuestion(Request $request)
    {
        $this->validate($request, [
            'intent_name' => 'required',
            'intent_reply' => 'required',
            'question' => 'required',
        ]);

        $ChatbotQuestion = null;
        $example = ChatbotQuestionExample::where('question', $request->question)->first();
        if ($example) {
            return response()->json(['message' => 'User intent is already available']);
        }

        if (is_numeric($request->intent_name)) {
            $ChatbotQuestion = ChatbotQuestion::where('id', $request->intent_name)->first();
        } else {
            if ($request->intent_name != '') {
                $ChatbotQuestion = ChatbotQuestion::create([
                    'value' => str_replace(' ', '_', preg_replace('/\s+/', ' ', $request->intent_name)),
                ]);
            }
        }
        $ChatbotQuestion->suggested_reply = $request->intent_reply;
        $ChatbotQuestion->category_id = $request->intent_category_id;
        $ChatbotQuestion->keyword_or_question = 'intent';
        $ChatbotQuestion->is_active = 1;
        $ChatbotQuestion->erp_or_watson = 'erp';
        $ChatbotQuestion->auto_approve = 1;
        $ChatbotQuestion->save();

        $ex = new ChatbotQuestionExample;
        $ex->question = $request->question;
        $ex->chatbot_question_id = $ChatbotQuestion->id;
        $ex->save();

        $wotson_account_website_ids = WatsonAccount::get()->pluck('store_website_id')->toArray();

        $data_to_insert = [];

        foreach ($wotson_account_website_ids as $id_) {
            $data_to_insert[] = [
                'chatbot_question_id' => $ChatbotQuestion->id,
                'store_website_id' => $id_,
                'suggested_reply' => $request->intent_reply,
            ];
        }

        ChatbotQuestionReply::insert($data_to_insert);
        Reply::where('id', $request->intent_reply_id)->delete();

        return response()->json(['message' => 'Successfully created', 'code' => 200]);
    }

    public function replyList(Request $request)
    {
        $storeWebsite = $request->get('store_website_id');
        $keyword = $request->get('keyword');
        $category = $request->get('category_id');
        $categoryChildNode = [];
        if ($category) {
            $parentNode = ReplyCategory::where('id', '=', $category)->where('parent_id', '=', 0)->first();
            if ($parentNode) {
                $subCatChild = ReplyCategory::where('parent_id', $parentNode->id)->get()->pluck('id')->toArray();
                $categoryChildNode = ReplyCategory::whereIn('parent_id', $subCatChild)->get()->pluck('id')->toArray();
            }
        }

        $replies = \App\ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
        ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
        ->where('model', 'Store Website')
        ->select(['replies.*', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id']);

        if ($storeWebsite > 0) {
            $replies = $replies->where('replies.store_website_id', $storeWebsite);
        }

        if (! empty($keyword)) {
            $replies = $replies->where(function ($q) use ($keyword) {
                $q->orWhere('reply_categories.name', 'LIKE', '%'.$keyword.'%')->orWhere('replies.reply', 'LIKE', '%'.$keyword.'%');
            });
        }
        if (! empty($category)) {
            if ($categoryChildNode) {
                $replies = $replies->where(function ($q) use ($categoryChildNode) {
                    $q->orWhereIn('reply_categories.id', $categoryChildNode);
                });
            } else {
                $replies = $replies->where(function ($q) use ($category) {
                    $q->orWhere('reply_categories.id', '=', $category)->orWhere('reply_categories.parent_id', '=', $category);
                });
            }
        }
        $replies = $replies->paginate(25);
        foreach ($replies as $key => $value) {
            $subCat = explode('>', $value->parentList());
            $replies[$key]['parent_first'] = isset($subCat[0]) ? $subCat[0] : '';
            $replies[$key]['parent_secound'] = isset($subCat[1]) ? $subCat[1] : '';
        }

        return view('reply.list', compact('replies'));
    }

    public function replyListDelete(Request $request)
    {
        $id = $request->get('id');
        $record = \App\ReplyCategory::find($id);

        if ($record) {
            $replies = $record->replies;
            if (! $replies->isEmpty()) {
                foreach ($replies as $re) {
                    $re->delete();
                }
            }
            $record->delete();
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Record deleted successfully']);
    }

    public function replyUpdate(Request $request)
    {
        $id = $request->get('id');
        $reply = \App\Reply::find($id);

        $replies = Reply::where('id', $id)->first();
        $ReplyUpdateHistory = new ReplyUpdateHistory;
        $ReplyUpdateHistory->last_message = $replies->reply;
        $ReplyUpdateHistory->reply_id = $replies->id;
        $ReplyUpdateHistory->user_id = Auth::id();
        $ReplyUpdateHistory->save();

        if ($reply) {
            $reply->reply = $request->reply;
            $reply->pushed_to_watson = 0;
            $reply->save();

            $replyCategory = \App\ReplyCategory::find($reply->category_id);

            $replyCategories = $replyCategory->parentList();
            $cats = explode('>', str_replace(' ', '', $replyCategories));
            if (isset($cats[0]) and $cats[0] == 'FAQ') {
                $faqCat = \App\ReplyCategory::where('name', 'FAQ')->pluck('id')->first();
                if ($faqCat != null) {
                    $faqToPush = '<div class="cls_shipping_panelmain">';
                    $topParents = \App\ReplyCategory::where('parent_id', $faqCat)->get();
                    foreach ($topParents as $topParent) {
                        $faqToPush .= '<div class="cls_shipping_panelsub">
						<div id="shopPlaceOrder" class="accordion_head" role="tab">
							<h4 class="panel-title"><a role="button" href="javascript:;" class="cls_abtn"> '.$topParent['name'].' </a><span class="plusminus">-</span></h4>
						</div> <div class="accordion_body" style="display: block;">';
                        $questions = \App\ReplyCategory::where('parent_id', $topParent['id'])->get();
                        foreach ($questions as $question) {
                            $answer = Reply::where('category_id', $question['id'])->first();
                            if ($answer != null) {
                                $faqToPush .= '<p class="md-paragraph"><strong>'.$question['name'].'</strong></p>
									<p class="md-paragraph"> '.$answer['reply'].' </p>';
                            }
                        }
                        $faqToPush .= '</div></div>';
                    }
                    $faqToPush .= '</div>';
                    $faqPage = StoreWebsitePage::where(['store_website_id' => $reply->store_website_id, 'url_key' => 'faqs'])->first();
                    if ($faqPage == null) {
                        echo 'if';
                        $a = StoreWebsitePage::create(['title' => 'faqs', 'content' => $faqToPush, 'store_website_id' => $reply->store_website_id, 'url_key' => 'faqs', 'is_pushed' => 0]);
                    } else {
                        echo 'else';
                        $a = StoreWebsitePage::where('id', $faqPage->id)->update(['content' => $faqToPush, 'is_pushed' => 0]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Quick Reply Updated successfully');
    }

    public function getReplyedHistory(Request $request)
    {
        $id = $request->id;
        $reply_histories = DB::select(DB::raw('SELECT reply_update_histories.id,reply_update_histories.reply_id,reply_update_histories.user_id,reply_update_histories.last_message,reply_update_histories.created_at,users.name FROM `reply_update_histories` JOIN `users` ON users.id = reply_update_histories.user_id where reply_update_histories.reply_id = '.$id));

        return response()->json(['histories' => $reply_histories]);
    }
}
