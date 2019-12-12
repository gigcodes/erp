<?php

namespace App\Helpers;

use App\Category;
use Illuminate\Database\Eloquent\Model;
use App\AttributeReplacement;
use App\Brand;
use App\GoogleServer;

class ProductHelper extends Model
{
    private static $_attributeReplacements = [];
    private static $_menShoesCategoryIds = [];
    private static $_womenShoesCategoryIds = [];

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

    public static function getRedactedText($text, $context = null)
    {
        // Get all replacements
        if (count(self::$_attributeReplacements) == 0) {
            self::$_attributeReplacements = AttributeReplacement::orderByRaw('CHAR_LENGTH(first_term)', 'DESC')->get();
        }

        // Loop over all replacements
        if (self::$_attributeReplacements !== null) {
            foreach (self::$_attributeReplacements as $replacement) {
                if ($context == null || $context == $replacement->field_identifier) {
                    $text = str_ireplace($replacement->first_term, $replacement->replacement_term, $text);
                }
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


    public static function getMeasurements($product)
    {
        // Create array with measurements
        $arrMeasurement = [];

        // Add measurements
        if ($product->lmeasurement > 0) {
            $arrMeasurement[] = $product->lmeasurement;
        }

        if ($product->hmeasurement > 0) {
            $arrMeasurement[] = $product->hmeasurement;
        }

        if ($product->dmeasurement > 0) {
            $arrMeasurement[] = $product->dmeasurement;
        }

        // Check for all dimensions
        if (count($arrMeasurement) == 3) {
            return 'L-' . $arrMeasurement[ 0 ] . 'cm,H-' . $arrMeasurement[ 1 ] . 'cm,D-' . $arrMeasurement[ 2 ] . 'cm';
        } elseif (count($arrMeasurement) == 2) {
            return $arrMeasurement[ 0 ] . 'cm x ' . $arrMeasurement[ 1 ] . 'cm';
        } elseif (count($arrMeasurement) == 1) {
            return 'Height: ' . $arrMeasurement[ 0 ] . 'cm';
        }

        // Still here?
        return;
    }

    public static function getBrandSegment($name, $select, $attr = array())
    {
        $brandSegment = ["A" => "A", "B" => "B", "C" => "C"];
        return \Form::select($name, $brandSegment, $select, $attr);
    }

    public static function getWebsiteSize($sizeSystem, $size, $categoryId = 0)
    {
        // For Italian sizes, return the original
        if (strtoupper($sizeSystem) == 'IT') {
            return $size;
        }

        // Get all category IDs for men's shoes (parent ID 5)
        if (count(self::$_menShoesCategoryIds) == 0) {
            self::$_menShoesCategoryIds = Category::where('parent_id', 5)->pluck('id')->toArray();
        }

        // Get all category IDs for men's shoes (parent ID 41)
        if (count(self::$_menShoesCategoryIds) == 0) {
            self::$_womenShoesCategoryIds = Category::where('parent_id', 41)->pluck('id')->toArray();
        }

        // US Shoes Men
        if (strtoupper($sizeSystem) == 'US' && in_array($categoryId, self::$_menShoesCategoryIds)) {
            switch ((int)$size) {
                // Shoes
                case 6:
                    return 38;
                case 7:
                    return 39;
                case 7.5:
                    return 40;
                case 8:
                    return 41;
                case 8.5:
                    return 42;
                case 9:
                    return 43;
                case 10.5:
                    return 44;
                case 11.5:
                    return 45;
                case 12:
                    return 46;
                case 13:
                    return 47;
                case 14:
                    return 48;
            }
        }

        // US Shoes Women
        if (strtoupper($sizeSystem) == 'US' && in_array($categoryId, self::$_womenShoesCategoryIds)) {
            switch ((int)$size) {
                // Shoes
                case 5:
                    return 35;
                case 6:
                    return 36;
                case 6.5:
                    return 37;
                case 7.5:
                    return 38;
                case 8.5:
                    return 39;
                case 9:
                    return 40;
                case 9.5:
                    return 41;
                case 10:
                    return 42;
                case 10.5:
                    return 43;
            }
        }

        // US Clothing
        if (strtoupper($sizeSystem) == 'US') {
            switch ((int)$size) {
                // Clothing
                case 2:
                    return 38;
                case 4:
                    return 40;
                case 6:
                    return 42;
                case 8:
                    return 44;
                case 10:
                    return 46;
                case 12:
                    return 48;
                case 14:
                    return 50;
                case 16:
                    return 52;
                case 18:
                    return 54;
                case 20:
                    return 56;
                case 22:
                    return 58;
                case 24:
                    return 60;
            }
        }

        // AU NZ Shoes Women
        if (in_array(strtoupper($sizeSystem), ['AU', 'NZ']) && in_array($categoryId, self::$_womenShoesCategoryIds)) {
            switch ((int)$size) {
                // Shoes
                case 3.5:
                    return 35;
                case 4.5:
                    return 36;
                case 5:
                    return 37;
                case 6:
                    return 38;
                case 7:
                    return 39;
                case 7.5:
                    return 40;
                case 8:
                    return 41;
                case 8.5:
                    return 42;
                case 9:
                    return 43;
            }
        }

        // UK Shoes Men and Women
        if (in_array(strtoupper($sizeSystem), ['UK', 'AU', 'NZ']) && (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds))) {
            switch ((int)$size) {
                // Shoes
                case 2.5:
                    return 35;
                case 3.5:
                    return 36;
                case 4:
                    return 37;
                case 5:
                    return 38;
                case 6:
                    return 39;
                case 6.5:
                    return 40;
                case 7:
                    return 41;
                case 7.5:
                    return 42;
                case 8:
                    return 43;
                case 9.5:
                    return 44;
                case 10.5:
                    return 45;
                case 11:
                    return 46;
                case 12:
                    return 47;
                case 13:
                    return 48;
            }
        }

        // UK / AU / NZ
        if (in_array(strtoupper($sizeSystem), ['UK', 'AU', 'NZ'])) {
            switch ((int)$size) {
                // Clothing
                case 6:
                    return 38;
                case 8:
                    return 40;
                case 10:
                    return 42;
                case 12:
                    return 44;
                case 14:
                    return 46;
                case 16:
                    return 48;
                case 18:
                    return 50;
                case 20:
                    return 52;
                case 22:
                    return 54;
                case 24:
                    return 56;
                case 26:
                    return 58;
                case 28:
                    return 60;
            }
        }

        // French Shoes Men and Women
        if (strtoupper($sizeSystem) == 'FR' && (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds))) {
            // Same size, just return
            return $size;
        }

        // French
        if (strtoupper($sizeSystem) == 'FR') {
            switch ((int)$size) {
                // Clothing
                case 34:
                    return 38;
                case 36:
                    return 40;
                case 38:
                    return 42;
                case 40:
                    return 44;
                case 42:
                    return 46;
                case 44:
                    return 48;
                case 46:
                    return 50;
                case 48:
                    return 52;
                case 50:
                    return 54;
                case 52:
                    return 56;
                case 54:
                    return 58;
                case 56:
                    return 60;
            }
        }

        // German Shoes Men and Women
        if (strtoupper($sizeSystem) == 'DE' && (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds))) {
            // Same size, just return
            return $size;
        }

        // German Clothing
        if (strtoupper($sizeSystem) == 'DE') {
            switch ((int)$size) {
                // Clothing
                case 32:
                    return 38;
                case 34:
                    return 40;
                case 36:
                    return 42;
                case 38:
                    return 44;
                case 40:
                    return 46;
                case 42:
                    return 48;
                case 44:
                    return 50;
                case 46:
                    return 52;
                case 48:
                    return 54;
                case 50:
                    return 56;
                case 52:
                    return 58;
                case 54:
                    return 60;
            }
        }

        // Japanese Shoes Men and Women
        if (strtoupper($sizeSystem) == 'JP' && (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds))) {
            switch ((int)$size) {
                // Shoes
                case 21:
                    return 35;
                case 22:
                    return 36;
                case 22.5:
                    return 37;
                case 23.5:
                    return 38;
                case 24.5:
                    return 39;
                case 25:
                    return 40;
                case 25.5:
                    return 41;
                case 26:
                    return 42;
                case 27:
                    return 43;
                case 28:
                    return 44;
                case 29:
                    return 45;
                case 30:
                    return 46;
                case 31:
                    return 47;
                case 32:
                    return 48;
            }
        }

        // Japanese Clothing
        if (strtoupper($sizeSystem) == 'JP') {
            switch ((int)$size) {
                // Clothing
                case 7:
                    return 38;
                case 9:
                    return 40;
                case 11:
                    return 42;
                case 13:
                    return 44;
                case 15:
                    return 46;
                case 17:
                    return 48;
                case 19:
                    return 50;
                case 21:
                    return 52;
                case 23:
                    return 54;
                case 25:
                    return 56;
                case 27:
                    return 58;
                case 29:
                    return 60;
            }
        }

        // Russian Shoes Men and Women
        if (strtoupper($sizeSystem) == 'DE' && (in_array($categoryId, self::$_menShoesCategoryIds) || in_array($categoryId, self::$_womenShoesCategoryIds))) {
            // Same size, just return
            return $size;
        }

        // Russian Clothing
        if (strtoupper($sizeSystem) == 'RU') {
            switch ((int)$size) {
                // Clothing
                case 40:
                    return 38;
                case 42:
                    return 40;
                case 44:
                    return 42;
                case 46:
                    return 44;
                case 48:
                    return 46;
                case 50:
                    return 48;
                case 52:
                    return 50;
                case 54:
                    return 52;
                case 56:
                    return 54;
                case 24:
                    return 56;
                case 26:
                    return 58;
                case 28:
                    return 60;
            }
        }

        // Still here? Return original size
        return $size;
    }

    public static function checkReadinessForLive($product)
    {
        // Check for mandatory fields
        if (empty($product->name)) {
            return false;
        }

        if (empty($product->short_description)) {
            return false;
        }

        // Check for price range
        if ((int)$product->price < 62.5 || (int)$product->price > 5000) {
            return false;
        }

        // Return
        return true;
    }

    /**
     * Get google server list
     *
     */

    public static function googleServerList()
    {
        return GoogleServer::pluck('name', 'key')->toArray();
        /*
        [
            "003745236201931391893:igsnhgfj79x" => "Group A",
            "003745236201931391893:gstsjpibsrr" => "Group B",
            "003745236201931391893:fnc4ssmvo8m" => "Group C"
        ];
        */
    }

    public static function getScraperIcon($name)
    {

        if (strpos($name, 'excel') !== false) {
            echo '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
        } else {
            echo '<i class="fa fa-globe" aria-hidden="true"></i>';
        }
    }
}
