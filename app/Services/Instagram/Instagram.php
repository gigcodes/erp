<?php

namespace App\Services\Instagram;

use App\Image;
use Facebook\Facebook;

class Instagram {
    private $facebook;
    private $url = 'https://graph.facebook.com/';
    private $user_access_token;
    private $page_access_token;
    private $page_id;
    private $ad_acc_id;

    private $imageIds = [];

    /**
     * Instagram constructor.
     * @param Facebook $facebook
     */
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
        $this->user_access_token=env('USER_ACCESS_TOKEN', 'EAAD7Te0j0B8BAJKziYXYZCNZB0i6B9JMBvYULH5kIeH5qm6N9E3DZBoQyZCZC0bxZB4c4Rl5gifAqVa788DRaCWXQ2fNPtKFVnEoKvb5Nm1ufMG5cZCTTzKZAM8qUyaDtT0mmyC0zjhv5S9IJt70tQBpDMRHk9XNYoPTtmBedrvevtPIRPEUKns8feYJMkqHS6EZD');
        $this->page_access_token=env('PAGE_ACCESS_TOKEN', 'EAAD7Te0j0B8BAO2yF97qtbFJq2pPzKZBOocsJVU3MZA95wKZBd0VkQtiUAP534GYkXaLXI0xJRNjP3Jrv43GTY84cVofQCqipkEEUNnVrU2ZBuzmR6AdkNcngPF318iIR123ZBw2XT2sWZBgCXrFolAokqFZBcL9eQZBsVs3aZBpyOf8FMuJs4FvLG8J9HJNZBJ9IZD');
        $this->page_id= '507935072915757';
        $this->ad_acc_id= 'act_128125721296439';
        $this->instagram_id = '17841406743743390';
    }


    public function getMedia($url = null) {
        if ($url === null) {
            $params = 'fields'
                . '='
                . 'id,media_type,media_url,owner{id,username},timestamp,like_count,comments_count,caption';
            $url = $this->instagram_id . '/media?' . $params;
        }

        try {
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


        $media = array_map(function($post) {
            return [
                'id' => $post['id'],
                'comments' => [
                    'summary' => [
                        'total_count' => $post['comments_count']
                    ],
                    'url' => null
                ],
                'full_picture' => $post['media_url'] ?? null,
                'permalink_url' => null,
                'name' => $post['name'] ?? 'N/A',
                'message' => $post['caption'] ?? null,
                'created_time' => $post['timestamp'],
                'from' => $post['owner'],
                'likes' => [
                    'summary' => [
                        'total_count' => $post['like_count']
                    ]
                ]
            ];
        }, $media['data']);


        return [$media, $paging];
    }

    public function getComments($post_id) {
        $params = '?fields=username,text,timestamp,id,replies{id,username,text}';
        try {
            $comments = $this->facebook->get($post_id.'/comments'.$params, $this->page_access_token)->getDecodedBody();
            $comments = $comments['data'];
        } catch (\Exception $exception) {
            $comments = [];
        }

        $comments = array_map(function($item) {
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
     * @param $postId
     * @param $message
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @return array
     */
    public function postComment($postId, $message): array
    {
        $comment = $this->facebook
            ->post($postId . '/comments',
                [
                    'message' => $message,
                    'fields' => 'id,text,username,timestamp'
                ],
                $this->user_access_token
            )->getDecodedBody();

        $comment['status'] = 'success';

        return $comment;

    }

    public function postReply($commentId, $message) {
        $comment = $this->facebook
            ->post($commentId . '/replies',
                [
                    'message' => $message,
                    'fields' => 'id,text,username,timestamp'
                ],
                $this->user_access_token
            )->getDecodedBody();

        $comment['status'] = 'success';

        return $comment;
    }

    public function postMedia($images, $message) {
        if (!is_array($images)) {
            $images = [$images];
        }

        $return = [];
        $files = [];

        foreach ($images as $image) {
            $file = public_path().'/uploads/social-media/'.$image->filename;
            if (!file_exists($file)) {
                $file = public_path().'/uploads/'.$image->filename;
            }

            $files[] = $file;
        }

        $instagram = new \InstagramAPI\Instagram();
        $instagram->login('sololuxury.official', 'Insta123!');
        if (count($images) > 1) {
            $instagram->timeline->uploadAlbum($files, ['caption' => $message]);
        } else {
            $instagram->timeline->uploadPhoto($files[0], ['caption' => $message]);
        }
        $this->imageIds = $return;

    }

    private function postMediaObject(Image $image)
    {
        $data['caption']= $image->schedule->description;
        $data['access_token']=$this->page_access_token;
        $data['image_url'] = url(public_path().'/uploads/social-media/'.$image->filename);

        $containerId = null;

        try {
            $response = $this->facebook->post($this->instagram_id.'/media', $data)->getDecodedBody();
            if (is_array($response)) {
                $containerId = $response['id'];
            }
        } catch (\Exception $exception) {
            $containerId = false;
        }

        return $containerId;

    }

    /**
     * @return array
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
