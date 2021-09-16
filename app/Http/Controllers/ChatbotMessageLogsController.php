<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatbotMessageLog;

class ChatbotMessageLogsController extends Controller
{
    public function index(Request $request)
    {
        // Get results
        $logListMagentos = \App\ChatbotMessageLog::orderBy('id', 'DESC');

        // Get paginated result
        $logListMagentos->select('*');
        $logListMagentos = $logListMagentos->paginate(25);
        $total_count     = $logListMagentos->total();
        // Show results
        return view('chatboat_message_logs.index', compact('logListMagentos','total_count'))
            ->with('success', \Request::Session()->get("success"));
    }

    public function chatbotMessageLogHistory(Request $request,$id)
    {
        $response = \App\ChatbotMessageLogResponse::where("chatbot_message_log_id",$id)->get();

        return view("chatboat_message_logs.history",compact('response'));
    }
}
