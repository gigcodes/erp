<?php

namespace App\Http\Controllers;

use Auth;
use App\AutoReply;
use App\Setting;
use App\ScheduledMessage;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ChatbotKeyword;
use App\ChatbotKeywordValue;
use App\ChatMessagePhrase;
use App\ChatbotQuestion;
use App\ChatbotQuestionExample;
use App\ChatMessageWord;
use App\ChatbotDialog;
use App\Library\Watson\Model as WatsonManager;

class AutoReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $simple_auto_replies = AutoReply::where('type', 'simple')->latest()->get()->groupBy('reply')->toArray();
        $priority_customers_replies = AutoReply::where('type', 'priority-customer')->latest()->paginate(Setting::get('pagination'), ['*'], 'priority-page');
        $auto_replies = AutoReply::where('type', 'auto-reply')->latest()->paginate(Setting::get('pagination'), ['*'], 'autoreply-page');
        $show_automated_messages = Setting::get('show_automated_messages');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($simple_auto_replies, $perPage * ($currentPage - 1), $perPage);

        $simple_auto_replies = new LengthAwarePaginator($currentItems, count($simple_auto_replies), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
        
        return view('autoreplies.index', [
            'auto_replies' => $auto_replies,
            'simple_auto_replies' => $simple_auto_replies,
            'priority_customers_replies' => $priority_customers_replies,
            'show_automated_messages' => $show_automated_messages
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|string',
            'keyword' => 'sometimes|nullable|string',
            'reply' => 'required|min:3|string',
            'sending_time' => 'sometimes|nullable|date',
            'repeat' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|nullable|integer'
        ]);

        $exploded = explode(',', $request->keyword);

        foreach ($exploded as $keyword) {
            $auto_reply = new AutoReply;
            $auto_reply->type = $request->type;
            $auto_reply->keyword = trim($keyword);
            $auto_reply->reply = $request->reply;
            $auto_reply->sending_time = $request->sending_time;
            $auto_reply->repeat = $request->repeat;
            $auto_reply->is_active = $request->is_active;
            $auto_reply->save();
        }

        if ($request->type == 'priority-customer') {
            if ($request->repeat == '') {
                $customers = Customer::where('is_priority', 1)->get();

                foreach ($customers as $customer) {
                    ScheduledMessage::create([
                        'user_id' => Auth::id(),
                        'customer_id' => $customer->id,
                        'message' => $auto_reply->reply,
                        'sending_time' => $request->sending_time
                    ]);
                }
            }
        }

        return redirect()->route('autoreply.index')->withSuccess('You have successfully created a new auto-reply!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required|string',
            'keyword' => 'sometimes|nullable|string',
            'reply' => 'required|min:3|string',
            'sending_time' => 'sometimes|nullable|date',
            'repeat' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|nullable|integer'
        ]);

        $auto_reply = AutoReply::find($id);
        $auto_reply->type = $request->type;
        $auto_reply->keyword = $request->keyword;
        $auto_reply->reply = $request->reply;
        $auto_reply->sending_time = $request->sending_time;
        $auto_reply->repeat = $request->repeat;
        $auto_reply->is_active = $request->is_active;
        $auto_reply->save();

        return redirect()->route('autoreply.index')->withSuccess('You have successfully updated auto reply!');
    }

    public function updateReply(Request $request, $id)
    {
        $auto_reply = AutoReply::find($id);
        $auto_reply->reply = $request->reply;
        $auto_reply->save();

        return response('success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AutoReply::find($id)->delete();

        return redirect()->route('autoreply.index')->withSuccess('You have successfully deleted auto reply!');
    }

    public function deleteChatWord(Request $request)
    {
        $id = $request->get("id");
        
        if($id > 0) {
           \App\ChatMessagePhrase::where("word_id",$id)->delete();
           \App\ChatMessageWord::where("id",$id)->delete();

           return response()->json(["code" => 200]);
        }

        return response()->json(["code" => 500]);

    }

    public function getRepliedChat(Request $request, $id)
    {
        $currentMessage = \App\ChatMessage::where("id", $id)->first();

        if($currentMessage) {
            $customerId = $currentMessage->customer_id;

            $lastReplies = \App\ChatMessage::join("customers","customers.id","chat_messages.customer_id")->where("chat_messages.id",">",$id)
            ->where("customer_id",$customerId)
            ->whereNull("number")
            ->orderBy("chat_messages.id","ASC")
            ->limit(5)->get();

            return response()->json(["code" => 200,"data" => $lastReplies,"question" => $currentMessage->message]);

        }

        return response()->json(["code" => 200,"data" => []]);


    }

    public function saveByQuestion(Request $request)
    {
        $question  = $request->get("q");
        $answer = $request->get("a");

        \App\AutoReply::updateOrCreate([
            "type" => "auto-reply",
            "keyword" => $question
        ],[
            "type" => "auto-reply",
            "keyword" => $question,
            "reply" => $answer
        ]);

        return response()->json(["code" => 200]);


    }

    public function saveGroup(Request $request){
        $keywords = $request->id;
        $name = $request->name;
        $group = $request->keyword_group;
        
        //Check Existing Group
        if($group != ''){
            $group = ChatbotKeyword::find($group);
            $groupId = $group->id; 
        }else{
            //Create Group 
            $group = new ChatbotKeyword();
            $group->keyword = str_replace(" ", "_", preg_replace('/\s+/', ' ', $name));
            $group->save();
            $groupId = $group->id; 
        }

        if(!empty($keywords) && is_array($keywords)) {
            $words = ChatMessageWord::whereIn("id",$keywords)->get();
            if(!$words->isEmpty()) {
                foreach($words as $word) {
                    //Check If Group ALready Exist
                    $checkExistingGroup  = ChatbotKeywordValue::where('chatbot_keyword_id',$groupId)->where('value',$word->word)->first();
                    if($checkExistingGroup == null){
                        $keywordSave = new ChatbotKeywordValue();
                        $keywordSave->chatbot_keyword_id = $groupId;
                        $keywordSave->value = preg_replace("/\s+/", " ", $word->word);
                        $keywordSave->save();
                    }
                }
            }
        }
        // call api to store data
        WatsonManager::pushKeyword($groupId);

        return response()->json(["response" => 200]);
    }   

    public function saveGroupPhrases(Request $request)
    {
       $phrasesReq = $request->phraseId;
       $keyword = $request->keyword;
       $name = $request->name;
       $group = $request->phrase_group;

       //Check Existing Group
        if($group != ''){
            $group = ChatbotQuestion::find($group);
            $groupId = $group->id; 
        }else{
            //Create Group 
            $group = new ChatbotQuestion();
            $group->value = str_replace(" ", "_", preg_replace('/\s+/', ' ', $name));
            $group->save();
            $groupId = $group->id; 
        }


       //Getting Phrase in array
       if(!empty($phrasesReq) && is_array($phrasesReq)) {
          $phrase = ChatMessagePhrase::whereIn("id",$phrasesReq)->get();
          if(!$phrase->isEmpty()) {
             foreach($phrase as $rec) {
                $checkExistingGroup  = ChatbotQuestionExample::where('chatbot_question_id',$groupId)->where('question',$rec->phrase)->first();
                if($checkExistingGroup == null){
                    //Place Api Here For Keywords
                    $phraseSave = new ChatbotQuestionExample();
                    $phraseSave->chatbot_question_id = $groupId;
                    $phraseSave->question = preg_replace("/\s+/", " ", $rec->phrase);
                    $phraseSave->save();

                }
             }
        }
       }

        // call api to store data
        WatsonManager::pushQuestion($groupId);
       
       return response()->json(["response" => 200]);
    }

    public function mostUsedWords(Request $request)
    {
        $groupKeywords = \App\ChatbotKeyword::all();
        $groupPhrases = \App\ChatbotQuestion::all();
        $mostUsedWords = \App\ChatMessageWord::get()->take(3);

        $allSuggestedOptions = \App\ChatbotDialog::allSuggestedOptions();
        
        return view("autoreplies.most-used-words",[
            'mostUsedWords' => $mostUsedWords,
            'groupPhrases' => $groupPhrases,
            'groupKeywords' => $groupKeywords,
            'allSuggestedOptions' => $allSuggestedOptions
        ]);
    }

}
