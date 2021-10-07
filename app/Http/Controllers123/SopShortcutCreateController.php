<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sop;

class SopShortcutCreateController extends Controller
{
    public function createShortcut(Request $request)
    {
        $sop = new Sop;
        $sop->name = $request->name;
        $sop->content = $request->description;
        $sop->save();
        return response()->json(['status' =>true , 'message' => "Sop Created Successfully"]);
    }
}
