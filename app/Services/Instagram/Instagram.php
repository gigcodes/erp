<?php

namespace App\Services\Instagram;

use App\Image;
use Facebook\Facebook;

class Instagram
{
    private $url = 'https://graph.facebook.com/';

    private $user_access_token;

    private $page_access_token;

    private $page_id;

    private $ad_acc_id;

    private $imageIds = [];

    /**
     * Instagram constructor.
     */
    public function __construct(private Facebook $facebook)
    {
        $this->user_access_token = env('USER_ACCESS_TOKEN', 'EAAD7Te0j0B8BAJKziYXYZCNZB0i6B9JMBvYULH5kIeH5qm6N9E3DZBoQyZCZC0bxZB4c4Rl5gifAqVa788DRaCWXQ2fNPtKFVnEoKvb5Nm1ufMG5cZCTTzKZAM8qUyaDtT0mmyC0zjhv5S9IJt70tQBpDMRHk9XNYoPTtmBedrvevtPIRPEUKns8feYJMkqHS6EZD');
        $this->page_access_token = env('PAGE_ACCESS_TOKEN', 'EAAD7Te0j0B8BAO2yF97qtbFJq2pPzKZBOocsJVU3MZA95wKZBd0VkQtiUAP534GYkXaLXI0xJRNjP3Jrv43GTY84cVofQCqipkEEUNnVrU2ZBuzmR6AdkNcngPF318iIR123ZBw2XT2sWZBgCXrFolAokqFZBcL9eQZBsVs3aZBpyOf8FMuJs4FvLG8J9HJNZBJ9IZD');
        $this->page_id = '507935072915757';
        $this->ad_acc_id = 'act_128125721296439';
        $this->instagram_id = '17841406743743390';
    }

    /**
     * @param  null  $url
     * @return array
     * gets the list of media
     */
    public function getMedia($url = null)
    {
        if ($url === null) {
            $params = 'fields'
                . '='
                . 'id,media_type,media_url,owner{id,username},timestamp,like_count,comments_count,caption';
            $url = $this->instagram_id . '/media?' . $params;
        }

        try {
            //get the media for the url
            $media = $this->facebook->get($url, $this->page_access_token)->getDecodedBody();
        } catch (\Exception $exception) {
            return [];
        }

        $paging = [];

        if (isset($media['paging']['next'])) {
            $paging['next'] = $media['paging']['next'];
        }
        if (isset($media['paging']['previous'])) {
            $paging['previous'] = $media['paging']['previous'];
        }

        // loop through and get the result in the array
        $media = array_map(function ($post) {
            return [
                'id' => $post['id'],
                'comments' => [
                    'summary' => [
                        'total_count' => $post['comments_count'],
                    ],
                    'url' => null,
                ],
                'full_picture' => $post['media_url'] ?? null,
                'permalink_url' => null,
                'name' => $post['name'] ?? 'N/A',
                'message' => $post['caption'] ?? null,
                'created_time' => $post['timestamp'],
                'from' => $post['owner'],
                'likes' => [
                    'summary' => [
                        'total_count' => $post['like_count'],
                    ],
                ],
            ];
        }, $media['data']);

        return [$media, $paging];
    }

    /**
     * @return array
     * Get the comments + replies for the given post ID
     */
    public function getComments($post_id)
    {
        $params = '?fields=username,text,timestamp,id,replies{id,username,text}';
        try {
            //get the comments for the post ID
            $comments = $this->facebook->get($post_id . '/comments' . $params, $this->page_access_token)->getDecodedBody();
            $comments = $comments['data'];
        } catch (\Exception $exception) {
            $comments = [];
        }

        //loop through the comments and get result in an array form
        $comments = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'username' => $item['username'],
                'text' => $item['text'],
                'replies' => isset($item['replies']) ? $item['replies']['data'] : [],
            ];
        }, $comments);

        return $comments;
    }

    /**
     * @return array
     * This will post ID to instagram Post
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function postComment($postId, $message): array
    {
        //post the comment to facebook - from facebook API - postId required to send the comment
        $comment = $this->facebook
            ->post($postId . '/comments',
                [
                    'message' => $message,
                    'fields' => 'id,text,username,timestamp',
                ],
                $this->user_access_token
            )->getDecodedBody();

        $comment['status'] = 'success';

        return $comment;
    }

    /**
     * @return array
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     * This will post the reply for a post
     */
    public function postReply($commentId, $message)
    {
        //send the reply to the comment
        $comment = $this->facebook
            ->post($commentId . '/replies',
                [
                    'message' => $message,
                    'fields' => 'id,text,username,timestamp',
                ],
                $this->user_access_token
            )->getDecodedBody();

        $comment['status'] = 'success';

        return $comment;
    }

    /**
     * @param $message
     * This will post a media to the Instragram sololuxury account
     */
    public function postMedia($images, $message)
    {
    }

    /**
     * @return bool|mixed|null
     * This will post media object to the facebook server.
     */
    private function postMediaObject(Image $image)
    {
        $data['caption'] = $image->schedule->description;
        $data['access_token'] = $this->page_access_token;
        $data['image_url'] = url(public_path() . '/uploads/social-media/' . $image->filename);

        $containerId = null;

        try {
            //send the media objecct to facebook. Required for us because in next step we use this object ID to post on facebook
            $response = $this->facebook->post($this->instagram_id . '/media', $data)->getDecodedBody();
            if (is_array($response)) {
                $containerId = $response['id'];
            }
        } catch (\Exception $exception) {
            $containerId = false;
        }

        return $containerId;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
