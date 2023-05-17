<?php

namespace App\Http\Controllers\ChatGPT;

use App\ChatGptResponses;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatGPTController extends Controller
{

    public function index(Request $request)
    {
        $responses = ChatGptResponses::where(function ($query) use ($request) {
            if ($request->has('prompt') && $request->prompt) {
                $query->where('prompt', 'like', '%' . $request->prompt . '%');
            }
            if ($request->has('response') && $request->response) {
                $query->where('response', 'like', '%' . $request->response . '%');
            }
        })->orderBy('id', 'desc')->paginate(Setting::get('pagination'), ['*'], 'chat_gpt_responses');

        return view('chat-gpt.index', compact('responses'));
    }

    /**
     * Get completion from chat GPT.
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompletions(Request $request): JsonResponse
    {
        try {
            $prompt = ChatGptResponses::where('prompt', 'like', '%' . $request->get('question') . '%')->first();
            if ($prompt) {
                return response()->json(['status' => true, 'message' => 'Response found', 'data' => $prompt->response]);
            }
            $chatGpt = new ChatGPTService();
            $response = $chatGpt->getCompletions($request->get('question'));
            if (!$response['status']) {
                return response()->json(['status' => false, 'message' => $response['message']]);
            } else {
                $insertData = [
                    'prompt' => $request->get('question'),
                    'response' => $response['data']['choices'][0]['text'],
                    'response_data' => serialize($response['data'])
                ];
                ChatGptResponses::create($insertData);
                return response()->json(['status' => true, 'message' => 'Response found', 'data' => $response['data']['choices'][0]['text']]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

}
