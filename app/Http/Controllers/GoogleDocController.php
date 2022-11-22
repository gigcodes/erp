<?php

namespace App\Http\Controllers;

use App\GoogleDoc;
use App\GoogleFiletranslatorFile;
use Illuminate\Http\Request;

class GoogleDocController extends Controller
{
    public function index(Request $request)
    {
        $data = GoogleDoc::orderBy('created_at', 'desc')->get();

        return view('googledoc.index', compact('data'));
    }
}
