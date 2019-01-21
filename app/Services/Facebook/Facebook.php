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
        $this->page_access_token=env('PAGE_ACCESS_TOKEN', 'EAAD7Te0j0B8BAO2yF97qtbFJq2pPzKZBOocsJVU3MZA95wKZBd0VkQtiUAP534GYkXaLXI0xJRNjP3Jrv43GTY84cVofQCqipkEEUNnVrU2ZBuzmR6AdkNcngPF318iIR123ZBw2XT2sWZBgCXrFolAokqFZBcL9eQZBsVs3aZBpyOf8FMuJs4FvLG8J9HJNZBJ9IZD');
        $this->page_id= '507935072915757';
        $this->ad_acc_id= 'act_128125721296439';
        $this->instagram_id = '17841406743743390';
    }

    public function postMedia($images): void
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

        try {
            $response = $this->facebook->post('/me/feed',$postMedia)->getDecodedBody();
            $data =  $response['id'];
            ImageSchedule::whereIn('image_id', $imageIds)->update([
                'posted' => 1
            ]);

        }
        catch (\Exception $exception) {
            $data = false;
        }

        $this->imageIds = $imageIds;
        $this->feedId = $data;

    }

    private function postMediaObject(Image $image) {
        $data['caption']= $image->schedule->description;
        $data['published'] = 'false';
        $data['access_token']=$this->page_access_token;
        $file = new File(public_path().'/uploads/social-media/'.$image->filename);
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
}
