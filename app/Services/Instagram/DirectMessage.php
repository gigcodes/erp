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
        $username = env('IG_USERNAME');
        $password = env('IG_PASSWORD');
        $this->currentId = env('IG_CURRENT_USER_ID');


        try {
            $r = $this->instagram->login($username, $password);
        } catch (Exception $Exception) {
            if ($Exception instanceof ChallengeRequiredException)
            {
                sleep(5);
                $customResponse = $this->instagram->request(substr($Exception->getResponse()->getChallenge()->getApiPath(), 1))->setNeedsAuth(false)->addPost('choice', 0)->getDecodedResponse();
                if (is_array($customResponse)) {
                    $this->instagram->login($username, $password);
                }
            }
        }
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