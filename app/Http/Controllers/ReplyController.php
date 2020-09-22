<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reply;
use App\Setting;
use App\ReplyCategory;
use App\ChatbotQuestion;

class ReplyController extends Controller
{
    public function __construct() {
    //  $this->middleware('permission:reply-edit',[ 'only' => 'index','create','store','destroy','update','edit']);
    }

    public function index()
    {
      $replies = Reply::oldest()->whereNull('deleted_at')->paginate(Setting::get('pagination'));
      $reply_categories = ReplyCategory::all();
  		return view('reply.index',compact('replies','reply_categories'))
  					->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $data['reply'] = '';
      $data['model'] = '';
  		$data['category_id'] = '';
      $data['modify'] = 0;
      $data['reply_categories'] = ReplyCategory::all();

  		return view('reply.form',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Reply $reply)
    {
      $this->validate($request,[
        'reply'       => 'required|string',
  			'category_id' => 'required|numeric',
  			'model'       => 'required'
  		]);

  		$data = $request->except('_token','_method');

  		$reply->create($data);

      if ($request->ajax()) {
        return response($request->reply);
      }

  		return redirect()->route('reply.index')->with('success','Quick Reply added successfully');
    }

    public function categoryStore(Request $request)
    {
      $this->validate($request, [
        'name'  => 'required|string'
      ]);

      $category = new ReplyCategory;
      $category->name = $request->name;
      $category->save();

      return redirect()->route('reply.index')->with('success', 'You have successfully created category');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
      $data = $reply->toArray();
  		$data['modify'] = 1;
      $data['reply_categories'] = ReplyCategory::all();

  		return view('reply.form',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
      $this->validate($request,[
  			'reply' => 'required|string',
  			'model' => 'required'
  		]);

  		$data = $request->except('_token','_method');

  		$reply->update($data);

  		return redirect()->route('reply.index')->with('success','Quick Reply updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply, Request $request)
    {
      $reply->delete();
      if ($request->ajax()) {
          return response()->json(['message' => "Deleted successfully"]);
      }
  		return redirect()->route('reply.index')->with('success','Quick Reply Deleted successfully');
    }

    public function chatBotQuestion(Request $request)
    {
      $this->validate($request,[
        'intent_name' => 'required',
        'intent_reply' => 'required',
        'intent_model' => 'required',
        'intent_category_id' => 'required'
      ]);


        $ChatbotQuestion = new ChatbotQuestion;
        $ChatbotQuestion->value = 'Intent';
        $ChatbotQuestion->suggested_reply = $request->intent_reply;
        $ChatbotQuestion->category_id = $request->intent_category_id;
        $ChatbotQuestion->keyword_or_question = $request->intent_model;
        $ChatbotQuestion->is_active = 1;
        $ChatbotQuestion->erp_or_watson = 'erp';

        $ChatbotQuestion->save();
        dd();
        return redirect()->route('reply')->with('success','Intent Updated');
      
    }

}
