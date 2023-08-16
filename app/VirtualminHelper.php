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
            $url = $this->_options['endpoint'] . "program=list-domains&json=1";

            $response = Http::withOptions(['verify' => false])
                ->withBasicAuth($this->_options['user'], $this->_options['pass'])
                ->get($url);

            $result = json_decode($response->getBody()->getContents(), true);

            // From the provided output:
            // The first item in the array appears to contain the column names, which seems to represent the header of a table.
            // The second item represents a row of dashes, possibly indicating the separation between header and data.
            // The remaining items are domains with associated data.

            if ($result['status'] === 'success') {
                $domainNames = [];
                for ($i = 2; $i < count($result['data']); $i++) {
                    $name = trim($result['data'][$i]['name']); // Extract the name
                    if (!empty($name) && strpos($name, '----') === false) {
                        preg_match('/^([\w.-]+)/', $name, $matches);
                        if (isset($matches[1])) {
                            $domainNames[] = $matches[1];
                        }
                    }
                }

                // Save domain names to the local database
                foreach ($domainNames as $domainName) {
                    VirtualminDomain::updateOrCreate(['name' => $domainName]);
                }
            }
            return ['code' => 200, 'data' => [], 'message' => "Domains fetched successfully"];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'data' => [], 'message' => $e->getMessage()];
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
