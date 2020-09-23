<?php

namespace App\Services\Products;

use App\Http\Controllers\GoogleTranslateController;
use App\Product_translation;
use Illuminate\Support\Facades\Log;

class GraphqlService
{
    //register multiple translations by one call
    public static function sendTranslationByGrapql($shopifyProductId, $productId)
    {
        $result = true;
        $localeExist = self::getValidLocales()['success'];

        if ($localeExist) {
            $validLocales = self::getValidLocales()['validLocales'];
            $localeDiffs  = self::getValidLocales()['localeDiffs'];

            $endpoint = "https://o-labels.myshopify.com/admin/api/2020-07/graphql.json";//this is provided by graphcms

            $privateAppPassword = env('SHOPIFY_SHOP_PASSWORD');//this is password for Landing-Page-Store private app
            $translations = self::generateTranslations($validLocales, $localeDiffs, $productId, $shopifyProductId);

            if (!count($translations)) {
                $message = 'missing_product_translations: No translations found in product_translations 
                table for product with id: ' . $productId;
                self::addLogs($message);
            }

            $qry = '
               mutation translationsRegister($resourceId: ID!, $translations: [TranslationInput!]!) {
                  translationsRegister(resourceId: $resourceId, translations: $translations) {
                    translations {
                      key
                      locale
                      outdated
                      value
                    }
                    userErrors {
                      code
                      field
                      message
                    }
                  }
               }
            ';

            $vars = [
                "resourceId"   => "gid://shopify/Product/$shopifyProductId",
                "translations" => $translations
            ];

            $data = [];
            $data['query'] = $qry;
            $data['variables'] = $vars;
            $data = json_encode($data);

            $headers = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Shopify-Access-Token: ' . $privateAppPassword;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $response = json_decode($response, true);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }

            $userErrors = isset($response['data']['translationsRegister']['userErrors']);
            if ($userErrors) {
                foreach ($response['data']['translationsRegister']['userErrors'] as $error) {
                    $message = $error['code'] . ': ' . $error['message'];
                    $message .= PHP_EOL .' Data: ' . print_r($error['field'], true);

                    self::addLogs($message);
                }

            };

            curl_close($ch);

        } else {
            //add logs
            $result = false;
            $message = 'no_locales: No locales enabled in shopify store. Please visit:
             https://o-labels.myshopify.com/admin/apps/content-translation and select "CONFIGURE LANGUAGES"';
            self::addLogs($message);
        }

        return $result;
    }

    private static function getDataByCurl($query)
    {
        $endpoint = "https://o-labels.myshopify.com/admin/api/2020-07/graphql.json";//this is provided by graphcms

        $privateAppPassword = env('SHOPIFY_SHOP_PASSWORD');;//this is password for Landing-Page-Store private app

        $headers = [];
        $headers[] = 'Content-Type: application/graphql';
        $headers[] = 'X-Shopify-Access-Token: ' . $privateAppPassword;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return $result;
    }

    //check translations exist
    public static function testGetDataByCurl($shopifyProductId)
    {
        $endpoint = "https://o-labels.myshopify.com/admin/api/2020-07/graphql.json";//this is provided by graphcms

        $privateAppPassword = env('SHOPIFY_SHOP_PASSWORD');;//this is password for Landing-Page-Store private app

        //Retrieve a single translatable resource by its ID
        $qry = '
                {
                  translatableResource(resourceId: "gid://shopify/Product/'."$shopifyProductId".'") {
                    resourceId
                    translatableContent {
                      key
                      value
                      digest
                      locale
                    }
                    translations(locale: "ar") {
                      key
                      value
                      locale
                    }
                  }
                }
            ';

        $headers = [];
        $headers[] = 'Content-Type: application/graphql';
        $headers[] = 'X-Shopify-Access-Token: ' . $privateAppPassword;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

//        dd($result);

        return $result;
    }

    private static function getValidLocales ()
    {
        $data = ['success' => false];
        $languages   = GoogleTranslateController::LANGUAGES;
        $shopLocalesData = self::retrieveDataByGrapql('getLocales');

        if (isset($shopLocalesData['data']['shopLocales'])) {
            $shopLocales = array_column($shopLocalesData['data']['shopLocales'], 'locale');
            $localeDiffs = [];

            foreach ($shopLocales as &$locale) {
                if (strpos($locale, '-')) {
                    $old = $locale;
                    $locale = explode('-', $locale)[0];
                    $localeDiffs[$locale] = $old;
                }
            }

            $data = [
                'validLocales' => array_intersect($shopLocales, $languages),
                'localeDiffs' => $localeDiffs,
                'success' => true
            ];
        }

        return $data;
    }

    private static function generateTranslations ($validLocales, $localeDiffs, $productId, $shopifyProductId)
    {
        $translations = [];

        $productTranslations = Product_translation::where('product_id', $productId)
            ->whereIn('locale', $validLocales)
            ->groupBy('locale')->get()->keyBy('locale')->toArray();

        if ($productTranslations) {
            $shopifyProduct = $shopLocalesData = self::retrieveDataByGrapql('getTitleDesc', ['shopifyProductId' => $shopifyProductId]);
            $hashTitle = hash('sha256', $shopifyProduct['data']['product']['title']);
            $hashDesc  = hash('sha256', $shopifyProduct['data']['product']['description']);

            foreach ($productTranslations as $data)
            {
                $titleData = [];
                $descData  = [];

                //one for title
                $titleData['locale'] = array_key_exists($data['locale'], $localeDiffs) ? $localeDiffs[$data['locale']] : $data['locale'];
                $titleData['key']    = 'title';
                $titleData['value']  = $data['title'];
                $titleData['translatableContentDigest'] = $hashTitle;

                //one for description
                $descData['locale'] = array_key_exists($data['locale'], $localeDiffs) ? $localeDiffs[$data['locale']] : $data['locale'];
                $descData['key']    = 'body_html';
                $descData['value']  = $data['description'];
                $descData['translatableContentDigest'] = $hashDesc;

                $translations[] = $titleData;
                $translations[] = $descData;
            }
        }

        return $translations;
    }

    private static function retrieveDataByGrapql($retrieveKey, $data = [])
    {
        $query = '';
        switch ($retrieveKey) {
            case 'getLocales': $query = '
                {
                  shopLocales {
                    locale
                    primary
                    published
                  }
                }
            ';
                break;
            case 'getTitleDesc': $query = '
            {
                  product(id: "gid://shopify/Product/'."{$data['shopifyProductId']}".'") {
                    title
                    description
                    onlineStoreUrl
                  }
                }
            ';
                break;
        }

        return self::getDataByCurl($query);
    }


    private static function addLogs ($message)
    {
        Log::info($message);
    }
}
