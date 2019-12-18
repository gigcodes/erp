<?php

namespace App\Http\Controllers;


\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

use App\Account;
use App\HashTag;
use App\InstagramPosts;
use App\InstagramPostsComments;
use App\Setting;
use Illuminate\Http\Request;
use InstagramAPI\Instagram;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class InstagramPostsController extends Controller
{
    public function index(Request $request)
    {
        // Load posts
        $posts = $this->_getFilteredInstagramPosts($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // Return view
        return view('social-media.instagram-posts.index', compact('posts'));
    }

    public function grid(Request $request)
    {
        // Load posts
        $posts = $this->_getFilteredInstagramPosts($request);

        // Paginate
        $posts = $posts->paginate(Setting::get('pagination'));

        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('social-media.instagram-posts.json_grid', compact('posts'))->render(),
                'links' => (string)$posts->appends($request->all())->render()
            ], 200);
        }

        // Return view
        return view('social-media.instagram-posts.grid', compact('posts', 'request'));
    }

    private function _getFilteredInstagramPosts(Request $request) {
        // Base query
        $instagramPosts = InstagramPosts::orderBy('posted_at', 'DESC')
            ->join('hash_tags', 'instagram_posts.hashtag_id', '=', 'hash_tags.id');

        // Apply hashtag filter
        if (!empty($request->hashtag)) {
            $instagramPosts->where('hash_tags.hashtag', str_replace('#', '', $request->hashtag));
        }

        // Apply author filter
        if (!empty($request->author)) {
            $instagramPosts->where('username', 'LIKE', '%' . $request->author . '%');
        }

        // Apply author filter
        if (!empty($request->post)) {
            $instagramPosts->where('caption', 'LIKE', '%' . $request->post . '%');
        }

        // Return instagram posts
        return $instagramPosts;
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
                $tag = $postJson[ 'Tag used to search' ];

                // Get hashtag ID
                $hashtag = HashTag::firstOrCreate(['hashtag' => $tag]);

                // Retrieve instagram post or initiate new
                $instagramPost = InstagramPosts::firstOrNew(['location' => $postJson[ 'URL' ]]);
                $instagramPost->hashtag_id = $hashtag->id;
                $instagramPost->username = $postJson[ 'Owner' ];
                $instagramPost->caption = $postJson[ 'Original Post' ];
                $instagramPost->posted_at = date('Y-m-d H:i:s', strtotime($postJson[ 'Time of Post' ]));
                $instagramPost->media_type = !empty($postJson[ 'Image' ]) ? 'image' : 'other';
                $instagramPost->media_url = !empty($postJson[ 'Image' ]) ? $postJson[ 'Image' ] : $postJson[ 'URL' ];
                $instagramPost->source = 'instagram';
                $instagramPost->save();

                // Store media
                if (!empty($postJson[ 'Image' ])) {
                    if (!$instagramPost->hasMedia('instagram-post')) {
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
                        // Check if there really is a comment
                        if (isset($comment[ 'Comments' ][ 0 ])) {
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
        }

        // Return
        return response()->json([
            'ok'
        ], 200);
    }
}
