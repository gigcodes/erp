<?php

namespace App\Http\Controllers\Pinterest;

class PinterestService
{

//    private $BASE_API = 'https://api.pinterest.com/v5/';
    private $BASE_API = 'https://api-sandbox.pinterest.com/v5/';
    private $BASE_AUTH_API_URL = 'https://www.pinterest.com/oauth/';
    private $clientId = '';
    private $clientSecret = '';
    private $accountId = '';
    private $accessToken = '';
    private $scopes = [
        'ads:read', 'ads:write',
        'boards:read', 'boards:write',
        'catalogs:read', 'catalogs:write',
        'pins:read', 'pins:write',
        'user_accounts:read',
//        'boards:read_secret', 'boards:write_secret', 'pins:read_secret', 'pins:write_secret',
    ];

    public function __construct($clientId = null, $clientSecret = null, $accountId = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accountId = $accountId;
    }

    /**
     * Get authorization URL
     * @return string
     */
    public function getAuthURL(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => str_replace("http://", "https://", route('pinterest.accounts.connect.login')),
            'response_type' => 'code',
            'scope' => implode(',', $this->scopes),
            'state' => base64_encode($this->accountId)
        ];
        $url = $this->BASE_AUTH_API_URL . '?';
        foreach ($params as $key => $param) {
            $url .= $key . '=' . $param . '&';
        }
        return $url;
    }

    /**
     * Validate and get access token from given code
     * @param $params
     * @return array
     */
    public function validateAccessTokenAndRefreshToken($params): array
    {
        $postFields = 'grant_type=authorization_code&code=' . $params['code'] . '&redirect_uri=' . str_replace("http://", "https://", route('pinterest.accounts.connect.login'));
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->BASE_API . 'oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->accountId . ', ';
            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);
            _p([$postFields, [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            ], $this->BASE_API . 'oauth/token', $response]);
            die;
            return ['status' => true, 'message' => 'Data found', 'data' => $response];
        }
    }

    /**
     * Common function to fetch data from API using CURL.
     */
    public function callApi($method, $url, array $params = []): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->BASE_API . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accessToken,
            ],
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->accountId . ', ';
            return ['status' => false, 'message' => $message . 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                if (array_key_exists('errors', $response)) {
                    $message = 'Account :- ' . $this->accountId . ', ';
                    return ['status' => false, 'message' => $message . 'cURL Error #:' . serialize($response)];
                }
                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                if ($method == 'DELETE') {
                    return ['status' => true, 'message' => 'Data found', 'data' => $response];
                } else {
                    $message = 'Account :- ' . $this->accountId . ', ';
                    return ['status' => false, 'message' => $message . 'cURL Error #:' . $response];
                }
            }
        }
    }
}
