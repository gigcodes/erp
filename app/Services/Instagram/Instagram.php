<?php

namespace App\Services\Instagram;

use Facebook\Facebook;

class Instagram {
    private $facebook;
    private $url = 'https://graph.facebook.com/';

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
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
