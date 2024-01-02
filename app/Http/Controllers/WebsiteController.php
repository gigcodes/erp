<?php

namespace App\Http\Controllers;

use App\StoreWebsite;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $websites = StoreWebsite::all();

        return view('StoreWebsite.index', compact('websites'));
    }
}
