<?php

namespace App\Services\Products;

use App\Http\Controllers\GoogleTranslateController;
use App\Product_translation;

class GraphqlService
{
    //register multiple translations by one call
    public static function sendTranslationByGrapql($shopifyProductId, $productId)
    {
        $validLocales = self::getValidLocales();

        $endpoint = "https://o-labels.myshopify.com/admin/api/2020-07/graphql.json";//this is provided by graphcms

        $privateAppPassword = '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';//this is password for Landing-Page-Store private app

        //shopify productid 5695132893345

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
            "translations" => self::generateTranslations($validLocales, $productId)
        ];

        /*$vars = [
            "resourceId" => "gid://shopify/Product/$shopifyProductId",
            "translations" => [
                [
                    "locale" => "en",
                    "key" => "title",
                    "value" => "ALEXANDER MCQUEEN ABITI rururururzz",
                    "translatableContentDigest" => "e51e98217348adacb6016558bbad6686337067e844d450a3abfb29e1b551154f"
                ],
                [
                    "locale" => "en",
                    "key" => "body_html",
                    "value" => "Abito in misto viscosa-seta nero caratterizzato da girocollo, design smanicato, stampa grafica a contrasto, chiusura posteriore con cerniera, vestibilità aderente e tasglio corto.",
                    "translatableContentDigest" => "8c5ac3aff22f87cec6e506f4873ec16c3ebddaaef882ae334ebef8921fc8c3a3"
                ],
                [
                    "locale" => "ar",
                    "key" => "title",
                    "value" => "ALEXANDER MCQUEEN ABITI eseseszzz",
                    "translatableContentDigest" => "e51e98217348adacb6016558bbad6686337067e844d450a3abfb29e1b551154f"
                ],
            ]
        ];*/

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

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);

        return true;
    }

    private static function retrieveDataByGrapql($retrieveKey, $data = null)
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
        }

        return self::getDataByCurl($query);
    }

    private static function getDataByCurl($query)
    {
        $languages = GoogleTranslateController::LANGUAGES;
        $endpoint = "https://o-labels.myshopify.com/admin/api/2020-07/graphql.json";//this is provided by graphcms

        $privateAppPassword = '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';//this is password for Landing-Page-Store private app

        //Retrieve a single translatable resource by its ID
        /*$qry = '
                {
                  translatableResource(resourceId: "gid://shopify/Product/'."$shopifyProductId".'") {
                    resourceId
                    translatableContent {
                      key
                      value
                      digest
                      locale
                    }
                    translations(locale: "ru") {
                      key
                      value
                      locale
                    }
                  }
                }
            ';*/

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
    
    private static function getValidLocales ()
    {
        $languages   = GoogleTranslateController::LANGUAGES;
        $shopLocalesData = self::retrieveDataByGrapql('getLocales');
        //todo check locales exist and return error
        $shopLocales = array_column($shopLocalesData['data']['shopLocales'], 'locale');

        $shopLocalesModified = collect($shopLocales)->map(function ($locale) {
            return explode('-', $locale)[0];
        })->toArray();

        return array_intersect($shopLocalesModified, $languages);
    }

    private static function generateTranslations ($validLocales, $productId) //continue from here
    {
        $translations = [];

        $productTranslations = Product_translation::where('product_id', $productId)
            ->whereIn('locale', $validLocales)
            ->groupBy('locale')->get()->keyBy('locale')->toArray();
//        dd($productTranslations);

        foreach ($validLocales as $locale) {

        }
        $note = [
            "locale" => "ru",
            "key" => "title",
            "value" => "ALEXANDER MCQUEEN ABITI rururururzz",
            "translatableContentDigest" => "e51e98217348adacb6016558bbad6686337067e844d450a3abfb29e1b551154f"
        ];

        return $translations;
    }
}
