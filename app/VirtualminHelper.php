<?php

namespace App;

use App\EmailAddress;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class VirtualminHelper 
{
    private $_options = null;

    public function __construct()
    {
        $this->_options = [
            'endpoint' => getenv('VIRTUALMIN_ENDPOINT'),
            'user' => getenv('VIRTUALMIN_USER'),
            'pass' => getenv('VIRTUALMIN_PASS'),
        ];
    }

    public function createMail($domain, $user, $password)
    {
        try {
            $url = $this->_options['endpoint']."program=create-user&domain=$domain&user=$user&pass=$password&json=1";
            $httpClient = new Client();
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Basic '.base64_encode($this->_options['user'].":".$this->_options['pass']),
                    ]
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            return ['code' => $response->getStatusCode(), 'data' => ['status'=> $parsedResponse->status], 'message'=> $response->getReasonPhrase()];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'data' => [],'message'=> $e->getMessage()];
        }
    }

    public function changeMailPassword($domain, $user, $password)
    {
        try {
            $url = $this->_options['endpoint']."program=modify-user&domain=$domain&user=$user&pass=$password&json=1";
            $httpClient = new Client();
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Basic '.base64_encode($this->_options['user'].":".$this->_options['pass']),
                    ]
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            return ['code' => $response->getStatusCode(), 'data' => ['status'=> $parsedResponse->status], 'message'=> $response->getReasonPhrase()];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'data' => [],'message'=> $e->getMessage()];
        }
    }

}