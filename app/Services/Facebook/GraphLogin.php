<?php

namespace App\Services\Facebook;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class GraphLogin
{
    /*
     * Facebook Object
     */
    private $fb;

    /*
     * Facebook helper
     */
    private $fbHelper;

    /**
     * Instantiates a new Facebook class object, Facebook Helper
     */
    public function __construct()
    {
        $this->fb = new Facebook(config('facebook.config'));

        $this->fbHelper = $this->fb->getRedirectLoginHelper();
    }

    /**
     * Get Login Url for your Application
     */
    public function getLoginUrl(array $permissions): string
    {
        return $this->fbHelper->getLoginUrl($_ENV['INSTAGRAM_CALLBACK_URL'], $permissions);
    }

    /**
     * returns an AccessToken.
     *
     *
     * @return null|false
     *
     * @throws FacebookSDKException
     */
    public function getAccessToken()
    {
        try {
            $accessToken = $this->fbHelper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            error_log('Graph returned an error: ' . $e->getMessage());

            return false;
        } catch (FacebookSDKException $e) {
            error_log('Facebook SDK returned an error: ' . $e->getMessage());

            return false;
        }

        if (! isset($accessToken)) {
            if ($this->fbHelper->getError()) {
                $error = 'Error: ' . $this->fbHelper->getError() . "\n";
                $error .= 'Error Code: ' . $this->fbHelper->getErrorCode() . "\n";
                $error .= 'Error Reason: ' . $this->fbHelper->getErrorReason() . "\n";
                $error .= 'Error Description: ' . $this->fbHelper->getErrorDescription() . "\n";

                error_log($error);
            } else {
                error_log('Bad request');
            }

            return false;
        }

        $oAuth2Client = $this->fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId($_ENV['FACEBOOK_APP_ID']);
        $tokenMetadata->validateExpiration();

        return $accessToken->getValue();
    }

    /**
     * returns User Info for Connected Instagram IDs
     *
     * @return array|false
     *
     * @throws FacebookSDKException
     */
    public function getUserInfo()
    {
        return ['access_token' => $this->getAccessToken()];
    }
}
