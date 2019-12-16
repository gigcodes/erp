<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\HashTag;
use App\InstagramPosts;
use App\InstagramPostsComments;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class InstagramPostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * get all the posts from Instagram saved in instagram_posts table
     */
    public function index()
    {
        //$accounts = Account::where('platform', 'instagram')->get();
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * Create a new entry with image + account_id
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required',
            'account_id' => 'required'
        ]);

        $account = Account::findOrFail($request->get('account_id'));

        $instagram = new Instagram();

        try {
            $instagram->login($account->last_name, $account->password);
        } catch (\Exception $exception) {
            dd($exception);
            return redirect()->back()->with('message', 'Account could not log in!');
        }

        $image = $request->file('image');

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

        $media = MediaUploader::fromSource($image)
            ->useFilename(md5(time()))
            ->toDirectory('instagramposts/' . floor($instagramPost->id / config('constants.image_per_folder')))
            ->upload();

        $instagramPost->attachMedia($media, 'gallery');
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
     * @param  \App\InstagramPosts $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function show(InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InstagramPosts $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function edit(InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\InstagramPosts $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InstagramPosts $instagramPosts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InstagramPosts $instagramPosts
     * @return \Illuminate\Http\Response
     */
    public function destroy(InstagramPosts $instagramPosts)
    {
        //
    }

    public function apiPost(Request $request)
    {
        // Get raw body
        $payLoad = json_decode(request()->getContent(), true);

        // NULL? No valid JSON
        if ($payLoad == null) {
            return response()->json([
                'error' => 'Invalid json'
            ], 400);
        }

        // Process input
        if (is_array($payLoad) && count($payLoad) > 0) {
            // Loop over posts
            foreach ($payLoad as $postJson) {
                // Set tag
                $tag = $postJson[ 'Tags' ];
                $tag = 'SoloLuxury';

                // Get hashtag ID
                $hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);

                // Retrieve instagram post or initiate new
                $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson[ 'URL' ]]);
                $instagramPost->hashtag_id = $hashtag->id;
                $instagramPost->username = 'nobody';
                $instagramPost->posted_at = '2000-01-01 00:00:00';
                $instagramPost->media_type = !empty($postJson[ 'Image' ]) ? 'image' : 'other';
                $instagramPost->media_url = !empty($postJson[ 'Image' ]) ? $postJson[ 'Image' ] : $postJson[ 'URL' ];
                $instagramPost->source = 'instagram';
                $instagramPost->save();

                // Store media
                if (!empty($postJson[ 'Image' ])) {
                    if ($instagramPost->hasMedia('instagram-post')) {
                        $media = MediaUploader::fromSource($postJson[ 'Image' ])
                            ->toDisk('uploads')
                            ->toDirectory('social-media/instagram-posts/' . floor($instagramPost->id / 1000))
                            ->useFilename($instagramPost->id)
                            ->beforeSave(function (\Plank\Mediable\Media $model, $source) {
                                $model->setAttribute('extension', 'jpg');
                            })
                            ->upload();
                        $instagramPost->attachMedia($media, 'instagram-post');
                    }
                }

                // Comments
                if (isset($postJson[ 'Comments' ]) && is_array($postJson[ 'Comments' ])) {
                    // Loop over comments
                    foreach ($postJson[ 'Comments' ] as $comment) {
                        // Set hash
                        $commentHash = md5($comment[ 'Owner' ] . $comment[ 'Comments' ][ 0 ] . $comment[ 'Time' ]);

                        $instagramPostsComment = InstagramPostsComments::firstOrNew(['comment_id' => $commentHash]);
                        $instagramPostsComment->instagram_post_id = $instagramPost->id;
                        $instagramPostsComment->comment_id = $commentHash;
                        $instagramPostsComment->username = $comment[ 'Owner' ];
                        $instagramPostsComment->comment = $comment[ 'Comments' ][ 0 ];
                        $instagramPostsComment->posted_at = date('Y-m-d H:i:s', strtotime($comment[ 'Time' ]));
                        $instagramPostsComment->save();
                    }
                }
            }
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }
}
