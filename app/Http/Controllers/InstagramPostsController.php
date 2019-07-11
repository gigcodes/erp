<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\InstagramPosts;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class InstagramPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::where('platform', 'instagram')->get();
        $posts = InstagramPosts::all();

        return view('instagram.posts.index', compact('accounts', 'posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required',
            'account_id' => 'required'
        ]);

        $account  = Account::findOrFail($request->get('account_id'));

        $instagram  = new Instagram();
        try {
            $instagram->login($account->last_name, $account->password);
        } catch (\Exception $exception) {
            dd($exception);
            return redirect()->back()->with('message', 'Account could not log in!');
        }

        $image = $request->file('image');

        $media = MediaUploader::fromSource($image)->useFilename(md5(time()))->upload();

        $instagramPost = new InstagramPosts();
        $instagramPost->user_id = \Auth::user()->id;
        $instagramPost->account_id = $account->id;
        $instagramPost->caption = $request->get('caption') ?? 'N/A';
        $instagramPost->source = 'manual_post';
        $instagramPost->posted_at = date('Y-m-d');
        $instagramPost->media_url = 'N/A';
        $instagramPost->media_type = 'image';
        $instagramPost->post_id = 0;
        $instagramPost->username = $account->last_name;
        $instagramPost->save();

        $instagramPost->attachMedia($media,  'gallery');
        $instagramPost->save();

        $media = $instagramPost->getMedia('gallery')->first();

        $source = imagecreatefromjpeg($media->getAbsolutePath());
        list($width, $height) = getimagesize($media->getAbsolutePath());

        $newwidth = 800;
        $newheight = 800;

        $destination = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        imagejpeg($destination, $media->getAbsolutePath(), 100);

        $metaData = [];

        if ($request->get('caption') != '') {
            $metaData = [
                'caption' => $request->get('caption')
            ];
        }

        try {
            $instagram->timeline->uploadPhoto($media->getAbsolutePath(), $metaData);
        } catch (\Exception $exception) {
            $instagramPost->detachMediaTags('gallery');
            $instagramPost->delete();
            return redirect()->back()->with('message', 'Image could not be uploaded to Instagram.');
        }

        return redirect()->back()->with('message', 'Image posted successfully!');



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InstagramPosts  $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function show(InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InstagramPosts  $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function edit(InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InstagramPosts  $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InstagramPosts  $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstagramPosts $instagramPosts)
    {
        //
    }
}
