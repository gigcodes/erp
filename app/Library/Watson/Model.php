<?php

namespace App\Library\Watson;

use App\ChatbotDialog;
use App\ChatbotKeyword;
use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\Customer;
use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;
use App\Library\Watson\Language\Workspaces\V1\LogService;
use \App\ChatbotKeywordValue;

class Model
{

    const EXCLUDED_REPLY = [
        "Can you reword your statement? I'm not understanding.",
        "I didn't understand. You can try rephrasing.",
        "I didn't get your meaning.",
    ];

    const API_KEY = "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC";

    public static function getWorkspaceId()
    {
        return "19cf3225-f007-4332-8013-74443d36a3f7";
    }

    public static function getAssistantId()
    {
        return "28754e1c-6281-42e6-82af-eec6e87618a6";
    }

    public static function pushKeyword($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $keyword     = ChatbotQuestion::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($keyword) {

            $storeParams                = [];
            $storeParams["entity"]      = $keyword->value;
            $storeParams["fuzzy_match"] = true;
            $values                     = $keyword->chatbotQuestionExamples()->get();
            $storeParams["values"]      = [];
            $typeValue                  = [];
            foreach ($values as $value) {
                $typeValue = ChatbotQuestionExample::where("id", $value["id"])->first()->chatbotKeywordValueTypes()->get()->pluck("type");
                if ($value["types"] == "synonyms") {
                    $storeParams["values"][] = ["value" => $value["question"], "synonyms" => $typeValue];
                } else {
                    $storeParams["values"][] = ["value" => $value["question"], "type" => "patterns", "patterns" => $typeValue];
                }
            }

            $watson = new EntitiesService(
                "apiKey",
                self::API_KEY
            );

            if (!empty($keyword->workspace_id)) {
                $result = $watson->update($keyword->workspace_id, $keyword->value, $storeParams);
            } else {
                $result                = $watson->create($workSpaceId, $storeParams);
                $keyword->workspace_id = $workSpaceId;
                $keyword->save();
            }

            if ($result->getStatusCode() != 200) {
                \Log::info(print_r($result, true));
                return $result->getContent();
            }

        }

        return true;

    }

    public static function deleteKeyword($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $keyword = ChatbotKeyword::where("id", $id)->first();

        if (!empty($keyword) && !empty($keyword->workspace_id)) {

            $watson = new EntitiesService(
                "apiKey",
                self::API_KEY
            );

            $watson->delete($keyword->workspace_id, $keyword->keyword);
        }

        return true;

    }

    public static function pushQuestion($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $question    = ChatbotQuestion::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($question) {

            $storeParams             = [];
            $storeParams["intent"]   = $question->value;
            $values                  = $question->chatbotQuestionExamples()->get();
            $storeParams["examples"] = [];
            foreach ($values as $k => $value) {
                $storeParams["examples"][$k]["text"] = $value->question;
                $mentions                            = $value->annotations;
                if (!$mentions->isEmpty()) {
                    $sendMentions = [];
                    foreach ($mentions as $key => $mRaw) {
                        $sendMentions[] = [
                            "entity"   => $mRaw->chatbotKeyword->keyword,
                            "location" => [$mRaw->start_char_range, $mRaw->end_char_range],
                        ];
                    }
                    if (!empty($sendMentions)) {
                        $storeParams["examples"][$k]["mentions"] = $sendMentions;
                    }
                }
            }

            $watson = new IntentService(
                "apiKey",
                self::API_KEY
            );

            if (!empty($question->workspace_id)) {
                $result = $watson->update($question->workspace_id, $question->value, $storeParams);
            } else {
                $result                 = $watson->create($workSpaceId, $storeParams);
                $question->workspace_id = $workSpaceId;
                $question->save();
            }

            if ($result->getStatusCode() != 200) {
                \Log::info(print_r($result, true));
                return $result->getContent();
            }
        }

        return true;

    }

    public static function pushValue($exampleId, $oldExample = "")
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $questionExample = ChatbotQuestionExample::where("id", $exampleId)->first();
        $workSpaceId     = self::getWorkspaceId();

        if ($questionExample) {

            if (empty($oldExample)) {
                $oldExample = $questionExample->question;
            }

            $questionModel = $questionExample->questionModal;
            $question      = $questionExample->question;
            $mentions      = $questionExample->annotations;
            $storeParams   = [
                "text" => $questionExample->question,
            ];

            $sendMentions = [];
            if (!$mentions->isEmpty()) {
                foreach ($mentions as $key => $mRaw) {
                    // if ($mRaw->chatbotKeyword) {
                    //     $sendMentions[] = [
                    //         "entity"   => $mRaw->chatbotKeyword->keyword,
                    //         "location" => [$mRaw->start_char_range, $mRaw->end_char_range],
                    //     ];
                    // }
                    if ($mRaw->chatbotQuestion) {
                        $sendMentions[] = [
                            "entity"   => $mRaw->chatbotQuestion->value,
                            "location" => [$mRaw->start_char_range, $mRaw->end_char_range],
                        ];
                    }
                }
            }

            if (!empty($sendMentions)) {
                $storeParams["mentions"] = $sendMentions;
            }
            /*"mentions" => [
            [
            "entity" => "payment_card",
            "location" => [
            7,10
            ]
            ]
            ]*/

            $watson = new IntentService(
                "apiKey",
                self::API_KEY
            );

            if (!empty($questionModel->workspace_id)) {
                $result = $watson->updateExample($questionModel->workspace_id, $questionModel->value, $oldExample, $storeParams);

            }

            if ($result->getStatusCode() != 200) {
                \Log::info(print_r($result, true));
            }
        }

        return true;

    }

    public static function deleteQuestion($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $question = ChatbotQuestion::where("id", $id)->first();

        if (!empty($question) && !empty($question->workspace_id)) {

            $watson = new IntentService(
                "apiKey",
                self::API_KEY
            );

            $response = $watson->delete($question->workspace_id, $question->value);
        }

        return true;

    }

    public static function pushDialog($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $dialog = ChatbotDialog::where("id", $id)->first();

        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {

            $storeParams                = [];
            $storeParams["dialog_node"] = $dialog->name;
            $storeParams["conditions"]  = $dialog->match_condition;
            $storeParams["title"]       = $dialog->title;
            $values                     = $dialog->response()->get();
            $storeParams["type"]        = ($dialog->type == "folder") ? "folder" : "standard";

            $genericOutput = [];
            foreach ($values as $value) {
                $genericOutput["response_type"] = $value->response_type;
                $genericOutput["values"][]      = ["text" => $value->value];
            }

            $watson = new DialogService(
                "apiKey",
                self::API_KEY
            );

            if (!empty($dialog->workspace_id)) {
                $storeParams["output"]["generic"][] = $genericOutput;
                $result                             = $watson->update($dialog->workspace_id, $dialog->name, $storeParams);
            } else {
                $result               = $watson->create($workSpaceId, $storeParams);
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();
            }

            if ($result->getStatusCode() != 200) {
                \Log::info(print_r($result, true));
                return $result->getContent();
            }
        }

        return true;

    }

    public static function deleteDialog($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $dialog = ChatbotDialog::where("id", $id)->first();

        if (!empty($dialog) && !empty($dialog->workspace_id)) {

            $watson = new DialogService(
                "apiKey",
                self::API_KEY
            );

            $response = $watson->delete($dialog->workspace_id, $dialog->name);
        }

        return true;

    }

    public static function sendMessage(Customer $customer, $inputText, $contextReset = false)
    {
        // if (env("PUSH_WATSON", true) == false) {
        //     return true;
        // }

        $assistantID = self::getAssistantId();
        $assistant   = new AssistantService(
            "apiKey",
            self::API_KEY
        );

        if (empty($customer->chat_session_id)) {
            $customer = self::createSession($customer, $assistant);
            if (!$customer) {
                return false;
            }
        }
        if (!empty($customer->chat_session_id)) {
            // now sending message to the watson
            $result = self::sendMessageCustomer($customer, $assistant, $inputText, $contextReset);
            if (!empty($result->code) && $result->code == 404 && $result->error == "Invalid Session") {
                $customer = self::createSession($customer, $assistant);
                if ($customer) {
                    $result = self::sendMessageCustomer($customer, $assistant, $inputText, $contextReset);
                }
            }

            $chatResponse = new ResponsePurify($result, $customer);
            //check for auto approve message
            $auto_approve = $chatResponse->checkAutoApprove();
            if($auto_approve) {
                $status = \App\ChatMessage::CHAT_MESSAGE_APPROVED;
            }
            else {
                $status = \App\ChatMessage::CHAT_AUTO_WATSON_REPLY;
            }
            // if response is valid then check ahead
            if ($chatResponse->isValid()) {
                $result = $chatResponse->assignAction();

                \Log::info(print_r($result,true));
                if (!empty($result)) {
                    if (!empty($result["action"])) {
                        // assign params
                        $params = [
                            "is_queue"         => 0,
                            "status"           => $status,
                            "customer_ids"     => [$customer->id],
                            "message"          => $result["reply_text"],
                            "is_chatbot"       => true,
                            "chatbot_response" => $result,
                            "chatbot_question" => $inputText,
                            "chatbot_params"   => isset($result["medias"]) ? $result["medias"] : [],
                        ];

                        switch ($result["action"]) {
                            case 'send_product_images':

                                // add into suggestion
                                $brands   = [];
                                $category = [];

                                if (!empty($result["medias"]["params"]["brands"])) {
                                    $brands = $result["medias"]["params"]["brands"];
                                }

                                if (!empty($result["medias"]["params"]["category"])) {
                                    $category = $result["medias"]["params"]["category"];
                                }

                                self::sendMessage($customer, "image_has_been_found", true);

                                if (!empty($brands) || !empty($category)) {
                                    $suggestion = \App\Suggestion::create([
                                        "customer_id" => $customer->id,
                                        "brand"       => json_encode($brands),
                                        "category"    => json_encode($category),
                                        "number"      => 30,
                                    ]);

                                    // setup the params
                                    $insertParams = [
                                        "customer_id" => $customer->id,
                                        "message"     => isset($params["message"]) ? $params["message"] : null,
                                        "status"      => isset($params["status"]) ? $params["status"] : \App\ChatMessage::CHAT_AUTO_BROADCAST,
                                        "is_queue"    => isset($params["is_queue"]) ? $params["is_queue"] : 0,
                                        "group_id"    => isset($params["group_id"]) ? $params["group_id"] : null,
                                        "user_id"     => isset($params["user_id"]) ? $params["user_id"] : null,
                                        "number"      => null,
                                        "is_chatbot"  => isset($params["is_chatbot"]) ? $params["is_chatbot"] : 0,
                                    ];

                                    $chatMessage = ChatMessage::create($insertParams);
                                    if ($chatMessage->status == ChatMessage::CHAT_AUTO_WATSON_REPLY) {
                                        \App\ChatbotReply::create([
                                            "chat_id"  => $chatMessage->id,
                                            "question" => isset($params["chatbot_question"]) ? $params["chatbot_question"] : null,
                                            "reply"    => isset($params["chatbot_response"]) ? json_encode($params["chatbot_response"]) : json_encode([]),
                                        ]);
                                    }

                                    $suggestion->chat_message_id = $chatMessage->id;
                                    $suggestion->save();

                                    \App\Jobs\AttachSuggestionProduct::dispatch($suggestion)->onQueue("customer_message");
                                }

                                break;
                            case 'send_text_only':
                                \App\Jobs\SendMessageToCustomer::dispatch($params)->onQueue("customer_message");
                                break;
                        }
                    }

                }
            }
            /*if (isset($result->output) && isset($result->output->generic)) {

            $textMessage = reset($result->output->generic);
            if(isset($result->output->entities)) {
            $entities = $result->output->entities;
            $imageFiles = [];
            foreach($entities as $entity) {
            // if a entity keyword is product then find image matching it brand and category
            if( $entity->entity == "product") {
            $value = strtoupper($entity->value);
            $brand = explode(" ", $value);
            $brand = Brand::where('name', 'LIKE',"%".$brand[0]."%")->first();
            $category = trim(str_replace($brand->name,"", $value));
            $images = Image::where('brand','LIKE',"%".$brand->name."%")->where('category','LIKE',"%".$category."%")->get();
            foreach($images as $image) {
            array_push($imageFiles, $image->filename);
            }
            }
            }
            }

            if (isset($textMessage->text)) {
            if (!in_array($textMessage->text, self::EXCLUDED_REPLY)) {
            return ["reply_text" => $textMessage, "response" => json_encode($result), "imageFiles"=>$imageFiles];
            }
            }
            }*/

            return false;
        }

    }

    public static function createSession(Customer $customer, AssistantService $assistant)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $assistantID = self::getAssistantId();

        $session = $assistant->createSession($assistantID);
        $result  = json_decode($session->getContent());

        if (isset($result->session_id)) {
            $customer->chat_session_id = $result->session_id;
            $customer->save();

            return $customer;
        }

        return false;

    }

    public static function sendMessageCustomer(Customer $customer, AssistantService $assistant, $inputText, $contextReset = false)
    {
        if (env("PUSH_WATSON", true) == false) {
            return true;
        }

        $assistantID = self::getAssistantId();

        $params = [
            "input" => [
                "text"    => $inputText,
                "options" => [
                    "return_context" => true,
                ],
            ],
        ];
        //$contextReset = true;
        /*if($contextReset) {
        $params["context"]["global"]["system"]["turn_count"]                        = 0;
        $params["context"]["skills"]["main skill"]["user_defined"]["brand_name"]    = null;
        $params["context"]["skills"]["main skill"]["user_defined"] = null;
        //$params["context"]["skills"]["main skill"]["user_defined"]["category_name"] = null;
        }*/

        $result = $assistant->sendMessage($assistantID, $customer->chat_session_id, $params);
        return json_decode($result->getContent());

    }

    public static function newPushDialog($id)
    {
        if (env("PUSH_WATSON", true) == false) {
            return ["code" => 500, "error" => "Sorry, Watson push is not activated"];
        }

        $dialog      = ChatbotDialog::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {

            $storeParams                     = [];
            $storeParams["dialog_node"]      = $dialog->name;
            $storeParams["conditions"]       = $dialog->match_condition;
            $storeParams["title"]            = $dialog->title;
            $storeParams["previous_sibling"] = $dialog->getPreviousSiblingName();
            $storeParams["type"]             = ($dialog->dialog_type == "folder") ? $dialog->dialog_type : $dialog->response_type;
            $storeParams["parent"]           = $dialog->getParentName();

            $multipleResponse = false;
            if (!empty($dialog->metadata) && $storeParams["type"] != "folder") {
                $multipleResponse = true;
            }

            $genericOutput = [];
            if (!$multipleResponse) {
                foreach ($dialog->response as $value) {
                    $genericOutput["response_type"] = $value->response_type;
                    $genericOutput["values"][]      = ["text" => $value->value];
                }
            }

            // update into watson api
            $watson = new DialogService(
                "apiKey",
                self::API_KEY
            );

            if (!empty($genericOutput) && $storeParams["type"] != "folder") {
                $storeParams["output"]["generic"][] = $genericOutput;
            }

            if (!empty($dialog->workspace_id)) {
                $result = $watson->update($dialog->workspace_id, $dialog->name, $storeParams);
            } else {
                $result = $watson->create($workSpaceId, $storeParams);
                if ($result->getStatusCode() != 200) {
                    $error = json_decode($result->getContent());
                    if (isset($error->error)) {
                        return ["code" => 500, "error" => $error->error];
                    }
                }
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();
            }

            // once stored into the api now we will check for the multiple response condition
            if ($multipleResponse) {
                $multipleDialog = $dialog->multipleCondition()->where("response_type", "response_condition")->get();
                if (!$multipleDialog->isEmpty()) {
                    foreach ($multipleDialog as $mulDialog) {

                        $storeParams                     = [];
                        $storeParams["dialog_node"]      = $mulDialog->name;
                        $storeParams["conditions"]       = $mulDialog->match_condition;
                        $storeParams["title"]            = $mulDialog->title;
                        $storeParams["previous_sibling"] = $mulDialog->getPreviousSiblingName();
                        $storeParams["type"]             = $mulDialog->response_type;
                        $storeParams["parent"]           = $mulDialog->getParentName();

                        $genericOutput = [];
                        foreach ($mulDialog->response as $value) {
                            $genericOutput["response_type"] = $value->response_type;
                            $genericOutput["values"][]      = ["text" => $value->value];
                        }

                        $watson = new DialogService(
                            "apiKey",
                            self::API_KEY
                        );

                        if (!empty($mulDialog->workspace_id)) {
                            $storeParams["output"]["generic"][] = $genericOutput;
                            $result                             = $watson->update($mulDialog->workspace_id, $mulDialog->name, $storeParams);
                        } else {
                            $storeParams["output"]["generic"][] = $genericOutput;
                            $result                             = $watson->create($workSpaceId, $storeParams);
                            $mulDialog->workspace_id            = $workSpaceId;
                            $mulDialog->save();
                        }

                        if ($result->getStatusCode() != 200) {
                            $error = json_decode($result->getContent());
                            if (isset($error->error)) {
                                return ["code" => 500, "error" => $error->error];
                            }
                        } else {
                            return ["code" => 200, "error" => false];
                        }
                    }
                }
            }

            return ["code" => 200, "error" => false];
        }

    }

    public static function getLog($params = [])
    {
        $log = new LogService(
            "apiKey",
            self::API_KEY
        );

        $response = $log->get(self::getWorkspaceId(), $params);

        if ($response->getStatusCode() == 200) {
            return json_decode($response->getContent());
        }

        return [];
    }

}