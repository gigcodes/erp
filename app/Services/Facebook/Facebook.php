<?php

namespace App\Services\Facebook;

use App\Image;
use App\ImageSchedule;
use Facebook\Facebook as Fb;
use Illuminate\Http\File;

class Facebook {
    private $facebook;
    private $url = 'https://graph.facebook.com/';
    private $user_access_token;
    private $page_access_token;
    private $page_id;
    private $ad_acc_id;
    private $imageIds = [];
    private $feedId;
    /**
     * Instagram constructor.
     * @param Facebook $facebook
     */
    public function __construct(Fb $facebook)
    {
        $this->facebook = $facebook;
        $this->user_access_token=env('USER_ACCESS_TOKEN', 'EAAD7Te0j0B8BAJKziYXYZCNZB0i6B9JMBvYULH5kIeH5qm6N9E3DZBoQyZCZC0bxZB4c4Rl5gifAqVa788DRaCWXQ2fNPtKFVnEoKvb5Nm1ufMG5cZCTTzKZAM8qUyaDtT0mmyC0zjhv5S9IJt70tQBpDMRHk9XNYoPTtmBedrvevtPIRPEUKns8feYJMkqHS6EZD');
        $this->page_access_token=env('PAGE_ACCESS_TOKEN', 'EAAD7Te0j0B8BALpu9yCul1yz2L6WPZAQtydzn4VPrihHcpGsgBZC9EvnrZCx0IhDEZCmeOZCxZAvJF48ZAxygl9ujSx1bdFMtSLoyucYDBz6yspCbRiNELnRlPMJEZBwgCKwSriEgQY6IQC9FNDBk790OOZCjbca9DLWXHjolkCHrFZAnhSNX5yNeqTR3ZCU49hl2vKJYaz26oaGgZDZD');
        $this->page_id= '507935072915757';
        $this->ad_acc_id= 'act_128125721296439';
        $this->instagram_id = '17841406743743390';
        // 291175945035620 ;  business id
    }

    public function getMentions($tag) {
        $tagText = $tag->name;
        $data = $this->facebook->get('ig_hashtag_search?user_id='.$this->instagram_id.'&q='.$tagText, '10606198004.0912694.8a4c5161260e41bb87fd646638d27093'  );

        dd($data);
    }

    public function postMedia($images, $message = ''): void
    {
        if (!is_array($images)) {
            $images = [$images];
        }

        $imageIds = [];
        $key = 0;

        $postMedia['access_token']=$this->page_access_token;

        foreach ($images as $image) {
            $mediaId = $this->postMediaObject($image);
            if ($mediaId !== false) {
                $imageIds[] = $image->id;
                $postMedia['attached_media['.$key.']'] = '{"media_fbid":"'.$mediaId.'"}';
                $key++;
            }

            $postMedia['published'] = 'true';

        }

        $data = null;
        $postMedia['message'] = $message;

        try {
            $response = $this->facebook->post('/me/feed',$postMedia)->getDecodedBody();
            $data =  $response['id'];
            ImageSchedule::whereIn('image_id', $imageIds)->update([
                'posted' => 1
            ]);

        }
        catch (\Exception $exception) {
            dd($exception);
            $data = false;
        }

        $this->imageIds = $imageIds;
        $this->feedId = $data;

    }

    private function postMediaObject(Image $image) {
        $data['caption']= $image->schedule->description;
        $data['published'] = 'false';
        $data['access_token']=$this->page_access_token;
        $file = public_path().'/uploads/social-media/'.$image->filename;
        if (!file_exists($file)) {
            $file = public_path().'/uploads/'.$image->filename;
        }
        $file = new File($file);
        $data['source'] = $this->facebook->fileToUpload($file);

        $mediaId = null;


        try {
            $response = $this->facebook->post('/me/photos', $data)->getDecodedBody();
            if (is_array($response)) {
                $mediaId = $response['id'];
            }
        } catch (\Exception $exception) {
            $mediaId = false;
        }

        return $mediaId;

    }

    /**
     * @return mixed
     */
    public function getImageIds()
    {
        return $this->imageIds;
    }

    /**
     * @return mixed
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    public function getConversations() {
        $conversation = $this->facebook->get($this->page_id . '/conversations?fields=name,participants,messages.limit(100){message,from,id,sticker}&limit=10000', 'EAAD7Te0j0B8BAFMI2ZB9vn5WjaZAbrYUIZBvchkyMlHMjrpY3Bw24hFrRCob0f3uB3CG8IVZBvVBn3tlIL12XJyucqOeZBWZBCLX3f34Yctx0vZAa3RtIds3j7IZCe2wqh8LEnCJlzmLrwyR7rdV2OFURWlvA9ZB8vwmZC8yuFVKBS13wloLD3ScMRpryefpfJDaYG7pXa3ZB946wZDZD');

        return $conversation->getDecodedBody();

    }
}
