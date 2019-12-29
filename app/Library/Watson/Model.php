<?php

namespace App\Library\Watson;

use App\ChatbotDialog;
use App\ChatbotKeyword;
use App\ChatbotQuestion;
use App\Customer;
use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;

class Model
{

    const EXCLUDED_REPLY = [
        "Can you reword your statement? I'm not understanding.",
        "I didn't understand. You can try rephrasing.",
        "I didn't get your meaning.",
    ];

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
        $keyword     = ChatbotKeyword::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($keyword) {

            $storeParams                = [];
            $storeParams["entity"]      = $keyword->keyword;
            $storeParams["fuzzy_match"] = true;
            $values                     = $keyword->chatbotKeywordValues()->get()->pluck("value", "value")->toArray();
            $storeParams["values"]      = [];
            foreach ($values as $value) {
                $storeParams["values"][] = ["value" => $value];
            }

            $watson = new EntitiesService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            if (!empty($keyword->workspace_id)) {
                $result = $watson->update($keyword->workspace_id, $keyword->keyword, $storeParams);
            } else {
                $result                = $watson->create($workSpaceId, $storeParams);
                $keyword->workspace_id = $workSpaceId;
                $keyword->save();
            }

        }

        return true;

    }

    public static function deleteKeyword($id)
    {

        $keyword = ChatbotKeyword::where("id", $id)->first();

        if (!empty($keyword) && !empty($keyword->workspace_id)) {

            $watson = new EntitiesService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            $watson->delete($keyword->workspace_id, $keyword->keyword);
        }

        return true;

    }

    public static function pushQuestion($id)
    {
        $question    = ChatbotQuestion::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($question) {

            $storeParams             = [];
            $storeParams["intent"]   = $question->value;
            $values                  = $question->chatbotQuestionExamples()->get()->pluck("question", "question")->toArray();
            $storeParams["examples"] = [];
            foreach ($values as $value) {
                $storeParams["examples"][] = ["text" => $value];
            }

            $watson = new IntentService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            if (!empty($question->workspace_id)) {
                $result = $watson->update($question->workspace_id, $question->value, $storeParams);
            } else {
                $result                 = $watson->create($workSpaceId, $storeParams);
                $question->workspace_id = $workSpaceId;
                $question->save();
            }
        }

        return true;

    }

    public static function deleteQuestion($id)
    {

        $question = ChatbotQuestion::where("id", $id)->first();

        if (!empty($question) && !empty($question->workspace_id)) {

            $watson = new IntentService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            $response = $watson->delete($question->workspace_id, $question->value);
        }

        return true;

    }

    public static function pushDialog($id)
    {
        $dialog = ChatbotDialog::where("id", $id)->first();

        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {

            $storeParams                = [];
            $storeParams["dialog_node"] = $dialog->name;
            $storeParams["conditions"]  = $dialog->match_condition;
            $storeParams["title"]       = $dialog->title;
            $values                     = $dialog->response()->get();

            $genericOutput = [];
            foreach ($values as $value) {
                $genericOutput["response_type"] = $value->response_type;
                $genericOutput["values"][]      = ["text" => $value->value];
            }

            $watson = new DialogService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            if (!empty($dialog->workspace_id)) {
                $storeParams["output"]["generic"][] = $genericOutput;
                $result                             = $watson->update($dialog->workspace_id, $dialog->name, $storeParams);
            } else {
                $result               = $watson->create($workSpaceId, $storeParams);
                $dialog->workspace_id = $workSpaceId;
                $dialog->save();
            }
        }

        return true;

    }

    public static function deleteDialog($id)
    {

        $dialog = ChatbotDialog::where("id", $id)->first();

        if (!empty($dialog) && !empty($dialog->workspace_id)) {

            $watson = new DialogService(
                "apiKey",
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            $response = $watson->delete($dialog->workspace_id, $dialog->name);
        }

        return true;

    }

    public static function sendMessage(Customer $customer, $inputText)
    {
        $assistantID = self::getAssistantId();
        $assistant   = new AssistantService(
            "apiKey",
            "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
        );

        if (empty($customer->chat_session_id)) {

            $customer = self::createSession($customer, $assistant);
            if (!$customer) {
                return false;
            }
        }

        if (!empty($customer->chat_session_id)) {
            // now sending message to the watson
            $result = self::sendMessageCustomer($customer, $assistant, $inputText);

            if (!empty($result->code) && $result->code == 404 && $result->error == "Invalid Session") {
                $customer = self::createSession($customer, $assistant);
                if ($customer) {
                    $result = self::sendMessageCustomer($customer, $assistant, $inputText);
                }
            }

            if (isset($result->output) && isset($result->output->generic)) {

                $textMessage = reset($result->output->generic);

                if (isset($textMessage->text)) {
                    if (!in_array($textMessage->text, self::EXCLUDED_REPLY)) {
                        return ["reply_text" => $textMessage, "response" => json_encode($result)];
                    }
                }
            }

            return false;
        }

    }

    public static function createSession(Customer $customer, AssistantService $assistant)
    {
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

    public static function sendMessageCustomer(Customer $customer, AssistantService $assistant, $inputText)
    {
        $assistantID = self::getAssistantId();

        $result = $assistant->sendMessage($assistantID, $customer->chat_session_id, [
            "input" => [
                "text" => $inputText,
            ],
        ]);

        return json_decode($result->getContent());

    }

    public static function newPushDialog($id)
    {
        $dialog      = ChatbotDialog::where("id", $id)->first();
        $workSpaceId = self::getWorkspaceId();

        if ($dialog) {

            $storeParams                     = [];
            $storeParams["dialog_node"]      = $dialog->name;
            $storeParams["conditions"]       = $dialog->match_condition;
            $storeParams["title"]            = $dialog->title;
            $storeParams["previous_sibling"] = $dialog->getPreviousSiblingName();
            $storeParams["type"]             = $dialog->response_type;
            $storeParams["parent"]           = $dialog->getParentName();

            $multipleResponse = false;
            if (!empty($dialog->metadata)) {
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
                "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
            );

            if(!empty($genericOutput)) {
                $storeParams["output"]["generic"][] = $genericOutput;
            }
            if (!empty($dialog->workspace_id)) {
                $result                             = $watson->update($dialog->workspace_id, $dialog->name, $storeParams);
            } else {
                $result               = $watson->create($workSpaceId, $storeParams);
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
                            "9is8bMkHLESrkNJvcMNNeabUeXRGIK8Hxhww373MavdC"
                        );

                        if (!empty($mulDialog->workspace_id)) {
                            $storeParams["output"]["generic"][] = $genericOutput;
                            $result                             = $watson->update($mulDialog->workspace_id, $mulDialog->name, $storeParams);
                        } else {
                            $storeParams["output"]["generic"][] = $genericOutput;
                            $result               = $watson->create($workSpaceId, $storeParams);
                            $mulDialog->workspace_id = $workSpaceId;
                            $mulDialog->save();
                        }
                    }
                }
            }

        }

    }

}
