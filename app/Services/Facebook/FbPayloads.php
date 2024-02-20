<?php

namespace App\Services\Facebook;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

class FbPayloads extends FB
{
    /**
     * Send POST request to Instagram Graph API.
     *
     *
     * @throws FacebookSDKException
     */
    public function postPayload(array $params, string $endpoint, string $token): array
    {
        try {
            $response = $this->fb->post(
                $endpoint,
                $params,
                $token
            );
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        // Return result
        return $response->getGraphNode()->asArray();
    }

    /**
     * Login and Authenticate to Instagram Graph API.
     *
     *
     * @throws FacebookSDKException
     */
    public function getPayload(string $endpoint, string $token, bool $graphEdge = null): array
    {
        try {
            $response = $this->fb->get(
                $endpoint,
                $token
            );
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if ($graphEdge) {
            return $response->getGraphEdge()->asArray();
        }

        return $response->getGraphNode()->asArray();
    }

    /**
     * Send DELETE request to Instagram Graph API.
     *
     *
     * @throws FacebookSDKException
     */
    public function deletePayload(array $params, string $endpoint, string $token): array
    {
        try {
            $response = $this->fb->delete(
                $endpoint,
                $params,
                $token
            );
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        // Return result
        return $response->getGraphNode()->asArray();
    }
}
