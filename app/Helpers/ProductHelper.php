<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use App\AttributeReplacement;
use App\Brand;

class ProductHelper extends Model
{
    private static $_attributeReplacements = [];

    public static function getSku($sku)
    {
        // Do replaces in SKU
        $sku = str_replace(' ', '', $sku);
        $sku = str_replace('/', '', $sku);
        $sku = str_replace('-', '', $sku);
        $sku = str_replace('_', '', $sku);
        $sku = str_replace('+', '', $sku);
        $sku = str_replace('|', '', $sku);
        $sku = str_replace('\\', '', $sku);

        // Return SKU
        return strtoupper($sku);
    }

    public static function getOriginalSkuByBrand($sku, $brandId = 0)
    {
        // Get brand
        $brand = Brand::find($brandId);

        // Return sku if brand is unknown
        if ($brand == null) {
            return $sku;
        }

        // Gucci
        if ($brand == 'GUCCI') {
            return str_replace('/', '', $sku);
        }

        // Strip last # characters
        if (isset($brand->sku_strip_last) && (int)$brand->sku_strip_last > 0) {
            $sku = substr($sku, 0, $brand->sku_strip_last * -1);
        }

        // Return SKU
        return $sku;
    }

    public static function getSkuWithoutColor($sku)
    {
        // Replace all colors from SKU
        if (class_exists('\App\Colors')) {
            // Get all colors
            $colors = new \App\Colors;
            $colors = $colors->all();

            // Loop over colors
            foreach ($colors as $color) {
                if (stristr($sku, $color)) {
                    // Replace color
                    $sku = str_ireplace($color, '', $sku);
                }
            }

            // Replace multi
            $sku = str_ireplace('multicolor', '', $sku);
            $sku = str_ireplace('multi', '', $sku);

            // Replace Italian color names
            $sku = str_ireplace('azzuro', '', $sku); // Blue
            $sku = str_ireplace('bianco', '', $sku); // White
            $sku = str_ireplace('marrone', '', $sku); // Brown
            $sku = str_ireplace('nero', '', $sku); // Black
            $sku = str_ireplace('oro', '', $sku); // Gold
            $sku = str_ireplace('verde', '', $sku); // Green

            // Replace word color
            $sku = str_ireplace('color', '', $sku);
        }

        // Return sku
        return $sku;
    }

    public static function getRedactedText($text)
    {
        // Get all replacements
        if (count(self::$_attributeReplacements) == 0) {
            self::$_attributeReplacements = AttributeReplacement::all();
        }

        // Loop over all replacements
        if (self::$_attributeReplacements !== null) {
            foreach (self::$_attributeReplacements as $replacement) {
                $text = str_ireplace($replacement->first_term, $replacement->replacement_term, $text);
            }

            // Remove html special chars
            $text = htmlspecialchars_decode($text);
        }

        // Return redacted text
        return $text;
    }

    public static function getCurrency($currency)
    {
        // Check if the currency is a Euro-sumbol
        if ($currency = '€') {
            return 'EUR';
        }

        // Return currency
        return $currency;
    }

    public static function fixCommonMistakesInRequest($request)
    {
        // Category is not an array
        if (!is_array($request->get('category'))) {
            $request->merge([
                'category' => [],
            ]);
        }

        // Replace currency symbol with three character currency for EUR
        if ($request->get('currency') == '€') {
            $request->merge([
                'currency' => 'EUR',
            ]);
        }

        // Replace currency symbol with three character currency for GBP
        if ($request->get('currency') == '£') {
            $request->merge([
                'currency' => 'GBP',
            ]);
        }

        // Replace currency symbol with three character currency for USD
        if ($request->get('currency') == '$') {
            $request->merge([
                'currency' => 'USD',
            ]);
        }

        // Replace currency symbol with three character currency for USD
        if ($request->get('currency') == 'US$') {
            $request->merge([
                'currency' => 'USD',
            ]);
        }

        // Replace spaces in image URLS
        if (is_array($request->get('images')) && count($request->get('images')) > 0) {
            // Set empty array with images
            $arrImages = [];

            // Loop over arrImages
            foreach ($request->get('images') as $image) {
                // Replace space in image
                $image = str_replace(' ', '%20', $image);

                // Store image in array
                $arrImages[] = $image;
            }

            // Replace images with corrected URLs
            $request->merge([
                'images' => $arrImages,
            ]);
        }

        // Return request
        return $request;
    }

    public static function getMeasurements($product) {
        // Create array with measurements
        $arrMeasurement = [];

        // Add measurements
        if ( $product->lmeasurement > 0 ) {
            $arrMeasurement[] = $product->lmeasurement;
        }

        if ( $product->hmeasurement > 0 ) {
            $arrMeasurement[] = $product->hmeasurement;
        }

        if ( $product->dmeasurement > 0 ) {
            $arrMeasurement[] = $product->dmeasurement;
        }

        // Check for all dimensions
        if ( count($arrMeasurement) == 3 ) {
            return 'L-' . $arrMeasurement[0] . 'cm,H-' . $arrMeasurement[1] . 'cm,D-' . $arrMeasurement[2] . 'cm';
        } elseif ( count($arrMeasurement) == 2 ) {
            return $arrMeasurement[0] . 'cm x ' . $arrMeasurement[1] . 'cm';
        } elseif ( count($arrMeasurement) == 1 ) {
            return 'Height: ' . $arrMeasurement[0] . 'cm';
        }

        // Still here?
        return;
    }
}
