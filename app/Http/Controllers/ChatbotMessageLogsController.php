<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatbotMessageLog;
use App\Setting;

class ChatbotMessageLogsController extends Controller
{
    public function index(Request $request)
    {
        // Get results
       
        $logListMagentos = \App\ChatbotMessageLog::orderBy('chatbot_message_logs.id', 'DESC');
        $logListMagentos->leftjoin('customers', function($join)
        {
            $join->on('chatbot_message_logs.model_id', '=', 'customers.id');
            $join->where('model','=', 'customers');
        });

            if ($request->name !='')
             $logListMagentos->where('customers.name',$request->name);
             if ($request->email !='')
             $logListMagentos->where('customers.email',$request->email);
             if ($request->phone !='')
             $logListMagentos->where('customers.phone',$request->phone);
        


        // Get paginated result
        $logListMagentos->select('chatbot_message_logs.*','customers.name as cname');
        $logListMagentos = $logListMagentos->paginate(Setting::get('pagination'));
        $total_count     = $logListMagentos->total();
        // Show results
        if ($request->ajax())
        {
            return view('chatboat_message_logs.index_ajax', compact('logListMagentos','total_count'))
            ->with('success', \Request::Session()->get("success"));
        }
        else
        {
            return view('chatboat_message_logs.index', compact('logListMagentos','total_count'))
            ->with('success', \Request::Session()->get("success"));
        }
 
       
    }

    public function chatbotMessageLogHistory(Request $request,$id)
    {
        $response = \App\ChatbotMessageLogResponse::where("chatbot_message_log_id",$id)->get();

        return view("chatboat_message_logs.history",compact('response'));
    }
}
