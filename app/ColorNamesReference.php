<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorNamesReference extends Model
{
    // Get product color from text
    public static function getProductColorFromObject($productObject)
    {
        // Get distinct color names used on ERP
        $mainColorNames = ColorNamesReference::distinct('erp_name')->get(['erp_name']);

        // Check if color exists
        if (isset($productObject->properties->color)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr($productObject->properties->color, $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in url
        if (isset($productObject->url)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->url), $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in title
        if (isset($productObject->title)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->title), $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in description
        if (isset($productObject->description)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->description), $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Return an empty string by default
        return '';
    }

    private static function _replaceKnownProblems($text)
    {
        // Replace known problems
        $text = str_ireplace('off-white', '', $text);
        $text = str_ireplace('off+white', '', $text);
        $text = str_ireplace('off%20white', '', $text);
        $text = str_ireplace('off white', '', $text);
        $text = str_ireplace('offwhite', '', $text);

        // Return text
        return $text;
    }
}
