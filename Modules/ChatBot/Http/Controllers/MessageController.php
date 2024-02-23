<?php

namespace Modules\ChatBot\Http\Controllers;

use App\Task;
use App\Vendor;
use App\Customer;
use App\Supplier;
use App\ChatMessage;
use App\ChatbotCategory;
use App\ChatbotQuestion;
use App\Models\TmpReplay;
use App\SuggestedProduct;
use Illuminate\Http\Request;
use App\ChatbotQuestionReply;
use Illuminate\Http\Response;
use App\ChatbotQuestionExample;
use App\Models\DataTableColumn;
use App\Models\GoogleResponseId;
use Illuminate\Routing\Controller;
use App\Models\GoogleDialogAccount;
use Illuminate\Container\Container;
use App\Elasticsearch\Elasticsearch;
use App\Models\DialogflowEntityType;
use Illuminate\Pagination\Paginator;
use App\Elasticsearch\Reindex\Messages;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Console\Commands\ReindexMessages;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Library\Google\DialogFlow\DialogFlowService;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, $isElastic = null)
    {
        /**
         * Elastic
         */
        $elastic = new Elasticsearch();
        $sizeof = $elastic->count(Messages::INDEX_NAME);

        if (! $isElastic) {
            $isElastic = false;

            return $this->indexDB($request, $isElastic);
        }

        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $queryParam = [];

        if (! empty($search)) {
            $queryParam['multi_match']['query'] = $search;

            $queryParam['multi_match']['fields'][] = 'question';
            $queryParam['multi_match']['fields'][] = 'answer';
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (! empty($unreplied_msg)) {
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $queryParam['match']['approved'] = $status;
        }

        if (request('unread_message') == 'true') {
            $queryParam['match']['is_read'] = 0;
        }

        if (request('message_type') != null) {
            if (request('message_type') == 'email') {
                $queryParam['range']['is_email']['gt'] = 0;
            }
            if (request('message_type') == 'task') {
                $queryParam['range']['task_id']['gt'] = 0;
            }
            if (request('message_type') == 'dev_task') {
                $queryParam['range']['developer_task_id']['gt'] = 0;
            }
            if (request('message_type') == 'ticket') {
                $queryParam['range']['ticket_id']['gt'] = 0;
            }
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            if (in_array('customer', request('search_type'))) {
                $queryParam['range']['customer_id']['gt'] = 0;
            }
            if (in_array('vendor', request('search_type'))) {
                $queryParam['range']['vendor_id']['gt'] = 0;
            }
            if (in_array('supplier', request('search_type'))) {
                $queryParam['range']['supplier_id']['gt'] = 0;
            }
            if (in_array('dev_task', request('search_type'))) {
                $queryParam['range']['developer_task_id']['gt'] = 0;
            }
            if (in_array('task', request('search_type'))) {
                $queryParam['range']['task_id']['gt'] = 0;
            }
        }

        $currentPage = Paginator::resolveCurrentPage();

        $total = $sizeof;

        $body = [];

        if (isset($queryParam['match'])) {
            $body[]['match'] = $queryParam['match'];
        }
        if (isset($queryParam['range'])) {
            $range = [];
            foreach ($queryParam['range'] as $key => $value) {
                $body[]['range'][$key] = $value;
            }
        }
        if (isset($queryParam['multi_match'])) {
            $body[]['multi_match'] = $queryParam['multi_match'];
        }

        $body['exists'] = ['field' => 'message'];

        $response = Elasticsearch::search(
            [
                'index' => Messages::INDEX_NAME,
                'from' => ($currentPage - 1) * 20,
                'size' => 20,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => $body,
                            'should' => [
                                ['exists' => ['field' => 'vendor_id']],
                                ['exists' => ['field' => 'customer_id']],
                                ['exists' => ['field' => 'user_id']],
                                ['exists' => ['field' => 'task_id']],
                                ['exists' => ['field' => 'developer_task_id']],
                                ['exists' => ['field' => 'bug_id']],
                            ],
                            'minimum_should_match' => 1,
                        ],
                    ],
                    'aggs' => [
                        'group_by_customer' => [
                            'terms' => [
                                'field' => 'customer_id',
                                'size' => 1,
                            ],
                            'aggs' => [
                                'group_by_user' => [
                                    'terms' => [
                                        'field' => 'user_id',
                                        'size' => 1,
                                    ],
                                    'aggs' => [
                                        'group_by_vendor' => [
                                            'terms' => [
                                                'field' => 'vendor_id',
                                                'size' => 1,
                                            ],
                                            'aggs' => [
                                                'group_by_supplier' => [
                                                    'terms' => [
                                                        'field' => 'supplier_id',
                                                        'size' => 1,
                                                    ],
                                                    'aggs' => [
                                                        'group_by_task' => [
                                                            'terms' => [
                                                                'field' => 'task_id',
                                                                'size' => 1,
                                                            ],
                                                            'aggs' => [
                                                                'group_by_developer_task' => [
                                                                    'terms' => [
                                                                        'field' => 'developer_task_id',
                                                                        'size' => 1,
                                                                    ],
                                                                    'aggs' => [
                                                                        'group_by_bug' => [
                                                                            'terms' => [
                                                                                'field' => 'bug_id',
                                                                                'size' => 1,
                                                                            ],
                                                                            'aggs' => [
                                                                                'group_by_email' => [
                                                                                    'terms' => [
                                                                                        'field' => 'email_id',
                                                                                        'size' => 1,
                                                                                    ],
                                                                                    'aggs' => [
                                                                                        'max_number' => [
                                                                                            'max' => [
                                                                                                'field' => 'id',
                                                                                            ],
                                                                                        ],
                                                                                    ],
                                                                                ],
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'sort' => [
                        ['id' => 'desc'],
                    ],
                ],
            ]);

        $allItems = $response['hits']['hits'] ?? [];
        $total = $response['hits']['total']['value'] ?? 0;

        $pendingApprovalMsg = array_map(fn ($item) => (new \App\ChatMessage())->setRawAttributes($item['_source']),
            $allItems
        );

        $pendingApprovalMsg = Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            'items' => $pendingApprovalMsg,
            'total' => $total,
            'perPage' => 20,
            'currentPage' => $currentPage,
            'options' => [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        ]);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $currentPage;
        $reply_categories = \App\ReplyCategory::with('approval_leads')->orderby('name')->get();

        if ($request->ajax()) {
            $tml = (string) view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'isElastic'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'chatbot-messages')->first();

        $dynamicColumnsToShowPostman = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowPostman = json_decode($hideColumns, true);
        }

        //dd($pendingApprovalMsg);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'isElastic', 'dynamicColumnsToShowPostman'));
    }

    public function reindex(Request $request)
    {
        $paramFix = $request->get('fix');

        if ($paramFix == 1) {
            Artisan::call('reindex:messages', ['param' => 'fix']);
        }

        if (ReindexMessages::isRunning()) {
            return response()->json(['message' => 'Reindex already started in background.', 'code' => 500], 500);
        }

        Artisan::call('reindex:messages');

        return response()->json(['message' => 'Reindex successful, reload page.', 'code' => 200]);
    }

    public function todayMessagesCheck(Request $request)
    {
        $last_message_id = $request->cmid;
        $tml = '';
        if ($last_message_id) {
        }

        return response()->json(['code' => 200, 'tpl' => $tml]);
    }

    public function todayMessages(Request $request)
    {
        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select(['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator'])
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->paginate(10);
        // dd($pendingApprovalMsg);

        $allCategory = ChatbotCategory::all();

        $pendingApprovalMsg->links();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        $reply_categories = \App\ReplyCategory::with('approval_leads')->orderby('name')->get();

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();
        $totalpage = $pendingApprovalMsg->lastPage();
        if ($request->ajax()) {
            $tml = '';
            foreach ($pendingApprovalMsg as $index => $pam) {
                $tml .= (string) view('chatbot::message.partial.today-list', compact('pam', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents'));
            }

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page, 'totalpage' => $totalpage]);
        }

        //dd($pendingApprovalMsg);
        return view('chatbot::message.today', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'totalpage'));
    }

    public function approve()
    {
        $id = request('id');

        $messageId = 0;

        if ($id > 0) {
            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $id]);

            $chatMEssage = \app\ChatMessage::find($id);

            $type = '';
            if ($chatMEssage->task_id > 0) {
                $type = 'task';
            } elseif ($chatMEssage->developer_tasK_id > 0) {
                $type = 'issue';
            } elseif ($chatMEssage->vendor_id > 0) {
                $type = 'vendor';
            } elseif ($chatMEssage->user_id > 0) {
                $type = 'user';
            } elseif ($chatMEssage->supplier_id > 0) {
                $type = 'supplier';
            } elseif ($chatMEssage->customer_id > 0) {
                $type = 'customer';
            } elseif ($chatMEssage->message_type == 'email') {
                $type = 'email';
                $messageId = $id;
            }

            app(\App\Http\Controllers\WhatsAppController::class)->approveMessage($type, $myRequest, $messageId);
        }

        return response()->json(['code' => 200, 'message' => 'Messsage Send Successfully']);
    }

    /**
     * [removeImages description]
     *
     * @return [type] [description]
     */
    public function removeImages(Request $request)
    {
        $deleteImages = $request->get('delete_images', []);

        if (! empty($deleteImages)) {
            foreach ($deleteImages as $image) {
                [$mediableId, $mediaId] = explode('_', $image);
                if (! empty($mediaId) && ! empty($mediableId)) {
                    \Db::statement('delete from mediables where mediable_id = ? and media_id = ? limit 1', [$mediableId, $mediaId]);
                }
            }
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Image has been removed now']);
    }

    public function uploadAudio(Request $request)
    {
        if ($request->hasFile('audio_data')) {
            $audio_data = $request->file('audio_data');
            $fileOriginalName = $audio_data->getClientOriginalName();
            $path = Storage::disk('uploads')->putFileAs('audio-message', $audio_data, $fileOriginalName);
            $exists_file = Storage::disk('uploads')->exists($path);
            if ($exists_file) {
                $path = Storage::disk('uploads')->url($path);

                return response()->json(['success' => true, 'message' => '', 'url' => $path]);
            } else {
                return response()->json(['success' => false, 'message' => 'The file can not upload to the server']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Requested audio data is not found!']);
    }

    public function attachImages(Request $request)
    {
        $id = $request->get('chat_id', 0);

        $data = [];
        $ids = [];
        $images = [];

        if ($id > 0) {
            // find the chat message
            $chatMessages = ChatMessage::where('id', $id)->first();

            if ($chatMessages) {
                $chatsuggestion = $chatMessages->suggestion;
                if ($chatsuggestion) {
                    $data = SuggestedProduct::attachMoreProducts($chatsuggestion);
                    $code = 500;
                    $message = 'Sorry no images found!';
                    if (count($data) > 0) {
                        $code = 200;
                        $message = 'More images attached Successfully';
                    }

                    return response()->json(['code' => $code, 'data' => $data, 'message' => $message]);
                }
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Sorry , There is not avaialble images']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'It looks like there is not validate id']);
    }

    public function forwardToCustomer(Request $request)
    {
        $customer = $request->get('customer');
        $images = $request->get('images');

        if ($customer > 0 && ! empty($images)) {
            $params = request()->all();
            $params['user_id'] = \Auth::id();
            $params['is_queue'] = 0;
            $params['status'] = \App\ChatMessage::CHAT_MESSAGE_APPROVED;
            $params['customer_ids'] = is_array($customer) ? $customer : [$customer];
            $groupId = \DB::table('chat_messages')->max('group_id');
            $params['group_id'] = ($groupId > 0) ? $groupId + 1 : 1;
            $params['images'] = $images;

            \App\Jobs\SendMessageToCustomer::dispatch($params);
        }

        return response()->json(['code' => 200, 'data' => [], 'message' => 'Message forward to customer(s)']);
    }

    public function resendToBot(Request $request)
    {
        $chatId = $request->get('chat_id');

        if (! empty($chatId)) {
            $chatMessage = \App\ChatMessage::find($chatId);
            if ($chatMessage) {
                $customer = $chatMessage->customer;
                if ($customer) {
                    $params = $chatMessage->getAttributes();

                    \App\Helpers\MessageHelper::whatsAppSend($customer, $chatMessage->message, null, $chatMessage);

                    $data = [
                        'model' => \App\Customer::class,
                        'model_id' => $customer->id,
                        'chat_message_id' => $chatId,
                        'message' => $chatMessage->message,
                        'status' => 'started',
                    ];
                    $chat_message_log_id = \App\ChatbotMessageLog::generateLog($data);
                    $params['chat_message_log_id'] = $chat_message_log_id;
                    \App\Helpers\MessageHelper::sendwatson($customer, $chatMessage->message, null, $chatMessage, $params, false, 'customer');

                    return response()->json(['code' => 200, 'data' => [], 'message' => 'Message sent Successfully']);
                }
            }
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Message not exist in record']);
    }

    public function updateReadStatus(Request $request)
    {
        $chatId = $request->get('chat_id');
        $value = $request->get('value');

        $reply = \App\ChatbotReply::find($chatId);

        if ($reply) {
            $reply->is_read = $value;
            $reply->save();

            $status = ($value == 1) ? 'read' : 'unread';

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Marked as ' . $status]);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'Message not exist in record']);
    }

    public function stopReminder(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');

        if ($type == 'developer_task') {
            $task = \App\DeveloperTask::find($id);
        } else {
            $task = \App\Task::find($id);
        }

        if ($task) {
            $task->frequency = 0;
            $task->save();

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Reminder turned off']);
        }

        return response()->json(['code' => 500, 'data' => [], 'messages' => 'No task found']);
    }

    public function updateEmailAddress(Request $request)
    {
        $chat_id = $request->chat_id;
        $fromemail = $request->fromemail;
        $toemail = $request->toemail;
        $ccemail = $request->ccemail;
        if ($chat_id > 0) {
            ChatMessage::where('id', $chat_id)
                ->update(['from_email' => $fromemail, 'to_email' => $toemail, 'cc_email' => $ccemail]);

            return response()->json(['code' => 200, 'data' => [], 'messages' => 'Record Updated Successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function updateSimulator(Request $request)
    {
        $requestMessage = \Request::create('/', 'GET', [
            'limit' => 1,
            'object' => $request->object,
            'object_id' => $request->objectId,
            'order' => 'asc',
            'for_simulator' => true,
        ]);
        $response = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);
        $id = $request->objectId;
        if ($id > 0) {
            if ($request->object == 'customer') {
                $update_simulator = Customer::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            } elseif ($request->object == 'vendor') {
                $update_simulator = Vendor::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            } elseif ($request->object == 'supplier') {
                $update_simulator = Supplier::where('id', $id)->update(['is_auto_simulator' => $request->auto_simulator]);
            }

            return response()->json(['code' => 200, 'data' => [$update_simulator, $id], 'messages' => 'Auto simulator on successfully']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function chatBotReplayList(Request $request)
    {
        $requestMessage = \Request::create('/', 'GET', [
            'limit' => 20,
            'object' => $request->object,
            'object_id' => $request->object_id,
            'order' => 'asc',
            'plan_response' => true,
        ]);
        $message_list = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);

        return view('chatbot::message.partial.chatbot-list', compact('message_list'));
    }

    public function sendSuggestedMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $tmp_replay_id = $request->get('tmp_reply_id');
        $value = $request->get('value');
        $reply = TmpReplay::find($tmp_replay_id);
        if ($reply) {
            if ($value == 1) {
                $reply['is_approved'] = 1;
            } else {
                $reply['is_reject'] = 1;
            }
            $reply->save();
            if ($value == 1) {
                $requestMessage = \Request::create('/', 'GET', [
                    'limit' => 1,
                    'object' => $reply->type,
                    'object_id' => $reply->type_id,
                    'plan_response' => true,
                ]);
                $lastMessage = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);
                $requestData = [
                    'chat_id' => $reply->chat_message_id,
                    'status' => 2,
                    'add_autocomplete' => false,
                ];
                if ($lastMessage[0]['type'] === 'email') {
                    $requestData['email_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'chatbot') {
                    $requestData['customer_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'task') {
                    $requestData['task_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'issue') {
                    $requestData['issue_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'customer') {
                    $requestData['customer_id'] = $lastMessage[0]['object_type_id'];
                }
                if ($lastMessage[0]['type'] === 'developer_task') {
                    $requestData['developer_task_id'] = $lastMessage[0]['object_type_id'];
                }
                $requestData['message'] = $reply->suggested_replay;
                $requestData = \Request::create('/', 'POST', $requestData);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, $reply->type);
            }
            $status = ($value == 1) ? 'send message Successfully' : 'Suggested message rejected';

            return response()->json(['code' => 200, 'data' => [], 'messages' => $status]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Suggested replay does not exist in record']);
        }
    }

    public function simulatorMessageList(Request $request, $object, $objectId)
    {
        $object = $request->has('object') ? $request->get('object') : $object;
        $objectId = $request->has('object_id') ? $request->get('object_id') : $objectId;
        $objectData = [];
        if ($object == 'customer') {
            $customer = Customer::find($objectId);
            $objectData['type'] = $object;
            $objectData['name'] = $customer['name'];
            $google_accounts = GoogleDialogAccount::with('storeWebsite')->where('site_id', $customer->store_website_id)->first();
            $objectData['url'] = $google_accounts['storeWebsite']['website'];
        } else {
            if ($object == 'vendor') {
                $vendor = Vendor::find($objectId);
                $objectData['type'] = $object;
                $objectData['name'] = $vendor['name'];
            } elseif ($object == 'supplier') {
                $supplier = Supplier::find($objectId);
                $objectData['type'] = $object;
                $objectData['name'] = $supplier['name'];
            }
            $google_accounts = GoogleDialogAccount::with('storeWebsite')->where('default_selected', 1)->first();
            if (empty($google_accounts)) {
                $google_accounts = GoogleDialogAccount::with('storeWebsite')->first();
            }
            $objectData['url'] = $google_accounts ? $google_accounts['storeWebsite']['website'] : '';
        }
        $requestMessage = \Request::create('/', 'GET', [
            'limit' => 1,
            'object' => $object,
            'object_id' => $objectId,
            'order' => 'asc',
            'plan_response' => true,
            'page' => $request->page_no,
        ]);
        $message = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);

        //        if (!isset($message[0])) {
//            return response()->json(['code' => 200, 'data' => null, 'messages' => 'Message completed']);
//        }
        $intent = '';
        $type = '';
        $chatQuestions = [];

        if (! empty($message)) {
            if ($message[0]['inout'] == 'out') {
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', \DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('chatbot_questions.value', 'like', '%' . $message[0]['message'] . '%')->orWhere('cqe.question', 'like', '%' . $message[0]['message'] . '%')
                    ->groupBy('chatbot_questions.id')
                    ->orderBy('chatbot_questions.id', 'desc')
                    ->first();
            } else {
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_questions_reply as cr', 'cr.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', \DB::raw('group_concat(cr.suggested_reply) as `suggested_replies`'))
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('cr.suggested_reply', 'like', '%' . $message[0]['message'] . '%')
                    ->groupBy('chatbot_questions.id')
                    ->orderBy('chatbot_questions.id', 'desc')
                    ->first();
            }

            if ($chatQuestions) {
                $intent = $chatQuestions['value'];
                $type = 'Database';
            } else {
                $dialogFlowService = new DialogFlowService($google_accounts);
                $response = $dialogFlowService->detectIntent(null, $message[0]['message']);
                $intent = $response->getIntent()->getDisplayName();
                $intentName = $response->getIntent()->getName();
                $intentName = explode('/', $intentName);
                $intentName = $intentName[count($intentName) - 1];
                $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                    ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                    ->leftJoin('google_dialog_accounts as ga', 'ga.id', 'chatbot_questions.google_account_id')
                    ->select('chatbot_questions.*', 'ga.*', \DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                    ->where('chatbot_questions.google_account_id', $google_accounts['id'])
                    ->where('chatbot_questions.keyword_or_question', 'intent')
                    ->where('chatbot_questions.google_response_id', $intentName)
                    ->groupBy('chatbot_questions.id')
                    ->orderBy('chatbot_questions.id', 'desc')
                    ->first();
                if (! $chatQuestions) {
                    $chatQuestions = ['value' => $intent, 'id' => null];
                }
                $type = 'google';
            }
        }

        if ($request->request_type == 'ajax') {
            return response()->json(['code' => 200, 'data' => ['message' => $message ? $message[0] : '', 'chatQuestion' => $chatQuestions, 'type' => $type, 'intent' => $intent], 'messages' => 'Get message successfully']);
        }
        $allIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->pluck('value', 'id')->toArray();

        return view('chatbot::message.partial.chatbot', compact('message', 'intent', 'type', 'chatQuestions', 'allIntents', 'objectData'));
    }

    public function storeIntent(Request $request)
    {
        if ($request->question_id > 0) {
            $chatBotQuestion = ChatbotQuestion::where('id', $request->question_id)->first();
            $questionArr = [];
            if ($chatBotQuestion) {
                foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                    $questionArr[] = $question->question;
                }
                $googleAccount = GoogleDialogAccount::where('id', $chatBotQuestion->google_account_id)->first();
                $storeQuestion = new ChatbotQuestionExample();
                $storeQuestion->question = $request->value;
                $storeQuestion->chatbot_question_id = $chatBotQuestion['id'];
                $storeQuestion->save();
                $questionArr[] = $request->value;
                $dialogService = new DialogFlowService($googleAccount);
                try {
                    $response = $dialogService->createIntent([
                        'questions' => $questionArr,
                        'reply' => explode(',', $chatBotQuestion['suggested_reply']),
                        'name' => $chatBotQuestion['value'],
                        'parent' => $chatBotQuestion['parent'],
                    ], $chatBotQuestion->google_response_id ?: null);
                    if ($response) {
                        $chatBotQuestion->google_status = 'google sended';
                        $chatBotQuestion->save();

                        $name = explode('/', $response);
                        $store_response = new GoogleResponseId();
                        $store_response->google_response_id = $name[count($name) - 1];
                        $store_response->google_dialog_account_id = $googleAccount->id;
                        $store_response->chatbot_question_id = $chatBotQuestion->id;
                        $store_response->save();
                    }

                    return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Intent Store successfully']);
                } catch (\Exception $e) {
                    $chatBotQuestion->google_status = $e->getMessage();
                    $chatBotQuestion->save();

                    return response()->json(['code' => 00, 'data' => $chatBotQuestion, 'message' => $e->getMessage()]);
                }
            } else {
                if ($request->object === 'customer') {
                    $customer = Customer::find($request->object_id);
                    $googleAccount = GoogleDialogAccount::where('id', $customer->store_website_id)->first();
                } else {
                    $googleAccount = GoogleDialogAccount::where('default_selected', 1)->first();
                    if (empty($google_accounts)) {
                        $googleAccount = GoogleDialogAccount::with('storeWebsite')->first();
                    }
                }
                $chatBotQuestion = ChatbotQuestion::where('value', $request->question_id)->first();
                if (! $chatBotQuestion) {
                    $chatBotQuestion = new ChatbotQuestion();
                    $chatBotQuestion->keyword_or_question = 'intent';
                    $chatBotQuestion->value = $request->question_id;
                    $chatBotQuestion->auto_approve = 0;
                    $chatBotQuestion->suggested_reply = $request->value;
                    $chatBotQuestion->google_account_id = $googleAccount->id;
                    $chatBotQuestion->is_active = 1;
                    $chatBotQuestion->save();
                }
                $storeQuestion = new ChatbotQuestionExample();
                $storeQuestion->question = $request->value;
                $storeQuestion->chatbot_question_id = $chatBotQuestion->id;
                $storeQuestion->save();
                $questionArr[] = $request->value;
                $dialogService = new DialogFlowService($googleAccount);
                try {
                    $response = $dialogService->createIntent([
                        'questions' => $questionArr,
                        'reply' => explode(',', $chatBotQuestion->suggested_reply),
                        'name' => $chatBotQuestion->value,
                        'parent' => $chatBotQuestion->parent,
                    ], $chatBotQuestion->google_response_id ?: null);

                    if ($response) {
                        $name = explode('/', $response);
                        $chatBotQuestion->google_status = 'google sended';
                        $chatBotQuestion->save();

                        $store_response = new GoogleResponseId();
                        $store_response->google_response_id = $name[count($name) - 1];
                        $store_response->google_dialog_account_id = $googleAccount->id;
                        $store_response->chatbot_question_id = $chatBotQuestion->id;
                        $store_response->save();
                    }

                    return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Intent Store successfully']);
                } catch (\Exception $e) {
                    $chatBotQuestion->google_status = $e->getMessage();
                    $chatBotQuestion->save();
                }

                return response()->json(['code' => 00, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['code' => 00, 'data' => null, 'message' => 'Question not found']);
    }

    public function storeReplay(Request $request)
    {
        if ($request->object === 'customer') {
            $customer = Customer::find($request->object_id);
            $googleAccount = GoogleDialogAccount::where('id', $customer->store_website_id)->first();
        } else {
            $googleAccount = GoogleDialogAccount::where('default_selected', 1)->first();
            if (empty($google_accounts)) {
                $googleAccount = GoogleDialogAccount::with('storeWebsite')->first();
            }
        }
        $chatBotQuestion = ChatbotQuestion::where('id', $request->question_id)->first();
        $questionArr = [];
        $replyArr = [];
        if ($chatBotQuestion) {
            foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                $questionArr[] = $question->question;
            }
            $replyArr = explode(',', $chatBotQuestion->suggested_reply);
            foreach ($chatBotQuestion->chatbotQuestionReplies as $reply) {
                $replyArr[] = $reply->suggested_reply;
            }
            $googleAccount = GoogleDialogAccount::where('id', $chatBotQuestion->google_account_id)->first();
            $chatRply = new  ChatbotQuestionReply();
            $chatRply->suggested_reply = $request->value;
            $chatRply->store_website_id = $googleAccount->site_id;
            $chatRply->chatbot_question_id = $chatBotQuestion->id;
            $chatRply->save();
            $replyArr[] = $request->value;
            $dialogService = new DialogFlowService($googleAccount);
            try {
                $response = $dialogService->createIntent([
                    'questions' => $questionArr,
                    'reply' => $replyArr,
                    'name' => $chatBotQuestion['value'],
                    'parent' => $chatBotQuestion['parent'],
                ], $chatBotQuestion->google_response_id ?: null);
                if ($response) {
                    $name = explode('/', $response);
                    $chatBotQuestion->google_status = 'google sended';
                    $chatBotQuestion->save();

                    $store_response = new GoogleResponseId();
                    $store_response->google_response_id = $name[count($name) - 1];
                    $store_response->google_dialog_account_id = $googleAccount->id;
                    $store_response->chatbot_question_id = $chatBotQuestion->id;
                    $store_response->save();
                }

                return response()->json(['code' => 200, 'data' => $chatBotQuestion, 'message' => 'Reply Stored successfully']);
            } catch (\Exception $e) {
                $chatBotQuestion->google_status = $e->getMessage();
                $chatBotQuestion->save();

                return response()->json(['code' => 400, 'data' => null, 'message' => $e->getMessage()]);
            }
        }

        return response()->json(['code' => 400, 'data' => null, 'message' => 'Question not found']);
    }

    public function chatbotMessagesColumnVisbilityUpdate(Request $request)
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'chatbot-messages')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'chatbot-messages';
            $column->column_name = json_encode($request->column_chatbox);
            $column->save();
        } else {
            $column = new DataTableColumn();
            $column->section_name = 'chatbot-messages';
            $column->column_name = json_encode($request->column_chatbox);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function messagesJson(Request $request)
    {
        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('bug_trackers  as bt', 'bt.id', 'chat_messages.bug_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id')
            ->leftJoin('emails as e', 'e.id', 'chat_messages.email_id')
            ->leftJoin('tmp_replies as tmp', 'tmp.chat_message_id', 'chat_messages.id')
            ->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        if (! empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where('cr.question', 'like', '%' . $search . '%')->orWhere('cr.answer', 'Like', '%' . $search . '%');
            });
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (! empty($unreplied_msg)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where('cm1.message', null);
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where('chat_messages.approved', $status);
            });
        }

        if (request('unread_message') == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->where('cr.is_read', 0);
            });
        }

        if (request('message_type') != null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (request('message_type') == 'email') {
                    $q->where('chat_messages.is_email', '>', 0);
                }
                if (request('message_type') == 'task') {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
                if (request('message_type') == 'dev_task') {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (request('message_type') == 'ticket') {
                    $q->orWhere('chat_messages.ticket_id', '>', 0);
                }
            });
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (in_array('customer', request('search_type'))) {
                    $q->where('chat_messages.customer_id', '>', 0);
                }
                if (in_array('vendor', request('search_type'))) {
                    $q->orWhere('chat_messages.vendor_id', '>', 0);
                }
                if (in_array('supplier', request('search_type'))) {
                    $q->orWhere('chat_messages.supplier_id', '>', 0);
                }
                if (in_array('dev_task', request('search_type'))) {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (in_array('task', request('search_type'))) {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
            });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages LEFT JOIN chatbot_replies as cr on cr.replied_chat_id = `chat_messages`.`id` where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select(['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'tmp.id as tmp_replies_id', 'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject', 'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator'])
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->paginate(20);
        // dd($pendingApprovalMsg);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $pendingApprovalMsg->currentPage();
        $reply_categories = \App\ReplyCategory::with('approval_leads')->orderby('name')->get();

        return response()->json(['code' => 200, 'items' => (array) $pendingApprovalMsg->getIterator(), 'page' => $page]);
    }

    public function indexDB(Request $request, $isElastic)
    {
        $search = request('search');
        $status = request('status');
        $unreplied_msg = request('unreplied_msg'); //Purpose : get unreplied message value - DEVATSK=4350

        $pendingApprovalMsg = ChatMessage::with('taskUser', 'chatBotReplychat', 'chatBotReplychatlatest')
            ->leftjoin('customers as c', 'c.id', 'chat_messages.customer_id')
            ->leftJoin('vendors as v', 'v.id', 'chat_messages.vendor_id')
            ->leftJoin('suppliers as s', 's.id', 'chat_messages.supplier_id')
            ->leftJoin('store_websites as sw', 'sw.id', 'c.store_website_id')
            ->leftJoin('chatbot_replies as cr', 'cr.replied_chat_id', 'chat_messages.id')
            ->leftJoin('chat_messages as cm1', 'cm1.id', 'cr.chat_id');

        if (request('message_type') == 'email') {
            $pendingApprovalMsg->rightJoin('emails as e', 'e.id', 'chat_messages.email_id');
        } else {
            $pendingApprovalMsg->leftJoin('emails as e', 'e.id', 'chat_messages.email_id');
        }

        $pendingApprovalMsg->groupBy(['chat_messages.customer_id', 'chat_messages.vendor_id', 'chat_messages.user_id', 'chat_messages.task_id', 'chat_messages.developer_task_id', 'chat_messages.bug_id', 'chat_messages.email_id']); //Purpose : Add task_id - DEVTASK-4203

        if (! empty($search)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($search) {
                $q->where('cr.question', 'like', '%' . $search . '%')->orWhere('cr.answer', 'Like', '%' . $search . '%');
            });
        }

        //START - Purpose : get unreplied messages - DEVATSK=4350
        if (! empty($unreplied_msg)) {
            $pendingApprovalMsg = $pendingApprovalMsg->where('cm1.message', null);
        }
        //END - DEVATSK=4350

        if (isset($status) && $status !== null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) use ($status) {
                $q->where('chat_messages.approved', $status);
            });
        }

        if (request('unread_message') == 'true') {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                $q->where('cr.is_read', 0);
            });
        }

        if (request('message_type') != null) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (request('message_type') == 'task') {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
                if (request('message_type') == 'dev_task') {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (request('message_type') == 'ticket') {
                    $q->orWhere('chat_messages.ticket_id', '>', 0);
                }
            });
        }
        if (request('search_type') != null and count(request('search_type')) > 0) {
            $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
                if (in_array('customer', request('search_type'))) {
                    $q->where('chat_messages.customer_id', '>', 0);
                }
                if (in_array('vendor', request('search_type'))) {
                    $q->orWhere('chat_messages.vendor_id', '>', 0);
                }
                if (in_array('supplier', request('search_type'))) {
                    $q->orWhere('chat_messages.supplier_id', '>', 0);
                }
                if (in_array('dev_task', request('search_type'))) {
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if (in_array('task', request('search_type'))) {
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
            });
        }

        $pendingApprovalMsg = $pendingApprovalMsg->whereRaw('chat_messages.id in (select max(chat_messages.id) as latest_message from chat_messages where ((customer_id > 0 or vendor_id > 0 or task_id > 0 or developer_task_id > 0 or user_id > 0 or supplier_id > 0 or bug_id > 0 or email_id > 0) OR (customer_id IS NULL
        AND vendor_id IS NULL
        AND supplier_id IS NULL
        AND bug_id IS NULL
        AND task_id IS NULL
        AND developer_task_id IS NULL
        AND email_id IS NULL
        AND user_id IS NULL)) GROUP BY customer_id,user_id,vendor_id,supplier_id,task_id,developer_task_id, bug_id,email_id)');

        $currentPage = Paginator::resolveCurrentPage();
        $select = ['cr.id as chat_bot_id', 'cr.is_read as chat_read_id', 'chat_messages.*', 'cm1.id as chat_id', 'cr.question',
            'cm1.message as answer', 'cm1.is_audio as answer_is_audio', 'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name',
            'c.is_auto_simulator as customer_auto_simulator',
            'v.is_auto_simulator as vendor_auto_simulator', 's.is_auto_simulator as supplier_auto_simulator'];
        $pendingApprovalMsg = $pendingApprovalMsg->where(function ($q) {
            $q->where('chat_messages.message', '!=', '');
        })->select($select)
            ->orderByRaw('cr.id DESC, chat_messages.id DESC')
            ->offset(($currentPage - 1) * 20)->limit(20);

        $total = 3000000;

        $messages = $pendingApprovalMsg->select([...$select])->get($select);

        $pendingApprovalMsg = Container::getInstance()->makeWith(LengthAwarePaginator::class, [
            'items' => $messages,
            'total' => $total,
            'perPage' => 20,
            'currentPage' => $currentPage,
            'options' => [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        ]);

        $allCategory = ChatbotCategory::all();
        $allCategoryList = [];
        if (! $allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ['id' => $all->id, 'text' => $all->name];
            }
        }
        $page = $currentPage;
        $reply_categories = \App\ReplyCategory::with('approval_leads')->orderby('name')->get();

        if ($request->ajax()) {
            $tml = (string) view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'isElastic'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();
        $variables = DialogFlowService::VARIABLES;
        $parentIntents = ChatbotQuestion::where(['keyword_or_question' => 'intent'])->where('google_account_id', '>', 0)
            ->pluck('value', 'id')->toArray();

        //dd($pendingApprovalMsg);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType', 'variables', 'parentIntents', 'isElastic'));
    }
}
