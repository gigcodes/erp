<?php

namespace App\Http\Controllers;

use App\Language;
use App\Translations;
use App\GoogleTranslate;
use App\Product_translation;
use App\ProductPushErrorLog;
use App\Helpers\ProductHelper;
use App\Loggers\LogListMagento;

class GoogleTranslateController extends Controller
{
    public static function translateProductDetails($product, $logid = null)
    {
        $logModel = LogListMagento::find($logid);

        try {
            $measurement        = ProductHelper::getMeasurements($product);
            $isDefaultAvailable = Product_translation::where('locale', 'en')->where('product_id', $product->id)->first();
            $languages          = Language::where('status', 1)->pluck('locale')->toArray();
            if (! $isDefaultAvailable) {
                $product_translation                         = new Product_translation();
                $product_translation->title                  = html_entity_decode($product->name);
                $product_translation->description            = $product->short_description;
                $product_translation->product_id             = $product->id;
                $product_translation->locale                 = 'en';
                $product_translation->composition            = $product->composition;
                $product_translation->color                  = $product->color;
                $product_translation->size                   = $product->size;
                $product_translation->country_of_manufacture = $product->made_in;
                $product_translation->dimension              = $measurement;
                $product_translation->save();
            }
            if (count($languages) > 0) {
                foreach ($languages as $language) {
                    $isLocaleAvailable = Product_translation::where('locale', $language)->where('product_id', $product->id)->first();
                    if (! $isLocaleAvailable || $isLocaleAvailable->dimension == '' || $isLocaleAvailable->country_of_manufacture == '' || $isLocaleAvailable->color == '' || $isLocaleAvailable->composition == '' || $isLocaleAvailable->description == '' || $product_translation->title == '') { //if product translation not available
                        $title = $description = $composition = $color = $size = $country_of_manufacture = $dimension = '';
                        try {
                            $product_translation = Product_translation::where('locale', $language)->where('product_id', $product->id)->first();
                            if (empty($product_translation)) { //check if id existing or not
                                $product_translation = new Product_translation; //if id not existing create new object for insert else update
                            }
                            $googleTranslate         = new GoogleTranslate();
                            $productNames            = splitTextIntoSentences($product->name);
                            $productShortDescription = splitTextIntoSentences($product->short_description);
                            //check in table is field is empty and then translate
                            $requestData = $responseData = [];
                            if ($product_translation->title == '') {
                                $title                 = self::translateProducts($googleTranslate, $language, $productNames);
                                $requestData['title']  = $productNames;
                                $responseData['title'] = $title;
                                if ($title == '' and ! empty($logid)) {
                                    ProductPushErrorLog::log('', $product->id, 'Product Title transact to ' . $language . ' is blank', 'error', $logModel->store_website_id, $requestData, $responseData, $logModel->id);
                                }
                                $product_translation->title = $title;
                            }
                            if ($product_translation->description == '') {
                                $description                      = self::translateProducts($googleTranslate, $language, $productShortDescription);
                                $product_translation->description = $description;
                                $requestData['description']       = $productShortDescription;
                                $responseData['description']      = $description;
                                if ($description == '' and ! empty($logid)) {
                                    ProductPushErrorLog::log('', $product->id, 'Product description transact to ' . $language . ' is blank', 'error', $logModel->store_website_id, $requestData, $responseData, $logModel->id);
                                }
                            }
                            if ($product_translation->composition == '') {
                                $composition                      = self::translateProducts($googleTranslate, $language, [$product->composition]);
                                $product_translation->composition = $composition;
                                if ($composition != '' and ! empty($logid)) {
                                    $requestData['composition']  = [$product->composition];
                                    $responseData['composition'] = $composition;
                                }
                            }
                            if ($product_translation->color == '') {
                                $color                      = self::translateProducts($googleTranslate, $language, [$product->color]);
                                $product_translation->color = $color;
                                if ($color != '' and ! empty($logid)) {
                                    $requestData['color']  = [$product->color];
                                    $responseData['color'] = $color;
                                }
                            }
                            if ($product_translation->country_of_manufacture == '') {
                                $country_of_manufacture                      = self::translateProducts($googleTranslate, $language, [$product->made_in]);
                                $product_translation->country_of_manufacture = $country_of_manufacture;
                                if ($country_of_manufacture != '' and ! empty($logid)) {
                                    $requestData['country_of_manufacture']  = [$product->made_in];
                                    $responseData['country_of_manufacture'] = $country_of_manufacture;
                                }
                            }
                            if ($product_translation->dimension == '') {
                                $dimension                      = self::translateProducts($googleTranslate, $language, [$measurement]);
                                $product_translation->dimension = $dimension;
                                if ($dimension != '' and ! empty($logid)) {
                                    $requestData['dimension']  = [$measurement];
                                    $responseData['dimension'] = $country_of_manufacture;
                                }
                            }
                            $product_translation->product_id = $product->id;
                            $product_translation->locale     = $language;
                            $product_translation->save();
                            if ($responseData and ! empty($logid)) {
                                ProductPushErrorLog::log('', $product->id, 'Product Translated to ' . $language, 'info', $logModel->store_website_id, $requestData, $responseData, $logModel->id);
                            }
                        } catch (\Exception $e) {
                            $msg = $language . ' => ' . $e->getMessage();
                            if (! empty($logid)) {
                                if ($logModel) {
                                    ProductPushErrorLog::log('', $product->id, $msg, 'error', $logModel->store_website_id, '', '', $logModel->id);
                                }
                            }
                        }
                    }
                }
            } else {
                $msg = 'Languages data not exists';
                if (! empty($logid)) {
                    $logModel = LogListMagento::find($logid);
                    if ($logModel) {
                        ProductPushErrorLog::log('', $product->id, $msg, 'error', $logModel->store_website_id, '', '', $logModel->id);
                    }
                }
            }
        } catch (\Exception $e) {
            $msg = 'Internal server error';
            if (! empty($logid)) {
                $logModel = LogListMagento::find($logid);
                if ($logModel) {
                    ProductPushErrorLog::log('', $product->id, $msg, 'php', $logModel->store_website_id, '', '', $logModel->id);
                }
            }
        }
    }

    public static function translateProducts(GoogleTranslate $googleTranslate, $language, $names = [], $glue = '')
    {
        $response = [];
        if (count($names) > 0) {
            foreach ($names as $name) {
                // Check translation SEPARATE LINE exists or not
                $checkTranslationTable = Translations::select('text')->where('to', $language)->where('text_original', $name)->first();

                // If translation exists then USE it else do GOOGLE call
                if ($checkTranslationTable) {
                    $response[] = $checkTranslationTable->text;
                } else {
                    try {
                        $translationString = $googleTranslate->translate($language, $name);

                        // Devtask-2893 : Added model to save individual line translation
                        Translations::addTranslation($name, $translationString, 'en', $language);
                        $response[] = $translationString;
                    } catch (\Exception $e) {
                        \Log::channel('errorlog')->error($e);
                    }
                }
            }

            if ($glue) {
                return implode($glue, $response);
            }

            return implode($response);
        } else {
            return '';
        }
    }

    //DEVTASK-3272
    public static function translateGeneralDetails($data)
    {
        $languages = Language::where('status', 1)->pluck('locale');
        foreach ($languages as $language) {
            $isLocaleAvailable = Translations::where('to', $language)->where('text_original', $data['text'])->first();
            if (! $isLocaleAvailable) { //if its not exist then it will go to google translator.
                $googleTranslate = new GoogleTranslate();
                $dataNames       = splitTextIntoSentences($data['text']);
                $dataGeneral     = self::translateProducts($googleTranslate, $language, $dataNames);
            }
        }
    }
}
