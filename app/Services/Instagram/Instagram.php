<?php

namespace App\Services\Instagram;

use Facebook\Facebook;

class Instagram {
    private $facebook;
    private $url = 'https://graph.facebook.com/';
    private $user_access_token;
    private $page_access_token;
    private $page_id;
    private $ad_acc_id;

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
                . 'id,media_type,media_url,owner{id,username},timestamp,comments{text,owner,timestamp,like_count},like_count,comments_count,caption';
            $url = $this->instagram_id . '/media?' . $params;
        }

        try {
            $media = $this->facebook->get($url, $this->page_access_token)->getDecodedBody();
        } catch (\Exception $exception) {
            return [];
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

        return $media;
    }

    public function postMedia() {

    }

    public function getComments($post_id) {
        $params = '?fields=from,text,timestamp,id';
        try {
            $comments = $this->facebook->get($post_id.'/comments'.$params, $this->page_access_token)->getDecodedBody();
            $comments = $comments['data'];
        } catch (\Exception $exception) {
            $comments = [];
        }

        return $comments;
    }

    public function replyToComment() {

    }

    private function sendRequestToAPI() {

    }
}
