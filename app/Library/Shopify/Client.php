<?php

namespace App\Library\Shopify;

use App\StoreWebsite;

class Client
{

    private $_apiVersion = '2020-04';

    private $_shopUrl;
    private $_key;
    private $_password;
    private $_sharedSecret;

    private $_curlInfo;
    private $_curlResponse;
    private $_curlHeader;

    private $_shopifyCollections;

    private $_random;

    // GraphQL variables
    private $_gAddProduct = [];

    public function __construct()
    {
        // Config ID
        $this->_shopUrl      = env('SHOPIFY_SHOP_URL');
        $this->_key          = env('SHOPIFY_SHOP_KEY');
        $this->_password     = env('SHOPIFY_SHOP_PASSWORD');
        $this->_sharedSecret = env('SHOPIFY_SHOP_SHARED_SECRET');

        // Set random
        $this->_random = uniqid();
    }

    public function addProduct($json = null, $collections = null, $store_id = null)
    {
        // Set URL
        $url = '/admin/products.json';

        // Post data
        return $this->_sendRequestToShopify($url, $json, "POST", $store_id);
    }

    public function updateProduct($id, $json = null, $collections = null)
    {
        $url = '/admin/api/' . $this->_apiVersion . '/products/' . $id . '.json';
        // Post data
        return $this->_sendRequestToShopify($url, $json, "PUT");
    }

    private function _sendRequestToShopify($url = '/', $postData = '', $requestType = '', $store_id )
    {
        // Check if cURL exists
        if (!function_exists('curl_init')) {
            return false;
        }

        // Set empty headers
        $headers = [];

        // Set queryString
        if ($requestType == 'GET' && is_array($postData) && count($postData) > 0) {
            // Set initial queryString
            $queryString = "?";

            // Loop over fields
            foreach ($postData as $key => $value) {
                $queryString .= $key . '=' . urlencode($value) . '&';
            }

            // Add queryString to url
            $url .= substr($queryString, 0, -1);
        }

        if(!empty($store_id)){
            $store_website = StoreWebsite::where(['id' => $store_id])->first();
            $url = 'https://'.$store_website->magento_url . $url;
        }else{
            // Add _shopURL if no key is set
            if (!empty($this->_password) && $this->_key == 'APP' && !stristr($url, $this->_shopUrl)) {
                $url = 'https://' . $this->_shopUrl . $url;
            }
        }



        // Set cURL options
        if (stristr($url, $this->_shopUrl)) {
            $ch = curl_init($url);
        } else {
            if(!is_null($store_id)){
                $ch = curl_init('https://' . $store_website->api_token . ':' . $store_website->magento_password . '@' . $store_website->magento_url . $url) ;
            }else{
                $ch = curl_init('https://' . $this->_key . ':' . $this->_password . '@' . $this->_shopUrl . $url);
            }
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        if (!empty($requestType)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        }

        // Set postData for non-GET requests
        if ($requestType != 'GET' && is_array($postData) && count($postData) > 0) {
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        // Set X-Header for apps
        if(!empty($store_id)){
            $headers[] = "X-Shopify-Access-Token: " . $store_website->magento_password;
        }else{
            if (!empty($this->_password) && $this->_key == 'APP') {
                $headers[] = "X-Shopify-Access-Token: " . $this->_password;
            }
        }

        // Add headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Get response
        $response          = $this->_curlResponse          = curl_exec($ch);
        $headerSize        = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->_curlHeader = substr($response, 0, $headerSize);
        $response          = $this->_curlResponse          = substr($response, $headerSize);

        // Get info
        $this->_curlInfo = curl_getinfo($ch);

        // Close cURL
        curl_close($ch);

        // Return data
        return json_decode($response);
    }
}
