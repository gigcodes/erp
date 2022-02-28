<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatbotTypeErrorLog;

class ChatbotTypeErrorLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        $query = ChatbotTypeErrorLog::query();
        $query =  $query->select('chatbot_type_error_logs.id AS chatId', 'chatbot_type_error_logs.type_error', 'store_websites.website','chatbot_type_error_logs.phone_number', 'chatbot_questions.value');
        $query =  $query->leftJoin('store_websites', 'store_websites.id', '=', 'chatbot_type_error_logs.store_website_id');
        $query =  $query->leftJoin('chatbot_questions', 'chatbot_questions.id', '=', 'chatbot_type_error_logs.chatbot_id');
        if($request->id){
			$query = $query->where('chatbot_type_error_logs.id', $request->id);
		}
        if($request->type != null) {
            $query = $query->where('chatbot_type_error_logs.type_error', $request->type);
        }
		$data = $query->orderBy('chatbot_type_error_logs.id', 'asc')->paginate(25)->appends(request()->except(['page']));
		if ($request->ajax()) {
            return response()->json([
                'tbody' => view('chatboat-type-error-log.index_ajax', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string)$data->render(),
                'count' => $data->total(),
            ], 200);
        }
        
		return view('chatboat-type-error-log.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        $affiliates = Affiliates::find($id);

        return response()->json(["code" => 200 , "data" => $affiliates]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request)
    {
        $id = $request->id;
        if(is_array($id)){
            \DB::table('chatbot_type_Error_Logs')
            ->whereIn('id', $id)
            ->delete();
        }else{
            \DB::table('chatbot_type_Error_Logs')
            ->where('id', $id)
            ->delete();
        }

		return redirect()->route('chatboat-type-error-log.index')
			->with('success', 'Type error log deleted successfully');
    }
}
