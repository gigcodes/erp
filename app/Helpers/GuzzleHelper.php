<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GuzzleHelper
{
    public static function post(string $url, array $body, array $headers)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->post(

                $url,
                [
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::BODY => json_encode($body),
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return $parsedResponse;
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }

    public static function get(string $url, array $headers)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->get(

                $url,
                [
                    RequestOptions::HEADERS => $headers,
                ]
            );

            $parsedResponse = json_decode($response->getBody()->getContents());

            return $parsedResponse;
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }

    public static function patch(string $url, array $body, array $headers)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->patch(

                $url,
                [
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::BODY => json_encode($body),
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return $parsedResponse;
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }
}
