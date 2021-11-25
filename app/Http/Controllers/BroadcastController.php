<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BroadcastController extends Controller
{

    public function index(Request $request)
    {
        $inputs = $request->input();
        $data = \App\BroadcastMessage::with('numbers')->latest()->paginate(15);

        return view('broadcast-messages.index', compact('data'));
    }

    public function deleteMessage(Request $request)
    {
        $ID = $request->id;
        $deleted = \App\BroadcastMessage::where('id', $ID)->delete();

        return response()->json(['code' => 200, 'message' => 'Message deleted successfully']);
    }

    public function messagePreviewNumbers(Request $request)
    {
        $id = $request->id;
        $lists = \App\BroadcastMessageNumber::where('broadcast_message_id', $id)->orderBy('id', 'DESC')->get();

        return response()->json(["code" => 200, "data" => $lists]);
    }

}
