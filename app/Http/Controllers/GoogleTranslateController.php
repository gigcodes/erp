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
            $product_translation->composition = $product->composition;
            $product_translation->color = $product->color;
            $product_translation->size = $product->size;
            $product_translation->country_of_manufacture = $product->country_of_manufacture;
            $product_translation->dimension = $product->dimension;
            $product_translation->save();
        }
        foreach($languages as $language) {
            $isLocaleAvailable = Product_translation::where('locale',$language)->where('product_id',$product->id)->first();
            if(!$isLocaleAvailable) {
                $product_translation = new Product_translation();
                $titleFromTable = Product_translation::select('title')->where('locale',$language)->where('title',$product->name)->first();
                $descriptionFromTable = Product_translation::select('description')->where('locale',$language)->where('description',$product->short_description)->first();
                $compositionfromtable = Product_translation::select('composition')->where('locale',$language)->where('composition',$product->composition)->first();
                $colorfromtable = Product_translation::select('color')->where('locale',$language)->where('color',$product->color)->first(); 
                $sizefromtable = Product_translation::select('size')->where('locale',$language)->where('size',$product->size)->first();
                $country_of_manufacturefromtable = Product_translation::select('country_of_manufacture')->where('locale',$language)->where('country_of_manufacture',$product->country_of_manufacture)->first();
                $dimensionfromtable = Product_translation::select('description')->where('locale',$language)->where('dimension',$product->dimension)->first();
                $googleTranslate = new GoogleTranslate();
                $productNames = splitTextIntoSentences($product->name);
                $productShortDescription =  splitTextIntoSentences($product->short_description);
                $title = $titleFromTable ? $titleFromTable->title : self::translateProducts($googleTranslate, $language, $productNames);
                $description = $descriptionFromTable ? $descriptionFromTable->description : self::translateProducts($googleTranslate, $language, $productShortDescription);
                $composition = $compositionfromtable ? $compositionfromtable->composition : self::translateProducts($googleTranslate, $language, $product->composition);
                $color = $colorfromtable ? $colorfromtable->color : self::translateProducts($googleTranslate, $language, $product->color);
                $size = $sizefromtable ? $sizefromtable->size : self::translateProducts($googleTranslate, $language, $product->size);
                $country_of_manufacture = $country_of_manufacturefromtable ? $titleFromTable->country : self::translateProducts($googleTranslate, $language, $product->country_of_manufacture);
                $dimension = $dimensionfromtable ? $dimensionfromtable->dimension : self::translateProducts($googleTranslate, $language, $product->dimension);
                if($title && $description) {
                    $product_translation->title = $title;
                    $product_translation->description = $description;
                    $product_translation->product_id = $product->id;
                    $product_translation->locale = $language;
                    $product_translation->composition = $composition;
                    $product_translation->color = $color;
                    $product_translation->size = $size;
                    $product_translation->country_of_manufacture = $country_of_manufacture;
                    $product_translation->dimension = $dimension;
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
