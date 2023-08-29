<?php

namespace App\Http\Controllers;

use App\User;
use App\Reply;
use App\Setting;
use App\ReplyCategory;
use App\WatsonAccount;
use App\ChatbotQuestion;
use App\Models\ReplyLog;
use App\StoreWebsitePage;
use App\TranslateReplies;
use App\ReplyUpdateHistory;
use Illuminate\Http\Request;
use App\ChatbotQuestionReply;
use App\ReplyTranslatorStatus;
use App\ChatbotQuestionExample;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessTranslateReply;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Auth;
use App\Models\QuickRepliesPermissions;
use App\Models\RepliesTranslatorHistory;

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
            $replies->where('reply', 'LIKE', '%' . $request->keyword . '%');
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

    public function categorySetDefault(Request $request)
    {
        if ($request->has('model') && $request->has('cat_id')) {
            $model = $request->model;
            $cat_id = $request->cat_id;
            $ReplyCategory = \App\ReplyCategory::find($cat_id);
            if ($ReplyCategory) {
                $ReplyCategory->default_for = $model;
                $ReplyCategory->save();

                return response()->json(['success' => true, 'message' => 'Category Assignments Successfully']);
            }

            return response()->json(['success' => false, 'message' => 'The Reply Category data was not found']);
        }

        return response()->json(['success' => false, 'message' => 'The requested data was not found']);
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

        $reply->is_pushed = 0;
        $reply->update($data);

        (new \App\Models\ReplyLog)->addToLog($reply->id, 'System updated FAQ', 'Updated');

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

    public function removepermissions(Request $request)
    {
        if ($request->type == 'remove_permission') {
            $edit_data = QuickRepliesPermissions::where('user_id', $request->user_permission_id)->whereNotIn('lang_id', $request->edit_lang_name)->where('action', 'edit')->get();
            $view_data = QuickRepliesPermissions::where('user_id', $request->user_permission_id)->whereNotIn('lang_id', $request->view_lang_name)->where('action', 'view')->get();

            foreach ($edit_data as $edit_lang) {
                $edit_lang->delete();
            }

            foreach ($view_data as $view_lang) {
                $view_lang->delete();
            }

            return redirect()->back()->with('success', 'Remove Permission successfully');
        } else {
            $checkExists = QuickRepliesPermissions::where('user_id', $request->id)->get();
            $edit_lang = [];
            $view_lang = [];
            foreach ($checkExists as $checkExist) {
                if ($checkExist->action == 'edit') {
                    $edit_lang[] = $checkExist->lang_id;
                }
                if ($checkExist->action == 'view') {
                    $view_lang[] = $checkExist->lang_id;
                }
            }

            $data = [
                'edit_lang' => $edit_lang,
                'view_lang' => $view_lang,
                'status' => '200',
            ];

            return $data;
        }
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
        // dd('hii');
        $storeWebsite = $request->get('store_website_id');
        $keyword = $request->get('keyword');
        $parent_category = $request->get('parent_category_ids') ? $request->get('parent_category_ids') : [];
        $category_ids = $request->get('category_ids') ? $request->get('category_ids') : [];
        $sub_category_ids = $request->get('sub_category_ids') ? $request->get('sub_category_ids') : [];

        $categoryChildNode = [];
        if ($parent_category) {
            $parentNode = ReplyCategory::select(\DB::raw('group_concat(id) as ids'))->whereIn('id', $parent_category)->where('parent_id', '=', 0)->first();
            if ($parentNode) {
                $subCatChild = ReplyCategory::whereIn('parent_id', explode(',', $parentNode->ids))->get()->pluck('id')->toArray();
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
                $q->orWhere('reply_categories.name', 'LIKE', '%' . $keyword . '%')->orWhere('replies.reply', 'LIKE', '%' . $keyword . '%');
            });
        }
        if (! empty($parent_category)) {
            if ($categoryChildNode) {
                $replies = $replies->where(function ($q) use ($categoryChildNode) {
                    $q->orWhereIn('reply_categories.id', $categoryChildNode);
                });
            } else {
                $replies = $replies->where(function ($q) use ($parent_category) {
                    $q->orWhereIn('reply_categories.id', $parent_category)->where('reply_categories.parent_id', '=', 0);
                });
            }
        }

        if (! empty($category_ids)) {
            $replies = $replies->where(function ($q) use ($category_ids) {
                $q->orWhereIn('reply_categories.parent_id', $category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        if (! empty($sub_category_ids)) {
            $replies = $replies->where(function ($q) use ($sub_category_ids) {
                $q->orWhereIn('reply_categories.id', $sub_category_ids)->where('reply_categories.parent_id', '!=', 0);
            });
        }

        $replies = $replies->paginate(25);
        foreach ($replies as $key => $value) {
            $subCat = explode('>', $value->parentList());
            $replies[$key]['parent_first'] = isset($subCat[0]) ? $subCat[0] : '';
            $replies[$key]['parent_secound'] = isset($subCat[1]) ? $subCat[1] : '';
        }

        $parentCategory = $allSubCategory = [];
        $parentCategory = ReplyCategory::where('parent_id', 0)->get();
        $allSubCategory = ReplyCategory::where('parent_id', '!=', 0)->get();
        $category = $subCategory = [];
        foreach ($allSubCategory as $key => $value) {
            $categoryList = ReplyCategory::where('id', $value->parent_id)->first();
            if ($categoryList->parent_id == 0) {
                $category[$value->id] = $value->name;
            } else {
                $subCategory[$value->id] = $value->name;
            }
        }

        return view('reply.list', compact('replies', 'parentCategory', 'category', 'subCategory', 'parent_category', 'category_ids', 'sub_category_ids'));
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
							<h4 class="panel-title"><a role="button" href="javascript:;" class="cls_abtn"> ' . $topParent['name'] . ' </a><span class="plusminus">-</span></h4>
						</div> <div class="accordion_body" style="display: block;">';
                        $questions = \App\ReplyCategory::where('parent_id', $topParent['id'])->get();
                        foreach ($questions as $question) {
                            $answer = Reply::where('category_id', $question['id'])->first();
                            if ($answer != null) {
                                $faqToPush .= '<p class="md-paragraph"><strong>' . $question['name'] . '</strong></p>
									<p class="md-paragraph"> ' . $answer['reply'] . ' </p>';
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
        $reply_histories = DB::select(DB::raw('SELECT reply_update_histories.id,reply_update_histories.reply_id,reply_update_histories.user_id,reply_update_histories.last_message,reply_update_histories.created_at,users.name FROM `reply_update_histories` JOIN `users` ON users.id = reply_update_histories.user_id where reply_update_histories.reply_id = ' . $id));

        return response()->json(['histories' => $reply_histories]);
    }

    public function replyTranslate(Request $request)
    {
        $id = $request->reply_id;
        $is_flagged_request = $request->is_flagged;

        if ($is_flagged_request == '1') {
            $is_flagged = 0;
        } else {
            $is_flagged = 1;
        }

        if ($is_flagged == '1') {
            $record = \App\Reply::find($id);
            if ($record) {
                ProcessTranslateReply::dispatch($record, \Auth::id())->onQueue('replytranslation');

                $record->is_flagged = 1;
                $record->save();

                return response()->json(['code' => 200, 'data' => [], 'message' => 'Replies Set For Translatation']);
            }

            return response()->json(['code' => 400, 'data' => [], 'message' => 'There is a problem while translating']);
        } else {
            $res_rec = \App\Reply::find($id);
            $res_rec->is_flagged = 0;
            $res_rec->save();

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Translation off successfully']);
        }
    }

    public function replyTranslateList(Request $request)
    {
        $storeWebsite = $request->get('store_website_id');
        $lang = $request->get('lang');
        $keyword = $request->get('keyword');

        $replies = \App\TranslateReplies::join('replies', 'translate_replies.replies_id', 'replies.id')
        ->leftJoin('store_websites as sw', 'sw.id', 'replies.store_website_id')
        ->leftJoin('reply_categories', 'reply_categories.id', 'replies.category_id')
        ->where('model', 'Store Website')->where('replies.is_flagged', '1')
        ->select(['replies.*', 'translate_replies.status', 'translate_replies.replies_id as replies_id', 'replies.reply as original_text', 'sw.website', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id', 'translate_replies.id as id', 'translate_replies.translate_from', 'translate_replies.translate_to', 'translate_replies.translate_text', 'translate_replies.created_at', 'translate_replies.updated_at']);


        $getLangs =  \App\TranslateReplies::Select('id','translate_to')->get();

        if ($storeWebsite > 0) {
            $replies = $replies->where('replies.store_website_id', $storeWebsite);
        }

        if (! empty($keyword)) {
            $replies = $replies->where(function ($q) use ($keyword) {
                $q->orWhere('reply_categories.name', 'LIKE', '%' . $keyword . '%')->orWhere('replies.reply', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($lang) {
            $replies = $replies->where('translate_replies.translate_to', $lang);
        }

        $replies = $replies->get();

        $lang = [];
        $original_text = [];
        $ids = [];
        $translate_text = [];
        foreach ($replies as  $replie) {
            if (! in_array($replie->replies_id, $ids)) {
                $ids[] = $replie->replies_id;
                $translate_text[$replie->replies_id] = [
                    'id' => $replie->id,
                    'website' => $replie->website,
                    'category_name' => $replie->category_name,
                    'translate_from' => $replie->translate_from,
                    'original_text' => $replie->original_text,
                    'translate_text' => [[$replie->translate_to => $replie->translate_text]],
                    'translate_lang' => [$replie->translate_to],
                    'translate_id' => [$replie->id],
                    'translate_status' => [$replie->status],
                    'translate_status_color' => [$replie->status_color],
                    'created_at' => $replie->created_at,
                    'updated_at' => $replie->updated_at,
                ];
            } else {
                array_push($translate_text[$replie->replies_id]['translate_text'], [$replie->translate_to => $replie->translate_text]);
                array_push($translate_text[$replie->replies_id]['translate_lang'], $replie->translate_to);
                array_push($translate_text[$replie->replies_id]['translate_id'], $replie->id);
                array_push($translate_text[$replie->replies_id]['translate_status'], $replie->status);
                array_push($translate_text[$replie->replies_id]['translate_status_color'], $replie->status_color);
            }

            if (! in_array($replie->translate_to, $lang)) {
                $lang[$replie->id] = $replie['translate_to'];
            }
        }

        $replies = json_encode($translate_text);

        $replyTranslatorStatuses = ReplyTranslatorStatus::all();

        return view('reply.translate-list', compact('replies', 'lang', 'replyTranslatorStatuses','getLangs'))->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function quickRepliesPermissions(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->only('user_id', 'lang_id', 'action');
            $checkExists = QuickRepliesPermissions::where('user_id', $data['user_id'])->where('lang_id', $data['lang_id'])->where('action', $data['action'])->first();

            if ($checkExists) {
                return response()->json(['status' => 412]);
            }

            QuickRepliesPermissions::insert($data);
            $data = QuickRepliesPermissions::where('user_id', \Auth::user()->id)->get();

            return response()->json(['status' => 200]);
        }
    }

    public function replyTranslateUpdate(Request $request)
    {
        $record = TranslateReplies::find($request->record_id);
        $oldRecord = $request->lang_id;
        if ($record) {
            $record->updated_by_user_id = ! empty($request->update_by_user_id) ? $request->update_by_user_id : '';
            $record->translate_text = ! empty($request->update_record) ? $request->update_record : '';
            $record->status = 'new';
            $record->update();

            $historyData = [];
            $historyData['translate_replies_id'] = $record->id;
            $historyData['updated_by_user_id'] = $record->updated_by_user_id;
            $historyData['translate_text'] = $request->update_record;
            $historyData['status'] = 'new';
            $historyData['lang'] = $oldRecord;
            $historyData['created_at'] = \Carbon\Carbon::now();
            RepliesTranslatorHistory::insert($historyData);

            return redirect()->back()->with(['success' => 'Successfully Updated']);
        } else {
            return redirect()->back()->withErrors('Something Wrong');
        }
    }

    public function replyTranslatehistory(Request $request)
    {
        $key = $request->key;
        $language = $request->language;
        if ($request->type == 'all_view') {
            $history = RepliesTranslatorHistory::whereRaw('status is not null')->get();
        } else {
            $history = RepliesTranslatorHistory::where([
                'translate_replies_id' => $request->id,
                'lang' => $language,
            ])->whereRaw('status is not null')->get();
        }
        if (count($history) > 0) {
            foreach ($history as $key => $historyData) {
                $history[$key]['updater'] = User::where('id', $historyData['updated_by_user_id'])->pluck('name')->first();
                $history[$key]['approver'] = User::where('id', $historyData['approved_by_user_id'])->pluck('name')->first();
            }
        }
        $html = '';
        foreach ($history as $key => $value) {
            $ar = ($value->lang == 'ar') ? $value->translate_text : '';
            $en = ($value->lang == 'en') ? $value->translate_text : '';
            $zh = ($value->lang == 'zh-CN') ? $value->translate_text : '';
            $ja = ($value->lang == 'ja') ? $value->translate_text : '';
            $ko = ($value->lang == 'ko') ? $value->translate_text : '';
            $ur = ($value->lang == 'ur') ? $value->translate_text : '';
            $ru = ($value->lang == 'ru') ? $value->translate_text : '';
            $it = ($value->lang == 'it') ? $value->translate_text : '';
            $fr = ($value->lang == 'fr') ? $value->translate_text : '';
            $es = ($value->lang == 'es') ? $value->translate_text : '';
            $nl = ($value->lang == 'nl') ? $value->translate_text : '';
            $de = ($value->lang == 'de') ? $value->translate_text : '';

            $html .= '<tr><td>' . $value->id . '</td>';
            // $html .= '<td>scrollToSeeMoreImages</td>';
            // $html .= '<td>' . $ar . '</td>';
            // $html .= '<td>' . $zh . '</td>';
            // $html .= '<td>' . $ja . '</td>';
            // $html .= '<td>' . $ko . '</td>';
            // $html .= '<td>' . $ur . '</td>';
            // $html .= '<td>' . $ru . '</td>';
            // $html .= '<td>' . $it . '</td>';
            // $html .= '<td>' . $fr . '</td>';
            // $html .= '<td>' . $es . '</td>';
            // $html .= '<td>' . $de . '</td>';
            // $html .= '<td>' . $en . '</td>';
            // $html .= '<td>' . $nl . '</td>';
            $html .= '<td>' . $value->lang . '</td>';
            $html .= '<td>' . $value->translate_text . '</td>';
            $html .= '<td>' . $value->status . '</td>';
            $html .= '<td>' . $value->updater . '</td>';
            $html .= '<td>' . $value->approver . '</td>';
            $html .= '<td>' . $value->created_at . '</td>';
            $html .= '</tr>';
        }

        return response()->json(['status' => 200, 'data' => $html]);
    }

    public function approvedByAdmin(Request $request)
    {
        $record = TranslateReplies::where('id', $request->id)->first();
        $record['status'] = $request->status;
        $record['approved_by_user_id'] = \Auth::user()->id;
        $record->update();

        $record_history = RepliesTranslatorHistory::where('translate_replies_id', $request->id)->where('lang', $request->lang)->orderBy('id', 'desc')->first();
        $record_history['status'] = $request->status;
        $record_history['approved_by_user_id'] = \Auth::user()->id;
        $record_history->update();

        return response()->json(['status' => 200]);
    }

    public function show_logs(Request $request, ReplyLog $ReplyLog)
    {
        $data = $request->all();

        $data = $ReplyLog->where('reply_id', $data['id'])->orderby('created_at', 'desc')->paginate(20);
        $paginateHtml = $data->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $data, 'message' => 'Logs found']);
    }

    public function replyLogList(Request $request)
    {
        $replyLogs = new  ReplyLog();

        $replyLogs = $replyLogs->latest()->paginate(\App\Setting::get('pagination', 25));

        return view('reply.log-reply', compact('replyLogs'));
    }

    public function replyMulitiple(Request $request)
    {
        $replyIds = $request->input('reply_ids');

        $replyIdsArray = explode(',', $replyIds);

        foreach ($replyIdsArray as $replyId) {
            $replyLog = Reply::find($replyId);
            if ($replyLog) {
                $replyLog->is_flagged = 1;
                $replyLog->save();
            }
        }

        return response()->json(['message' => 'Flag Added successfully']);
    }

    public function statusColor(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $cronStatus = ReplyTranslatorStatus::find($key);
            $cronStatus->color = $value;
            $cronStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
}
