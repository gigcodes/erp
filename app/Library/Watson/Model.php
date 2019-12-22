<?php

namespace App\Library\Watson;

use App\ChatbotDialog;
use App\ChatbotKeyword;
use App\ChatbotQuestion;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;

class Model
{
    public static function getWorkspaceId()
    {
        return "19cf3225-f007-4332-8013-74443d36a3f7";
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
                $result = $watson->update($dialog->workspace_id, $dialog->name, $storeParams);
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

}
