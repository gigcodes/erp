<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogsController extends Controller
{

    public function index(Request $request)
    {
        $inputs = $request->input();
        $data = \App\SearchAttachedImagesLog::with('customer')->latest()->paginate(15);

        return view('image-logs.index', compact('data'));
    }

    public function deleteLog(Request $request)
    {
        $logId = $request->id;
        $deleted = \App\SearchAttachedImagesLog::where('id', $logId)->delete();

        return response()->json(['code' => 200, 'message' => 'Log deleted successfully']);
    }

}
