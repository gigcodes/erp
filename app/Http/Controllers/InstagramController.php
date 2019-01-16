<?php

namespace App\Http\Controllers;

use App\Services\Instagram\Instagram;
use Illuminate\Http\Request;

class InstagramController extends Controller
{
    private $instagram;

    public function __construct(Instagram $instagram)
    {
        $this->instagram = $instagram;

    }

    public function index() {

    }


    /**
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * This method gives the list of posts
     * that is in Instagram account
     */
    public function showPosts() {
        $posts = $this->instagram->getMedia();

        return view('instagram.index', compact(
            'posts'
        ));
    }

    /**
     * @param Request $request
     * This method will store photo to
     * Instagram Business account
     */
    public function store(Request $request) {
        $this->validate($request, [
           'image' => 'required|image'
        ]);
    }
}
