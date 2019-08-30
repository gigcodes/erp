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
}
