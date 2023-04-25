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

    /**
     * Create affiliate group on provider
     * @param $data
     * @return array
     */
    public function createAffiliateGroup($data): array
    {
        return $this->callApi('POST', 'affiliate-groups/', $data);
    }

    /**
     * Update affiliate group on provider
     * @param $id
     * @param $data
     * @return array
     */
    public function updateAffiliateGroup($id, $data): array
    {
        return $this->callApi('PATCH', 'affiliate-groups/' . $id . '/', $data);
    }

    public function getSyncData(): array
    {
        $programList = $this->callApi('GET', 'programs/');
        $affiliatesGroupList = $this->callApi('GET', 'affiliate-groups/');
        $affiliatesList = $this->callApi('GET', 'affiliates/');
        $commissionList = $this->callApi('GET', 'commissions/');
        $conversionsList = $this->callApi('GET', 'conversions/');
        $paymentsList = $this->callApi('GET', 'payments/');
        return [
            'programList' => $programList,
            'affiliatesGroupList' => $affiliatesGroupList,
            'affiliatesList' => $affiliatesList,
            'commissionList' => $commissionList,
            'conversionsList' => $conversionsList,
            'paymentsList' => $paymentsList,
        ];
    }

    /**
     * Get all the programmes from API
     * @return array
     */
    public function getProgrammes(): array
    {
        return $this->callApi('GET', 'programs/');
    }

    /**
     * Get all the commissions from API
     * @return array
     */
    public function getCommissions(): array
    {
        return $this->callApi('GET', 'commissions/');
    }

    /**
     * Update affiliate Commission on provider
     * @param $id
     * @param $data
     * @return array
     */
    public function updateAffiliateCommission($id, $data): array
    {
        return $this->callApi('PATCH', 'commissions/' . $id . '/', $data);
    }

    /**
     * Update affiliate Commission approve or disapprove on provider
     * @param $id
     * @param $data
     * @return array
     */
    public function updateAffiliateApproveDisapprove($id, $isApprove): array
    {
        return $this->callApi($isApprove ? 'PUT' : 'DELETE', 'commissions/' . $id . '/approved/');
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
//            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Api-Key:' . $this->API_KEY
            ]
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';
            return ['status' => false, 'message' => $message . "cURL Error #:" . $err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                return ['status' => true, 'message' => "Data found", 'data' => $response];
            } else {
                $message = 'Account :- ' . $this->PROVIDER_ACCOUNT->id . ', ';
                return ['status' => false, 'message' => $message . "cURL Error #:" . $response];
            }
        }
    }
}
