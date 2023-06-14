<?php

namespace App\Observers;

use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\ChatbotQuestionReply;
use App\ChatMessage;
use App\Library\Google\DialogFlow\DialogFlowService;
use App\Models\GoogleDialogAccount;
use App\Models\TmpReplay;
use Google\Service\Drive\Resource\Replies;
use Illuminate\Database\Eloquent\Model;
use Modules\ChatBot\Http\Controllers\QuestionController;

class ChatMessageObserver
{
    /**
     * Handle the ChatMessage "created" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function created(ChatMessage $chatMessage)
    {
        try {
            $session = \Cache::get('chatbot-session');
            if (!$session) {
                $session = uniqid();
                \Cache::set('chatbot-session', $session);
            }
            $object = '';
            $objectId = '';
            if ($chatMessage->vendor_id) {
                $object = 'vendor';
                $objectId = $chatMessage->vendor_id;
            }  elseif ($chatMessage->customer_id) {
                $object = 'customer';
                $objectId = $chatMessage->customer_id;
            } elseif ($chatMessage->supplier_id) {
                $object = 'supplier';
                $objectId = $chatMessage->supplier_id;
            }
            $requestMessage = \Request::create('/', 'GET', [
                'limit' => 1,
                'object' => $object,
                'object_id' => $objectId,
                'for_simulator' => true
            ]);
            $firstMessage = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);
            $requestMessage = \Request::create('/', 'GET', [
                'limit' => 1,
                'object' => $object,
                'object_id' => $objectId,
                'plan_response' => true
            ]);
            $lastMessage = app('App\Http\Controllers\ChatMessagesController')->loadMoreMessages($requestMessage);
            if ($firstMessage[0] && $lastMessage[0] && ($chatMessage->vendor_id || $chatMessage->customer_id || $chatMessage->supplier_id)) {
                if ($chatMessage->customer_id > 0) {
                    $googleAccount = GoogleDialogAccount::where('site_id', $object['store_website_id'])->first();
                } else {
                    $googleAccount = GoogleDialogAccount::where('id', 1)->first();
                }

                if ($firstMessage[0]['sendTo'] == $lastMessage[0]['sendTo']) {
                    if ($firstMessage[0]['is_auto_simulator'] == 1) {
                        $chatQuestions = ChatbotQuestion::leftJoin('chatbot_question_examples as cqe', 'cqe.chatbot_question_id', 'chatbot_questions.id')
                            ->leftJoin('chatbot_categories as cc', 'cc.id', 'chatbot_questions.category_id')
                            ->select('chatbot_questions.*', \DB::raw('group_concat(cqe.question) as `questions`'), 'cc.name as category_name')
                            ->where('chatbot_questions.google_account_id', 1)
                            ->where('chatbot_questions.keyword_or_question', 'intent')
                            ->where('chatbot_questions.value', 'like', '%' . $chatMessage->message . '%')->orWhere('cqe.question', 'like', '%' . $chatMessage->message . '%')
                            ->groupBy('chatbot_questions.id')
                            ->orderBy('chatbot_questions.id', 'desc')
                            ->first();
                        $requestData = [
                            'chat_id' => $chatMessage->id,
                            'status' => 2,
                            'add_autocomplete' => false
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

                        $getFromGoogle = true;
                        if ($chatQuestions) {
                            if ($chatQuestions->auto_approve == 1) {
                                $requestData['message'] = $chatQuestions->suggested_reply;
                                $request = \Request::create('/', 'POST', $requestData);
                                app('App\Http\Controllers\WhatsAppController')->sendMessage($request, $lastMessage[0]['type']);
                                $getFromGoogle = false;
                            }
                        } else {
                            $replay =  \App\ReplyCategory::join('replies', 'reply_categories.id', 'replies.category_id')
                                ->select(['replies.*', 'reply_categories.intent_id', 'reply_categories.name as category_name', 'reply_categories.parent_id', 'reply_categories.id as reply_cat_id'])
                                ->Where('reply_categories.name', 'LIKE', '%' . $chatMessage->message . '%')
                                ->first();

                            if ($replay){
                                $requestData['message'] = $replay->replay;
                                $request = \Request::create('/', 'POST', $requestData);
                                app('App\Http\Controllers\WhatsAppController')->sendMessage($request, $lastMessage[0]['type']);
                                $getFromGoogle = false;
                            }
                        }

                        if ($getFromGoogle) {
                            $dialogFlowService = new DialogFlowService($googleAccount);
                            $response = $dialogFlowService->detectIntent(null, $chatMessage->message);
                            $intentName = $response->getIntent()->getName();
                            $intentName = explode('/', $intentName);
                            $intentName = $intentName[count($intentName) - 1];

                            $question = ChatbotQuestion::where('google_response_id', $intentName)->first();
                            if (!$question) {
                                $question = ChatbotQuestion::where('value', $response->getIntent()->getDisplayName())->first();
                                if (!$question) {
                                    $question = ChatbotQuestion::create([
                                        'keyword_or_question' => 'intent',
                                        'is_active' => true,
                                        'google_account_id' => $googleAccount->id,
                                        'google_status' => 'google sended',
                                        'google_response_id' => $intentName,
                                        'value' => $response->getIntent()->getDisplayName(),
                                        'suggested_reply' => $response->getFulfillmentText()
                                    ]);
                                }
                            }

                            $questionsR = ChatbotQuestionReply::where('suggested_reply', 'like', '%' . $response->getFulfillmentText() . '%')->first();
                            if (!$questionsR) {
                                $chatRply = new  ChatbotQuestionReply();
                                $chatRply->suggested_reply = $response->getFulfillmentText();
                                $chatRply->store_website_id = $googleAccount->site_id;
                                $chatRply->chatbot_question_id = $question->id;
                                $chatRply->save();
                            }
                            $store_replay = new TmpReplay();
                            $store_replay->chat_message_id = $chatMessage->id;
                            $store_replay->suggested_replay = $response->getFulfillmentText();
                            $store_replay->type = $lastMessage[0]['type'];
                            $store_replay->type_id = $lastMessage[0]['object_type_id'];
                            $store_replay->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            _p([$e->getMessage()]);die;
        }
    }

    /**
     * Handle the ChatMessage "updated" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function updated(ChatMessage $chatMessage)
    {

    }

    /**
     * Handle the ChatMessage "deleted" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function deleting(ChatMessage $chatMessage)
    {

    }

}
