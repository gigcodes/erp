<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;

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

    public function syncDomains()
    {
        try {
            $url = $this->_options['endpoint'] . "?program=list-domains&json=1&multiline=";

            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->_options['user'], $this->_options['pass'])
                ->get($url);

            if ($response->successful()) {
                $responseData = $response->json();
                $domainsData = $responseData['data'];
                foreach ($domainsData as $domainInfo) {
                    $domainName = $domainInfo['name'];
                    if (isset($domainInfo['values']['disabled_at']) && !empty($domainInfo['values']['disabled_at'][0])) {
                        // Domain is disabled
                        $disabledTimestamp = $domainInfo['values']['disabled_at'][0];
                        // Process the disabled domain as needed
                        VirtualminDomain::updateOrCreate(
                            ['name' => $domainName],
                            ['is_enabled' => false]
                        );
                    } else {
                        // Domain is enabled
                        VirtualminDomain::updateOrCreate(
                            ['name' => $domainName],
                            ['is_enabled' => true]
                        );
                    }
                }
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to sync domain: ' . $e->getMessage());
        }
    }

    public function enableDomain($domainName)
    {
        try {
            $url = $this->_options['endpoint'] . "?program=enable-domain&domain={$domainName}&json=1&multiline=";

            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->_options['user'], $this->_options['pass'])
                ->get($url);

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to enable domain: ' . $e->getMessage());
        }
    }

    public function disableDomain($domainName)
    {
        try {
            $url = $this->_options['endpoint'] . "?program=disable-domain&domain={$domainName}&json=1&multiline=";

            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->_options['user'], $this->_options['pass'])
                ->get($url);

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to disable domain: ' . $e->getMessage());
        }
    }

    public function deleteDomain($domainName)
    {
        try {
            $url = $this->_options['endpoint'] . "?program=delete-domain&domain={$domainName}&json=1&multiline=";

            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->_options['user'], $this->_options['pass'])
                ->get($url);

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete domain: ' . $e->getMessage());
        }
    }

    public function createMail($domain, $user, $password)
    {
        try {
            $url = $this->_options['endpoint'] . "program=create-user&domain=$domain&user=$user&pass=$password&json=1";
            $httpClient = new Client();
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Basic ' . base64_encode($this->_options['user'] . ':' . $this->_options['pass']),
                    ],
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return ['code' => $response->getStatusCode(), 'data' => ['status' => $parsedResponse->status], 'message' => $response->getReasonPhrase()];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function changeMailPassword($domain, $user, $password)
    {
        try {
            $url = $this->_options['endpoint'] . "program=modify-user&domain=$domain&user=$user&pass=$password&json=1";
            $httpClient = new Client();
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Basic ' . base64_encode($this->_options['user'] . ':' . $this->_options['pass']),
                    ],
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return ['code' => $response->getStatusCode(), 'data' => ['status' => $parsedResponse->status], 'message' => $response->getReasonPhrase()];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'data' => [], 'message' => $e->getMessage()];
        }
    }
}
