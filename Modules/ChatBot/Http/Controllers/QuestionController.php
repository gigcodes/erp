<?php

namespace Modules\ChatBot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use \App\ChatbotQuestion;
use \App\ChatbotQuestionExample;

use App\Library\Watson\Model as WatsonManager;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        $chatQuestions = ChatbotQuestion::leftJoin("chatbot_question_examples as cqe", "cqe.chatbot_question_id", "chatbot_questions.id")
            ->select("chatbot_questions.*", \DB::raw("group_concat(cqe.question) as `questions`"))
            ->groupBy("chatbot_questions.id")
            ->paginate(10);

        return view('chatbot::question.index', compact('chatQuestions'));
    }

    public function create()
    {
        return view('chatbot::question.create');
    }

    public function save(Request $request)
    {
        $params          = $request->all();
        $params["value"] = str_replace(" ", "_", $params["value"]);

        $validator = Validator::make($params, [
            'value' => 'required|unique:chatbot_questions|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => []]);
        }

        $chatbotQuestion = ChatbotQuestion::create($params);

        WatsonManager::pushQuestion($chatbotQuestion->id);

        return response()->json(["code" => 200, "data" => $chatbotQuestion,"redirect" => route("chatbot.question.edit",[$chatbotQuestion->id])]);
    }

    public function destroy(Request $request, $id)
    {
        if ($id > 0) {

            $chatbotQuestion = ChatbotQuestion::where("id", $id)->first();

            if ($chatbotQuestion) {
                ChatbotQuestionExample::where("chatbot_question_id", $id)->delete();
                $chatbotQuestion->delete();
                WatsonManager::deleteQuestion($chatbotQuestion->id);
                return redirect()->back();
            }

        }

        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $chatbotQuestion = ChatbotQuestion::where("id", $id)->first();

        return view("chatbot::question.edit", compact('chatbotQuestion'));
    }

    public function update(Request $request, $id)
    {

        $params                        = $request->all();
        $params["value"]               = str_replace(" ", "_", $params["value"]);
        $params["chatbot_question_id"] = $id;

        $chatbotQuestion = ChatbotQuestion::where("id", $id)->first();

        if ($chatbotQuestion) {

            $chatbotQuestion->fill($params);
            $chatbotQuestion->save();

            if (!empty($params["question"])) {
                $chatbotQuestionExample = new ChatbotQuestionExample;
                $chatbotQuestionExample->fill($params);
                $chatbotQuestionExample->save();
            }

            WatsonManager::pushQuestion($chatbotQuestion->id);

        }

        return redirect()->back();

    }

    public function destroyValue(Request $request, $id, $valueId)
    {
        $cbValue = ChatbotQuestionExample::where("chatbot_question_id", $id)->where("id", $valueId)->first();
        if ($cbValue) {
            $cbValue->delete();
            WatsonManager::pushQuestion($id);
        }
        return redirect()->back();
    }

}
