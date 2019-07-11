<?php

namespace App\Http\Controllers;

use App\BrandFans;
use App\GroupMembers;
use App\HashtagPosts;
use App\Services\Facebook\Facebook;
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
        if ($type == 'brand') {
            $brands = BrandFans::all();

            return view('scrap.facebook_brand_fans', compact('brands'));
        }
    }

    public function getInbox() {
        $facebook = new Facebook(new \Facebook\Facebook());

        return $facebook->getConversations();

    }
}
