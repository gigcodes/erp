<?php

namespace App\Http\Controllers;

use Auth;
use App\AutoReply;
use App\Setting;
use App\ScheduledMessage;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ChatMessageWord;
use App\ChatBotKeywordGroup;
use App\ChatBotPhraseGroup;

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
        $groupKeywords = ChatBotKeywordGroup::all();
        $groupPhrases = ChatBotPhraseGroup::all();
        $mostUsedWords = ChatMessageWord::with('pharases')->take(2);
  
        return view('autoreplies.index', [
            'auto_replies' => $auto_replies,
            'simple_auto_replies' => $simple_auto_replies,
            'priority_customers_replies' => $priority_customers_replies,
            'show_automated_messages' => $show_automated_messages,
            'mostUsedWords' => $mostUsedWords,
            'groupPhrases' => $groupPhrases,
            'groupKeywords' => $groupKeywords,
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

            $lastReplies = \App\ChatMessage::join("customers c","c.id","chat_messages.customer_id")->where("chat_messages.id",">",$id)
            ->where("customer_id",$customerId)
            ->where("number","!=", "c.phone")
            ->orderBy("id","ASC")
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
        //Getting keyword in array
        foreach ($keywords as $keyword) {

            //Place Api Here For Keywords
          $keywordSave = new ChatBotKeywordGroup();
          $keywordSave->keyword_id = $keyword;
          $keywordSave->group_name = $name;
          $keywordSave->save();

        }
        return response()->json(["response" => 200]);
    }   

    public function saveGroupPhrases(Request $request)
    {
       $phrases = $request->phraseId;
       $keyword = $request->keyword;
       $name = $request->name;
       //Getting Phrase in array
       foreach ($phrases as $phrase) {
            //Place Api Here For phrase
           $phraseSave = new ChatBotPhraseGroup();
           $phraseSave->keyword_id = (int)$keyword;
           $phraseSave->phrase_id = (int)$phrase;
           $phraseSave->group_name = $name;
           $phraseSave->save();
        }
        return response()->json(["response" => 200]);
    }

}
