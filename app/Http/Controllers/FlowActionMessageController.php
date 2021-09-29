<?php

namespace App\Http\Controllers;

use App\FlowActionMessage;
use Illuminate\Http\Request;

class FlowActionMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = FlowActionMessage::query();

        // if ($request->id) {
        //     $query = $query->where('id', $request->id);
        // }
        // if ($request->term) {
        //     $query = $query->where('name', 'LIKE', '%' . $request->term . '%')->orWhere('email', 'LIKE', '%' . $request->term . '%')
        //         ->orWhere('phone', 'LIKE', '%' . $request->term . '%');
        // }

        $data = $query->orderBy('sender_name', 'asc')->paginate(25)->appends(request()->except(['page']));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('users.partials.list-users', compact('data'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $data->render(),
                'count' => $data->total(),
            ], 200);
        }
        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('flows.messages.details');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'sender_name'       => 'required',
            'sender_email_address'  => 'required',
            'subject' => 'required',
            'html_content' => 'required',
        ]);

        $input    = $request->all();

        if(isset($request->sender_email_as_reply_to)){
            $input['sender_email_as_reply_to']=true;
        }else{
            $input['sender_email_as_reply_to']=false;
        }
       
        FlowActionMessage::create($input);
        return back()->with('success', 'Added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FlowActionMessage  $flowActionMessage
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FlowActionMessage  $flowActionMessage
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $flowmeaage = FlowActionMessage::find($id);
        return view('flows.messages.details',compact('flowmeaage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FlowActionMessage  $flowActionMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->validate($request, [
            'sender_name'       => 'required',
            'sender_email_address'  => 'required',
            'subject' => 'required',
            'html_content' => 'required',
        ]);

        $input    = $request->all();

        if(isset($request->sender_email_as_reply_to)){
            $input['sender_email_as_reply_to']=true;
        }else{
            $input['sender_email_as_reply_to']=false;
        }
       
        FlowActionMessage::create($input);
        return back()->with('success', 'Added successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FlowActionMessage  $flowActionMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
