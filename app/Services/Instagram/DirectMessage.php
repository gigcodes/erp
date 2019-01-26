<?php

namespace App\Services\Instagram;

use InstagramAPI\Instagram;
use InstagramAPI\Media\Photo\InstagramPhoto;

Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class DirectMessage {

    private $instagram;
    private $currentId;

    public function __construct(Instagram $instagram)
    {
        $this->instagram = $instagram;
        $username = env('IG_USERNAME', 'sololuxury');
        $password = env('IG_PASSWORD', 'U;5%wn~L48E+');
        $this->currentId = env('IG_CURRENT_USER_ID', '6827120791');
        $this->instagram->login($username, $password);
    }

    public function getInbox() {
        $inbox = $this->instagram->direct->getInbox();
        return $inbox;
    }

    public function getThread($threadId) {
        $thread = $this->instagram->direct->getThread($threadId);
        return $thread;
    }

    public function sendImage($receipt, $photo) {
        $photo = new InstagramPhoto($photo);
        $this->instagram->direct->sendPhoto($receipt, $photo->getFile());
    }

    public function sendMessage($receipt, $message) {
        $this->instagram->direct->sendText($receipt, $message);
    }

    public function getCurrentUserId() {
        return $this->currentId;
    }
}