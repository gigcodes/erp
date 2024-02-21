<?php

namespace App\Services\Facebook;

use JanuSoftware\Facebook\Exception\ResponseException;
use JanuSoftware\Facebook\Exception\SDKException;

class FbPayloads extends FB
{
    /**
     * Send POST request to Instagram Graph API.
     *
     *
     * @throws SDKException
     */
    public function postPayload(array $params, string $endpoint, string $token): array
    {
        try {
            $response = $this->fb->post(
                $endpoint,
                $params,
                $token
            );
        } catch (ResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (SDKException $e) {
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
     * @throws SDKException
     */
    public function getPayload(string $endpoint, string $token, bool $graphEdge = null): array
    {
        try {
            $response = $this->fb->get(
                $endpoint,
                $token
            );
        } catch (ResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (SDKException $e) {
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
     * @throws SDKException
     */
    public function deletePayload(array $params, string $endpoint, string $token): array
    {
        try {
            $response = $this->fb->delete(
                $endpoint,
                $params,
                $token
            );
        } catch (ResponseException $e) {
            echo 'Graph returned an error: ' . json_encode($e->getResponseData());
            exit;
        } catch (SDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        // Return result
        return $response->getGraphNode()->asArray();
    }
}
