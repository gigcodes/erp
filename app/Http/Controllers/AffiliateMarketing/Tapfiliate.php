<?php

namespace App\Http\Controllers\AffiliateMarketing;

/**
 * Tapfiliate controller to manage and update data for tapfiliate provider.
 */
class Tapfiliate
{
    private $BASE_API_URL = 'https://api.tapfiliate.com/1.6/';
    private $API_KEY = '';
    private $PROVIDER_ACCOUNT;

    public function __construct($providerAccount)
    {
        $this->API_KEY = $providerAccount->api_key;
        $this->PROVIDER_ACCOUNT = $providerAccount;
    }

    public function createAffiliateGroup($data)
    {
        $response = $this->callApi('POST', 'affiliate-groups/', $data);
        return $response;
    }

    public function updateAffiliateGroup($id, $data)
    {
        $response = $this->callApi('PATCH', 'affiliate-groups/' . $id . '/', $data);
        return $response;
    }

    /**
     * Common function to fetch data from API using CURL.
     * @param $method
     * @param $url
     * @param array $params
     * @return array
     */
    public function callApi($method, $url, array $params = []): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->BASE_API_URL . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Api-Key:' . $this->API_KEY
            ]
        ]);

        if ($method == 'POST' || $method == 'PATCH') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';
            return ['status' => false, 'message' => $message . "cURL Error #:" . $err];
        } else {
            return ['status' => true, 'message' => "Data found", 'data' => json_decode($response, true)];
        }
    }
}
