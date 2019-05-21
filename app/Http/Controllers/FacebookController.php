<?php

namespace App\Http\Controllers;

use App\HashtagPosts;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    public function index() {
        $posts = HashtagPosts::all();

        return view('scrap.facebook', compact('posts'));
    }
}
