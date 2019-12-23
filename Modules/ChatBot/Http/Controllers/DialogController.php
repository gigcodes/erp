<?php

namespace Modules\ChatBot\Http\Controllers;

use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use \App\ChatbotQuestion;
use \App\ChatbotKeyword;
use \App\ChatbotDialog;
use \App\ChatbotDialogResponse;

class DialogController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $chatDialog = ChatbotDialog::leftJoin("chatbot_dialog_responses as cdr", "cdr.chatbot_dialog_id", "chatbot_dialogs.id")
            ->select("chatbot_dialogs.*", \DB::raw("group_concat(cdr.value) as `responses`"))
            ->groupBy("chatbot_dialogs.id")
            ->paginate(10);

        $question = ChatbotQuestion::select(\DB::raw("concat('#','',value) as value"))->get()->pluck("value","value")->toArray();
        $keywords = ChatbotKeyword::select(\DB::raw("concat('@','',keyword) as keyword"))->get()->pluck("keyword","keyword")->toArray();

        $allSuggestedOptions = $keywords+$question;

        return view('chatbot::dialog.index', compact('chatDialog','allSuggestedOptions'));
    }

    public function create()
    {
        return view('chatbot::dialog.create');
    }

    public function save(Request $request)
    {
        $params          = $request->all();
        $params["name"] = str_replace(" ", "_", $params["name"]);

        $validator = Validator::make($params, [
            'name' => 'required|unique:chatbot_dialogs|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => []]);
        }

        $chatbotDialog = ChatbotDialog::create($params);

        WatsonManager::pushDialog($chatbotDialog->id);

        return response()->json(["code" => 200, "data" => $chatbotDialog, "redirect" => route("chatbot.dialog.edit", [$chatbotDialog->id])]);
    }

    public function destroy(Request $request, $id)
    {
        if ($id > 0) {

            $chatbotDialog = ChatbotDialog::where("id", $id)->first();

            if ($chatbotDialog) {
                WatsonManager::deleteDialog($chatbotDialog->id);
                ChatbotDialogResponse::where("chatbot_dialog_id", $id)->delete();
                $chatbotDialog->delete();
                return redirect()->back();
            }

        }

        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $chatbotDialog = ChatbotDialog::where("id", $id)->first();
        $question = ChatbotQuestion::select(\DB::raw("concat('#','',value) as value"))->get()->pluck("value","value")->toArray();
        $keywords = ChatbotKeyword::select(\DB::raw("concat('@','',keyword) as keyword"))->get()->pluck("keyword","keyword")->toArray();
        $allSuggestedOptions = $keywords+$question;


        return view("chatbot::dialog.edit", compact('chatbotDialog','allSuggestedOptions'));
    }

    public function update(Request $request, $id)
    {

        $params                      = $request->all();
        $params["name"]             = str_replace(" ", "_", $params["name"]);
        $params["chatbot_dialog_id"] = $id;

        $chatbotDialog = ChatbotDialog::where("id", $id)->first();

        if ($chatbotDialog) {

            $chatbotDialog->fill($params);
            $chatbotDialog->save();

            if (!empty($params["value"])) {
                
                $params["response_type"]          = "text";
                $params["message_to_human_agent"] = 1;
                
                $chatbotDialogResponse            = new ChatbotDialogResponse;
                $chatbotDialogResponse->fill($params);
                $chatbotDialogResponse->save();
            
            }

            WatsonManager::pushDialog($chatbotDialog->id);

        }

        return redirect()->back();

    }

    public function destroyValue(Request $request, $id, $valueId)
    {
        $cbValue = ChatbotDialogResponse::where("chatbot_dialog_id", $id)->where("id", $valueId)->first();
        if ($cbValue) {
            $cbValue->delete();
            WatsonManager::pushDialog($id);
        }
        return redirect()->back();
    }

}
