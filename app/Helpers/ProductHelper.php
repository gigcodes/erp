<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use App\AttributeReplacement;

class ProductHelper extends Model
{
    private static $_attributeReplacements = [];

    public static function getSku($sku)
    {
        // Replace all colors from SKU
        if ( class_exists( '\App\Colors' ) ) {
            // Get all colors
            $colors = new \App\Colors;
            $colors = $colors->all();

            // Loop over colors
            foreach ( $colors as $color ) {
                if ( stristr( $sku, $color ) ) {
                    // Set firstcolor
                    str_ireplace($color, '', $sku);
                }
            }
        }

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
