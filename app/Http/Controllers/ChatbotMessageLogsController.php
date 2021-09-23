<?php

namespace App\Http\Controllers;
use App\Library\Watson\Model as WatsonManager;
use Illuminate\Http\Request;
use App\ChatbotMessageLog;
use App\Setting;
use App\ChatbotQuestionExample;

use Illuminate\Support\Facades\Validator;

class ChatbotMessageLogsController extends Controller
{
    public function index(Request $request)
    {
        // Get results

        $logListMagentos = \App\ChatbotMessageLog::orderBy('chatbot_message_logs.id', 'DESC');
        $logListMagentos->leftjoin('customers', function ($join) {
            $join->on('chatbot_message_logs.model_id', '=', 'customers.id');
            $join->where('model', '=', 'customers');
        });

        if ($request->name != '') {
            $logListMagentos->where('customers.name', $request->name);
        }

        if ($request->email != '') {
            $logListMagentos->where('customers.email', $request->email);
        }

        if ($request->phone != '') {
            $logListMagentos->where('customers.phone', $request->phone);
        }

        // Get paginated result
        $logListMagentos->select('chatbot_message_logs.*', 'customers.name as cname');
        $logListMagentos = $logListMagentos->paginate(Setting::get('pagination'));
        $total_count     = $logListMagentos->total();
        $allCategory = \App\ChatbotCategory::all();
        $allCategoryList = [];
        $watson_accounts =  \App\WatsonAccount::all();
        if (!$allCategory->isEmpty()) {
            foreach ($allCategory as $all) {
                $allCategoryList[] = ["id" => $all->id, "text" => $all->name];
            }
        }
        // Show results
        if ($request->ajax())
        {
            return view('chatboat_message_logs.index_ajax', compact('logListMagentos','total_count','allCategoryList','watson_accounts'))
            ->with('success', \Request::Session()->get("success"));
        }
        else
        {
            return view('chatboat_message_logs.index', compact('logListMagentos','total_count','allCategoryList','watson_accounts'))
            ->with('success', \Request::Session()->get("success"));
        }
 
       
    }

    public function chatbotMessageLogHistory(Request $request, $id)
    {
        $response = \App\ChatbotMessageLogResponse::where("chatbot_message_log_id", $id)->get();

        return view("chatboat_message_logs.history", compact('response'));
    }


    public function pushwaston(Request $request)
    {
        /*$params = [
            "name"      => $request->get("dialog_type", 'node') == "node" ? "solo_" . time() : "solo_project_" . time(),
            "parent_id" => $request->get("parent_id", 0),
            "dialog_type" => $request->get("dialog_type", 'node')
        ];
        $previousNode = $request->get("previous_node", 0);
        if ($previousNode > 0) {
            $params["previous_sibling"] = $previousNode;
        }else{
        	$params["previous_sibling"] = 0;
        }
        $params["response_type"] = "standard";

        //$siblingNode = ChatbotDialog::where("previous_sibling", 0)->first();
        $dialog = \App\ChatbotDialog::create($params);

        $result        = json_decode(WatsonManager::pushDialog($dialog->id));*/
       

        $params          = $request->all();
        $params["value"] = str_replace(" ", "_", $params["value"]);
        $params["watson_account_id"] = $request->watson_account;
        $validator = Validator::make($params, [
            'value' => 'required|unique:chatbot_questions|max:255',
            'keyword_or_question' => 'required',
            'watson_account'  => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(["code" => 500, "error" => $validator->errors()]);
        }

        if($request->keyword_or_question  == 'simple' || $request->keyword_or_question  == 'priority-customer') {
            $validator = Validator::make($request->all(), [
                'keyword'      => 'sometimes|nullable|string',
                'suggested_reply'        => 'required|min:3|string',
                'sending_time' => 'sometimes|nullable|date',
                'repeat'       => 'sometimes|nullable|string',
                'is_active'    => 'sometimes|nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 500, "error" => $validator->errors()]);
            }
        }
        

        $chatbotQuestion = \App\ChatbotQuestion::create($params);
        if (!empty($params["question"])) {
            foreach($params["question"] as $qu) {
                if($qu) {
                    $params["chatbot_question_id"] = $chatbotQuestion->id;
                    $chatbotQuestionExample        = new ChatbotQuestionExample;
                    $chatbotQuestionExample->question = $qu;
                    $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                    $chatbotQuestionExample->save();
                }
            }
        }


        if (array_key_exists("types",$params) && $params["types"] != NULL && array_key_exists("type",$params) && $params["type"] != NULL) {
            $chatbotQuestionExample = null;
            if(!empty($params["value_name"])) {
                $chatbotQuestionExample        = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = $params["value_name"];
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->types = $params["types"];
                $chatbotQuestionExample->save();
            }

            if($chatbotQuestionExample) {
                $valueType = [];
                $valueType["chatbot_keyword_value_id"] =  $chatbotQuestionExample->id;
                if(!empty($params["type"])) {
                    foreach ($params["type"] as $value) {
                        if($value != NULL) {
                            $valueType["type"] = $value;
                            $chatbotKeywordValueTypes = new ChatbotKeywordValueTypes;
                            $chatbotKeywordValueTypes->fill($valueType);
                            $chatbotKeywordValueTypes->save();
                        }
                    }
                }
            }
        }


        if($request->keyword_or_question  == 'simple' || $request->keyword_or_question  == 'priority-customer') {
            $exploded = explode(',', $request->keyword);
    
            foreach ($exploded as $keyword) {

                $chatbotQuestionExample        = new ChatbotQuestionExample;
                $chatbotQuestionExample->question = trim($keyword);
                $chatbotQuestionExample->chatbot_question_id = $chatbotQuestion->id;
                $chatbotQuestionExample->save();
            }
    
            if ($request->type == 'priority-customer') {
                if ($request->repeat == '') {
                    $customers = Customer::where('is_priority', 1)->get();
    
                    foreach ($customers as $customer) {
                        ScheduledMessage::create([
                            'user_id'      => Auth::id(),
                            'customer_id'  => $customer->id,
                            'message'      => $chatbotQuestion->suggested_reply,
                            'sending_time' => $request->sending_time,
                        ]);
                    }
                }
            }
        }


        if( $params['watson_account'] > 0 ){
            $wotson_account_ids =  \App\WatsonAccount::where( 'id', $request->watson_account )->get();
        }else{
            $wotson_account_ids =  \App\WatsonAccount::all();
        }
        
        foreach($wotson_account_ids as $id){
            $data_to_insert[] = [
                'suggested_reply' => $params["suggested_reply"],
                'store_website_id' => $id->store_website_id,
                'chatbot_question_id' => $chatbotQuestion->id
            ];
        }
        \App\ChatbotQuestionReply::insert($data_to_insert);


        if($request->erp_or_watson == 'watson') {
            if($request->keyword_or_question == 'intent' || $request->keyword_or_question == 'simple' || $request->keyword_or_question == 'priority-customer') {

                \App\ChatbotQuestion::where( 'id', $chatbotQuestion->id )->update([ 'watson_status' => 'Pending watson send' ]);

                $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, null, $request->watson_account));
                $this->createdialog($request->value);
                return redirect()->back();
            }

            if($request->keyword_or_question == 'entity') {
                
                \App\ChatbotQuestion::where( 'id', $chatbotQuestion->id )->update([ 'watson_status' => 'Pending watson send' ]);

                $result = json_decode(WatsonManager::pushQuestion($chatbotQuestion->id, null, $request->watson_account));
                $this->createdialog($request->value);
                return redirect()->back();
            }

            
           
        }
               

       
       


       
    } 

    public function createdialog($name)
    {
        $params = [
            "name"      =>$name,
            "parent_id" => 0,
            "dialog_type" =>'node',
            "response_type"=> "standard",
            "previous_sibling"=>0
        ];
        
        $dialog = \App\ChatbotDialog::create($params);
        //$result        = json_decode(WatsonManager::pushDialog($dialog->id));
    }

    

}
