<?php

namespace App\Http\Controllers;

use App\BusinessPost;
use App\Social\SocialConfig;

class SocialAccountPostController extends Controller
{
    public function index($accountId)
    {
        $account = SocialConfig::find($accountId);
        $posts = BusinessPost::where('social_config_id', $accountId)->orderBy('post_id', 'DESC')->latest('time')->paginate(50);

        return view('social-account.post', compact('account', 'posts'));
    }
}
