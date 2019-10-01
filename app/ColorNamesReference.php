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
                if (stristr($productObject->url, $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in title
        if (isset($productObject->title)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr($productObject->title, $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in description
        if (isset($productObject->description)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr($productObject->description, $colorName->erp_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Return false by default
        return false;
    }
}
