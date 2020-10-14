<?php

namespace App\Http\Controllers;

use App\BrandFans;
use App\GroupMembers;
use App\HashtagPosts;
use App\ScrappedFacebookUser;
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


    public function apiPost(Request $request)
    {
        $file = $request->file('file');

        $f = File::get($file);

        $payLoad = json_decode($f);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json'
            ], 400);
        }
        if (is_array($payLoad) && count($payLoad) > 0) {
            $payLoad = json_decode(json_encode($payLoad), true);
            foreach ($payLoad as $postJson) {
                if($postJson['Owner'])
                {
                    $inf = ScrappedFacebookUser::where('owner',$postJson['Owner'])->first();
                    if($inf == null)
                    {
                        $scrapeFacebook = new ScrappedFacebookUser;
                        $scrapeFacebook->url = $postJson['URL'];
                        $scrapeFacebook->owner = $postJson['Owner'];
                        $scrapeFacebook->bio = $postJson['Bio'];
                        if(isset($postJson['keyword'])){
                            $scrapeFacebook->keyword = $postJson['keyword'];
                        }
                        $scrapeFacebook->save();
                    }
                }
            }
        }
        return response()->json([
            'ok'
        ], 200);
    } 

}
