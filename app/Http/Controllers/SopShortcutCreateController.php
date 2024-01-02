<?php

namespace App\Http\Controllers;

use App\Sop;
use Illuminate\Http\Request;

class SopShortcutCreateController extends Controller
{
    public function createShortcut(Request $request)
    {
        $sop = new Sop;
        $sop->name = $request->name;
        $sop->category = $request->category;
        $sop->chat_message_id = $request->chat_message_id;
        $sop->content = $request->description;
        $sop->save();

        return response()->json(['status' => true, 'message' => 'Sop Created Successfully']);
    }
}
