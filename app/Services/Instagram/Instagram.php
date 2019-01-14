<?php

namespace App\Services\Instagram;

class Instagram {

    private $clientId;
    private $clientSecret;
    private $url = 'https://graph.facebook.com/';

    public function __construct()
    {
        $this->clientId = '88787edabc0349a993e596b71415866f';
        $this->clientSecret = '415d22dab3c54502b843bb8218eab4e6';
    }


    public function getMedia() {

    }

    public function postMedia() {

    }

    public function getComments() {

    }

    public function replyToComment() {

    }

    private function sendRequestToAPI() {

    }
}
