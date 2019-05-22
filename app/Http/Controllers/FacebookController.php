<?php

namespace App\Http\Controllers;

use App\GroupMembers;
use App\HashtagPosts;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    public function index() {
        $posts = HashtagPosts::all();

        return view('scrap.facebook', compact('posts'));
    }

    public function show($type) {
        if ($type == 'group') {
            $groups = GroupMembers::all();

            return view('scrap.facebook_groups', compact('groups'));
        }
    }
}
