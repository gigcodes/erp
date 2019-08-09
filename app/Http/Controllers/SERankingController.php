<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SERankingController extends Controller
{
    private $apiKey; 

    public function __construct()
    {
        $this->apiKey = '66122f8ad1adb1c075c75aba3bd503a4a559fc7f';
    }

    public function getSites() {
        // $url = 'https://api4.seranking.com/sites';
        $url = 'https://api4.seranking.com/research/overview?domain=seranking.com';
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'header' => [
                    "Authorization: Token $this->apiKey",
                    "Content-Type: application/json; charset=utf-8"
                ],
                // 'content' => json_encode([
                // // 'url' => 'https://api4.seranking.com/sites',
                // 'title' => 'my test project'
                // ])
            ]
        ]);
        $httpStatus = null;
        $result = file_get_contents($url, 0, $context);
        if (isset($http_response_header)) {
            preg_match('`HTTP/[0-9\.]+\s+([0-9]+)`', $http_response_header[0], $matches);
            $httpStatus = $matches[1];
        }
        if (!$result) {
            echo "Request failed!";
        } else {
            $result = json_decode($result);
        }
        dd($result);
    }
}
