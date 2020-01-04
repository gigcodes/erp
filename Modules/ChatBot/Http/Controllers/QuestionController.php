<?php

namespace Modules\ChatBot\Http\Controllers;

use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use \App\ChatbotQuestion;
use \App\ChatbotQuestionExample;

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
            ->orderBy("chatbot_questions.id", "desc")
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

        return response()->json(["code" => 200, "data" => $chatbotQuestion, "redirect" => route("chatbot.question.edit", [$chatbotQuestion->id])]);
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

    public function saveAjax(Request $request)
    {
        $groupId  = $request->get("group_id", 0);
        $name     = $request->get("name", "");
        $question = $request->get("question");

        if (!empty($groupId) && $groupId > 0) {
            $q = ChatbotQuestionExample::updateOrCreate(
                ["chatbot_question_id" => $groupId, "question" => $question],
                ["chatbot_question_id" => $groupId, "question" => $question]
            );
            WatsonManager::pushQuestion($groupId);
        } else if (!empty($name)) {

            $chQuestion = null;

            if (is_numeric($name)) {
                $chQuestion = ChatbotQuestion::where("id", $name)->first();
            }

            if (!$chQuestion) {
                $chQuestion = ChatbotQuestion::create([
                    "value" => str_replace(" ", "_", preg_replace('/\s+/', ' ', $name)),
                ]);
            }

            $groupId = $chQuestion->id;

            if (is_string($question)) {
                ChatbotQuestionExample::create(
                    ["chatbot_question_id" => $chQuestion->id, "question" => preg_replace("/\s+/", " ", $question)]
                );
            } elseif (is_array($question)) {
                foreach ($question as $key => $qRaw) {
                    ChatbotQuestionExample::create(
                        ["chatbot_question_id" => $chQuestion->id, "question" => preg_replace("/\s+/", " ", $qRaw)]
                    );
                }
            }
        }

        if ($groupId > 0) {
            WatsonManager::pushQuestion($groupId);
        }

        return response()->json(["code" => 200]);

    }

    public function search(Request $request)
    {
        $keyword     = request("term", "");
        $allquestion = ChatbotQuestion::where("value", "like", "%" . $keyword . "%")->limit(10)->get();

        $allquestionList = [];
        if (!$allquestion->isEmpty()) {
            foreach ($allquestion as $all) {
                $allquestionList[] = ["id" => $all->id, "text" => $all->value];
            }
        }

        return response()->json(["incomplete_results" => false, "items" => $allquestionList, "total_count" => count($allquestionList)]);

    }

    public function saveAnnotation(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'chatbot_keyword_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => []]);
        }

        $model = \App\ChatbotIntentsAnnotation::updateOrCreate(
            [
                "question_example_id" => $request->get("question_example_id"),
                "chatbot_keyword_id"  => $request->get("chatbot_keyword_id"),
                "start_char_range"    => $request->get("start_char_range"),
                "end_char_range"      => $request->get("end_char_range"),
            ],
            $request->all()
        );

        if ($model) {
            $chatbotKeywordValue = \App\ChatbotKeywordValue::create([
                "chatbot_keyword_id" => $model->chatbot_keyword_id,
                "value"              => $request->get("keyword_value"),
            ]);
            $model->chatbot_value_id = $chatbotKeywordValue->id;
            $model->save();
            WatsonManager::pushValue($model->question_example_id);
        }

        return response()->json(["code" => 200]);
    }

}
