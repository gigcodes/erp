<?php

namespace App\Http\Controllers;

use App\GoogleTranslate;
use App\Language;
use App\Product_translation;
use App\Translations;
use Illuminate\Http\Request;

class GoogleTranslateController extends Controller
{
    public static function translateProductDetails($product)
    {
        $isDefaultAvailable = Product_translation::where('locale','en')->where('product_id',$product->id)->first();
        $languages = Language::pluck('locale')->where("status",1)->toArray();
        if(!$isDefaultAvailable) {
            $product_translation = new Product_translation();
            $product_translation->title = $product->name;
            $product_translation->description = $product->short_description;
            $product_translation->product_id = $product->id;
            $product_translation->locale = 'en';
            $product_translation->save();
        }
        foreach($languages as $language) {
            $isLocaleAvailable = Product_translation::where('locale',$language)->where('product_id',$product->id)->first();
            if(!$isLocaleAvailable) {
                $product_translation = new Product_translation();
                $titleFromTable = Product_translation::select('title')->where('locale',$language)->where('title',$product->name)->first();
                $descriptionFromTable = Product_translation::select('description')->where('locale',$language)->where('description',$product->short_description)->first();
                $googleTranslate = new GoogleTranslate();
                $productNames = splitTextIntoSentences($product->name);
                $productShortDescription =  splitTextIntoSentences($product->short_description);
                $title = $titleFromTable ? $titleFromTable->title : self::translateProducts($googleTranslate, $language, $productNames);
                $description = $descriptionFromTable ? $descriptionFromTable->description : self::translateProducts($googleTranslate, $language, $productShortDescription);
                if($title && $description) {
                    $product_translation->title = $title;
                    $product_translation->description = $description;
                    $product_translation->product_id = $product->id;
                    $product_translation->locale = $language;
                    $product_translation->save();
                }
            }
        }
    }

    public static function translateProducts(GoogleTranslate $googleTranslate,$language,$names = []){
        $response = [];
        if(count($names) > 0){
            foreach($names as $name){
                // Check translation SEPARATE LINE exists or not
                $checkTranslationTable = Translations::select('text')->where('to',$language)->where('text_original',$name)->first();

                // If translation exists then USE it else do GOOGLE call
                if($checkTranslationTable) {
                    $response[] = $checkTranslationTable->text;
                } else {
                    try {
                        $translationString = $googleTranslate->translate($language,$name);

                        // Devtask-2893 : Added model to save individual line translation
                        Translations::addTranslation($name, $translationString, 'en', $language);
                        $response[] = $translationString;
                        
                    } catch (\Exception $e) {
                        \Log::error($e);
                    }
                }
            }
            return implode($response);
        }
        else{
            return '';
        }

    }
}
