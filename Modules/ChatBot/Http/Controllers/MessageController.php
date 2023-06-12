<?php

namespace Modules\ChatBot\Http\Controllers;

use App\BugTracker;
use App\ChatMessage;
use App\ChatbotCategory;
use App\Customer;
use App\CustomerCharity;
use App\DeveloperTask;
use App\Document;
use App\Email;
use App\Learning;
use App\Models\DialogflowEntityType;
use App\Models\TmpReplay;
use App\Old;
use App\Order;
use App\PaymentReceipt;
use App\PublicKey;
use App\SiteDevelopment;
use App\SocialStrategy;
use App\StoreSocialContent;
use App\SuggestedProduct;
use App\Supplier;
use App\Task;
use App\TestCase;
use App\TestSuites;
use App\Tickets;
use App\Uicheck;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
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
                if(request('message_type')=='email'){
                    $q->where('chat_messages.message_type', 'email');
                    $q->orWhere('chat_messages.is_email', '>', 0);
                }
                if(request('message_type')=='task'){
                    $q->orWhere('chat_messages.task_id', '>', 0);
                }
                if(request('message_type')=='dev_task'){
                    $q->orWhere('chat_messages.developer_task_id', '>', 0);
                }
                if(request('message_type')=='ticket'){
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
            'cm1.message as answer',
            'c.name as customer_name', 'v.name as vendors_name', 's.supplier as supplier_name', 'cr.reply_from', 'sw.title as website_title', 'c.do_not_disturb as customer_do_not_disturb', 'e.name as from_name', 'tmp.id as tmp_replies_id' ,'tmp.suggested_replay', 'tmp.is_approved', 'tmp.is_reject'])
            ->orderByRaw("cr.id DESC, chat_messages.id DESC")
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

        if ($request->ajax()) {
            $tml = (string) view('chatbot::message.partial.list', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        $allEntityType = DialogflowEntityType::all()->pluck('name', 'id')->toArray();

        //dd($pendingApprovalMsg);
        return view('chatbot::message.index', compact('pendingApprovalMsg', 'page', 'allCategoryList', 'reply_categories', 'allEntityType'));
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

    public function updateSimulator(Request $request){
        $id = $request->get('id');
        $user_id = $request->get('user_id');
        $send_to_user_id = $request->get('send_to_user_id');
        $auto_simulator = $request->get('auto_simulator');
        if ($id > 0) {
            $chat_message = ChatMessage::where(function ($query) use ($user_id, $send_to_user_id) {
                $query->where('user_id', $user_id)
                    ->where('sent_to_user_id', $send_to_user_id);
            })->orWhere(function ($query) use ($user_id, $send_to_user_id) {
                $query->where('user_id', $user_id)
                    ->where('sent_to_user_id', $send_to_user_id);
            })->orderBy('id', 'asc')->first();

            $update_chat_message = ChatMessage::where('id', $chat_message['id'])->update(['is_auto_simulator' => $auto_simulator]);

            return response()->json(['code' => 200, 'data' => [$update_chat_message, $chat_message['id']], 'messages' => 'Auto simulator on successfully']);

        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Error']);
        }
    }

    public function chatBotReplayList(Request $request) {

        die('------------------');
            // Set variables
            $limit = $request->get('limit', 3);
            $loadAttached = $request->get('load_attached', 0);
            $loadAllMessages = $request->get('load_all', 0);
            // Get object (customer, vendor, etc.)
            switch ($request->object) {
                case 'customer':
                    $object = Customer::find($request->object_id);
                    break;
                case 'user-feedback':
                    $object = User::find($request->object_id);
                    break;
                case 'user-feedback-hrTicket':
                    $object = User::find($request->object_id);
                    break;
                case 'hubstuff':
                    $object = User::find($request->object_id);
                    break;
                case 'user':
                    $object = User::find($request->object_id);
                    break;
                case 'vendor':
                    $object = Vendor::find($request->object_id);
                    break;
                case 'charity':
                    $object = CustomerCharity::find($request->object_id);
                    break;
                case 'task':
                    $object = Task::find($request->object_id);
                    break;
                case 'ticket':
                    $object = Tickets::find($request->object_id);
                    break;
                case 'developer_task':
                    $object = DeveloperTask::find($request->object_id);
                    break;
                case 'supplier':
                    $object = Supplier::find($request->object_id);
                    break;
                case 'old':
                    $object = Old::find($request->object_id);
                    break;
                case 'site_development':
                    $object = SiteDevelopment::find($request->object_id);
                    break;
                case 'site_development':
                    $object = SiteDevelopment::find($request->object_id);
                    break;
                case 'social_strategy':
                    $object = SocialStrategy::find($request->object_id);
                    break;
                case 'content_management':
                    $object = StoreSocialContent::find($request->object_id);
                    break;
                case 'order':
                    $object = Order::find($request->object_id);
                    break;
                case 'payment-receipts':
                    $object = PaymentReceipt::find($request->object_id);
                    break;
                //START - Purpose - Add learning - DEVTASK-4020
                case 'learning':
                    $object = Learning::find($request->object_id);
                    break;
                //END - DEVTASK-4020
                case 'SOP':
                    $object = User::find($request->object_id);
                    break;
                // no break
                case 'document' :
                    $object = Document::find($request->object_id);
                    break;
                case 'uicheck' :
                    $object = Uicheck::find($request->object_id);
                    //dd($object);
                    break;
                case 'bug' :
                    $object = BugTracker::find($request->object_id);
                    //dd($object);
                    break;
                case 'testcase' :
                    $object = TestCase::find($request->object_id);
                    //dd($object);
                    break;
                case 'testsuites' :
                    $object = TestSuites::find($request->object_id);
                    //dd($object);
                    break;
                case 'timedoctor':
                    $object = User::find($request->object_id);
                    break;
                case 'email':
                    $object = Email::find($request->object_id);
                    break;
                default:
                    $object = Customer::find($request->object);
            }
            // Set raw where query
            $rawWhere = "(message!='' or media_url!='')";

            // Do we want all?
            if ($loadAllMessages == 1) {
                $loadAttached = 1;
                $rawWhere = '1=1';
            }

            // Get chat messages
            $currentPage = request('page', 1);
            $skip = ($currentPage - 1) * $limit;

            $loadType = $request->get('load_type');
            $onlyBroadcast = false;

            //  if loadtype is brodcast then get the images only
            if ($loadType == 'broadcast') {
                $onlyBroadcast = true;
                $loadType = 'images';
            }

            $chatMessages = $object->whatsappAll($onlyBroadcast)->whereRaw($rawWhere);
            if ($request->object == 'SOP') {
                $chatMessages = ChatMessage::where('sop_user_id', $object->id);
            }

            if ($request->object == 'user-feedback') {
                $chatMessages = ChatMessage::where('user_feedback_id', $object->id)->where('user_feedback_category_id', $request->feedback_category_id);
            }
            if ($request->object == 'user-feedback-hrTicket') {
                $chatMessages = ChatMessage::where('user_feedback_id', $object->id)->where('user_feedback_category_id', $request->feedback_category_id);
            }
            if ($request->object == 'uicheck') {
                $chatMessages = ChatMessage::where('ui_check_id', $request->object_id);
            }
            if ($request->object == 'hubstuff') {
                $chatMessages = ChatMessage::where('hubstuff_activity_user_id', $object->id);
            }
            if (! $onlyBroadcast) {
                $chatMessages = $chatMessages->where('status', '!=', 10);
            }

            if ($request->date != null) {
                $chatMessages = $chatMessages->whereDate('created_at', $request->date);
            }

            if ($request->keyword != null) {
                $chatMessages = $chatMessages->where('message', 'like', '%' . $request->keyword . '%'); //Purpose - solve issue for search message , Replace form whereDate to where - DEVTASK-4020
            }

            if ($request->object == 'timedoctor') {
                $chatMessages = ChatMessage::where('time_doctor_activity_user_id', $object->id);
            }

            $chatMessages = $chatMessages->skip($skip)->take($limit);

            switch ($loadType) {
                case 'text':
                    $chatMessages = $chatMessages->whereNotNull('message')
                        ->whereNull('media_url')
                        ->whereRaw('id not in (select mediable_id from mediables WHERE mediable_type LIKE "App%ChatMessage")');
                    break;
                case 'images':
                    $chatMessages = $chatMessages->whereRaw("(media_url is not null or id in (
                    select
                        mediable_id
                    from
                        mediables
                        join media on id = media_id and extension != 'pdf'
                    WHERE
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                    break;
                case 'pdf':
                    $chatMessages = $chatMessages->whereRaw("(id in (
                    select
                        mediable_id
                    from
                        mediables
                        join media on id = media_id and extension = 'pdf'
                    WHERE
                        mediable_type LIKE 'App%ChatMessage'
                ) )");
                    break;
                case 'text_with_incoming_img':
                    $chatMessages = $chatMessages->where(function ($query) use ($object) {
                        $query->whereRaw('(chat_messages.number = ' . $object->phone . " and ( media_url is not null
                                                or id in (
                                                select
                                                    mediable_id
                                                from
                                                    mediables
                                                    join media on id = media_id and extension != 'pdf'
                                                WHERE
                                                    mediable_type LIKE 'App%ChatMessage'
                                            )) )")->orWhere(function ($query) {
                            $query->whereNotNull('message')
                                ->whereNull('media_url')
                                ->whereRaw('id not in (select mediable_id from mediables WHERE mediable_type LIKE "App%ChatMessage")');
                        });
                    });
                    break;
                case 'incoming_img':
                    $chatMessages = $chatMessages->where(function ($query) use ($object) {
                        $query->whereRaw('(chat_messages.number = ' . $object->phone . " and ( media_url is not null
                                                or id in (
                                                select
                                                    mediable_id
                                                from
                                                    mediables
                                                    join media on id = media_id and extension != 'pdf'
                                                WHERE
                                                    mediable_type LIKE 'App%ChatMessage'
                                            )) )");
                    });
                    break;
                case 'outgoing_img':
                    $chatMessages = $chatMessages->where(function ($query) use ($object) {
                        $query->whereRaw('((chat_messages.number != ' . $object->phone . "  or chat_messages.number is null) and ( media_url is not null
                                            or id in (
                                            select
                                                mediable_id
                                            from
                                                mediables
                                                join media on id = media_id and extension != 'pdf'
                                            WHERE
                                                mediable_type LIKE 'App%ChatMessage'
                                        )) )");
                    });
                    break;
            }

            $chatMessages = $chatMessages->get();

            // Set empty array with messages
            $messages = [];
            $chatFileData = '';
            // Loop over ChatMessages
            foreach ($chatMessages as $chatMessage) {
                $objectname = null;
                if ($request->object == 'customer' || $request->object == 'charity' || $request->object == 'user' || $request->object == 'vendor' || $request->object == 'supplier' || $request->object == 'site_development' || $request->object == 'social_strategy' || $request->object == 'content_management' || $request->object == 'uicheck') {
                    $objectname = $object->name;
                }
                if ($request->object == 'task' || $request->object == 'developer_task') {
                    $u = User::find($chatMessage->user_id);
                    if ($u) {
                        $objectname = $u->name;
                    }
                }
                // Create empty media array
                $media = [];
                $mediaWithDetails = [];
                $productId = null;
                $parentMedia = [];
                $parentMediaWithDetails = [];
                $parentProductId = null;

                // Check for media
                if ($loadAttached == 1 && $chatMessage->hasMedia(config('constants.media_tags'))) {
                    foreach ($chatMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                        // Supplier checkbox
                        if (in_array($request->object, ['supplier'])) {
                            $tempImage = [
                                'key' => $image->getKey(),
                                'image' => $image->getUrl(),
                                'product_id' => '',
                                'special_price' => '',
                                'size' => '',
                            ];

                            $imageKey = $image->getKey();
                            $mediableType = 'Product';

                            $productImage = \App\Product::with('Media')
                                ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $imageKey AND mediables.mediable_type LIKE '%$mediableType%')")
                                ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                            if ($productImage) {
                                $tempImage['product_id'] = $productImage->id;
                                $tempImage['special_price'] = $productImage->price_inr_special;
                                $tempImage['supplier_initials'] = $this->getSupplierIntials($productImage->supplier);
                                $tempImage['size'] = $this->getSize($productImage);
                            }

                            $mediaWithDetails[] = $tempImage;
                        } else {
                            // Check for product
                            if (isset($image->id)) {
                                $product = DB::table('mediables')->where('mediable_type', \App\Product::class)->where('media_id', $image->id)->get(['mediable_id'])->first();

                                if ($product != null) {
                                    $productId = $product->mediable_id;
                                } else {
                                    $productId = null;
                                }
                            }

                            // Get media URL
                            $media[] = [
                                'key' => $image->getKey(),
                                'image' => $image->getUrl(),
                                'product_id' => $productId,
                            ];
                        }
                    }
                }
                if ($request->object == 'customer') {
                    if (session()->has('encrpyt')) {
                        $public = PublicKey::first();
                        if ($public != null) {
                            $privateKey = hex2bin(session()->get('encrpyt.private'));
                            $publicKey = hex2bin($public->key);
                            $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);
                            $message = hex2bin($chatMessage->message);
                            $textMessage = sodium_crypto_box_seal_open($message, $keypair);
                        }
                    } else {
                        $textMessage = htmlentities($chatMessage->message);
                    }
                } else {
                    $textMessage = htmlentities($chatMessage->message);
                }
                $isOut = ($chatMessage->number != $object->phone) ? true : false;
                //check for parent message
                $textParent = null;
                if ($chatMessage->quoted_message_id) {
                    $parentMessage = ChatMessage::find($chatMessage->quoted_message_id);
                    if ($parentMessage) {
                        if ($request->object == 'customer') {
                            if (session()->has('encrpyt')) {
                                $public = PublicKey::first();
                                if ($public != null) {
                                    $privateKey = hex2bin(session()->get('encrpyt.private'));
                                    $publicKey = hex2bin($public->key);
                                    $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey, $publicKey);
                                    $message = hex2bin($parentMessage->message);
                                    $textParent = sodium_crypto_box_seal_open($message, $keypair);
                                }
                            } else {
                                $textParent = htmlentities($parentMessage->message);
                            }
                        } else {
                            $textParent = htmlentities($parentMessage->message);
                        }

                        //parent image start here
                        if ($parentMessage->hasMedia(config('constants.media_tags'))) {
                            // foreach ($parentMessage->getMedia(config('constants.media_tags')) as $key => $image) {
                            $images = $parentMessage->getMedia(config('constants.media_tags'));
                            $image = $images->first();
                            // Supplier checkbox
                            if ($image) {
                                if (in_array($request->object, ['supplier'])) {
                                    $tempImage = [
                                        'key' => $image->getKey(),
                                        'image' => $image->getUrl(),
                                        'product_id' => '',
                                        'special_price' => '',
                                        'size' => '',
                                    ];
                                    $imageKey = $image->getKey();
                                    $mediableType = 'Product';

                                    $productImage = \App\Product::with('Media')
                                        ->whereRaw("products.id IN (SELECT mediables.mediable_id FROM mediables WHERE mediables.media_id = $imageKey AND mediables.mediable_type LIKE '%$mediableType%')")
                                        ->select(['id', 'price_inr_special', 'supplier', 'size', 'lmeasurement', 'hmeasurement', 'dmeasurement'])->first();

                                    if ($productImage) {
                                        $tempImage['product_id'] = $productImage->id;
                                        $tempImage['special_price'] = $productImage->price_inr_special;
                                        $tempImage['supplier_initials'] = $this->getSupplierIntials($productImage->supplier);
                                        $tempImage['size'] = $this->getSize($productImage);
                                    }

                                    $parentMediaWithDetails[] = $tempImage;
                                } else {
                                    // Check for product
                                    if (isset($image->id)) {
                                        $product = DB::table('mediables')->where('mediable_type', \App\Product::class)->where('media_id', $image->id)->get(['mediable_id'])->first();

                                        if ($product != null) {
                                            $parentProductId = $product->mediable_id;
                                        } else {
                                            $parentProductId = null;
                                        }
                                    }

                                    // Get media URL
                                    $parentMedia[] = [
                                        'key' => $image->getKey(),
                                        'image' => $image->getUrl(),
                                        'product_id' => $parentProductId,
                                    ];
                                }
                            }

                            // }
                        }
                        //parent image ends
                    }
                }

                //START - Purpose : Get Excel sheet - DEVTASK-4236
                $excel_attach = json_decode($chatMessage->additional_data);
                if (! empty($excel_attach)) {
                    $path = $excel_attach->attachment[0];
                    $additional_data = $path;
                } else {
                    $additional_data = '';
                }
                $sopdata = \App\Sop::where(['chat_message_id' => $chatMessage->id])->first();
                //END - DEVTASK-4236

                if (isset($request->downloadMessages) && $request->downloadMessages == 1) {
                    if ($textMessage != '') {
                        $chatFileData .= html_entity_decode($textMessage, ENT_QUOTES, 'UTF-8');
                        $chatFileData .= "\n From " . (($isOut) ? 'ERP' : $objectname) . ' To ' . (($isOut) ? $object->name : 'ERP');
                        $chatFileData .= "\n On " . Carbon::parse($chatMessage->created_at)->format('Y-m-d H:i A');
                        $chatFileData .= "\n" . "\n" . "\n";
                    }
                } else {
                    $arr = [
                        'id' => $chatMessage->id,
                        'type' => $request->object,
                        'object_type_id' => $request->object_id,
                        'sop_name' => @$sopdata->name,
                        'sop_category' => @$sopdata->category,
                        'sop_content' => @$sopdata->content,
                        'inout' => ($isOut) ? 'out' : 'in',
                        'sendBy' => ($request->object == 'bug' || $request->object == 'testcase' || $request->object == 'testsuites' || $request->object == 'developer_task') ? User::where('id', $chatMessage->sent_to_user_id)->value('name') : (($isOut) ? 'ERP' : $objectname),
                        'sendTo' => ($request->object == 'bug' || $request->object == 'testcase' || $request->object == 'testsuites' || $request->object == 'developer_task') ? User::where('id', $chatMessage->user_id)->value('name') : (($isOut) ? $object->name : 'ERP'),
                        'message' => $textMessage,
                        'parentMessage' => $textParent,
                        'media_url' => $chatMessage->media_url,
                        'datetime' => Carbon::parse($chatMessage->created_at)->format('Y-m-d H:i A'),
                        'media' => is_array($media) ? $media : null,
                        'mediaWithDetails' => is_array($mediaWithDetails) ? $mediaWithDetails : null,
                        'product_id' => ! empty($productId) ? $productId : null,
                        'parentMedia' => is_array($parentMedia) ? $parentMedia : null,
                        'parentMediaWithDetails' => is_array($parentMediaWithDetails) ? $parentMediaWithDetails : null,
                        'parentProductId' => ! empty($parentProductId) ? $parentProductId : null,
                        'status' => $chatMessage->status,
                        'resent' => $chatMessage->resent,
                        'customer_id' => $chatMessage->customer_id,
                        'approved' => $chatMessage->approved,
                        'error_status' => $chatMessage->error_status,
                        'error_info' => $chatMessage->error_info,
                        'is_queue' => $chatMessage->is_queue,
                        'is_reviewed' => $chatMessage->is_reviewed,
                        'quoted_message_id' => $chatMessage->quoted_message_id,
                        'additional_data' => $additional_data, //Purpose : Add additional data - DEVTASK-4236
                    ];

                    if ($chatMessage->message_type == 'email') {
                        $arr['sendTo'] = $chatMessage->from_email;
                        $arr['sendBy'] = $chatMessage->to_email;
                    }

                    $messages[] = $arr;
                }
            }

            // Return JSON
            if (isset($request->downloadMessages) && $request->downloadMessages == 1) {
                $storagelocation = storage_path() . '/chatMessageFiles';
                if (! is_dir($storagelocation)) {
                    mkdir($storagelocation, 0777, true);
                }
                $filename = $request->object . $request->object_id . '_chat.txt';
                $file = $storagelocation . '/' . $filename;
                $txt = fopen($file, 'w') or exit('Unable to open file!');
                fwrite($txt, $chatFileData);
                fclose($txt);
                if ($chatFileData == '') {
                    return response()->json([
                        'downloadUrl' => '',
                    ]);
                }

                return response()->json([
                    'downloadUrl' => $file,
                ]);
            } else {
                return response()->json([
                    'messages' => $messages,
                ]);
            }

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
//            $sendMessage = app('App\Http\Controllers\WhatsAppController')->sendMessage($request, 'task');
            $status = ($value == 1) ? 'send message Successfully' : 'Suggested message rejected';

            return response()->json(['code' => 200, 'data' => [], 'messages' => $status]);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'messages' => 'Suggested replay does not exist in record']);
        }

    }
}
