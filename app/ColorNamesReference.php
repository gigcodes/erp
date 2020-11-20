<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorNamesReference extends Model
{
    protected $fillable = ['color_code'];
    
    // Get product color from text
    public static function getProductColorFromObject($productObject)
    {
        // Get distinct color names used on ERP
        $mainColorNames = ColorNamesReference::distinct('color_name')->get(['color_name','erp_name']);
        
        // Check if color exists
        if (isset($productObject->properties['color']) || isset($productObject->properties->color)) {
            $colerRef = isset($productObject->properties->color) ? isset($productObject->properties->color) : $productObject->properties['color'];
            foreach ($mainColorNames as $colorName) {
                if (stristr($colerRef, $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
            // in this case color refenrece we don't found so we need to add that one
            ColorNamesReference::create([
                'color_code' => '',
                'color_name' => $colerRef
            ]);
            
        }

        // Check if color can be found in url
        if (isset($productObject->url)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->url), $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in title
        if (isset($productObject->title)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->title), $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in description
        if (isset($productObject->description)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($productObject->description), $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Return an empty string by default
        return '';
    }

    public static function getColorRequest($color = "" , $url = "" , $title = "", $description = "")
    {
        // Get distinct color names used on ERP
        $mainColorNames = ColorNamesReference::distinct('color_name')->get(['color_name','erp_name']);
        
        // Check if color exists
        if ($color) {
            foreach ($mainColorNames as $colorName) {
                if (stristr($color, $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
            // in this case color refenrece we don't found so we need to add that one
            ColorNamesReference::create([
                'color_code' => '',
                'color_name' => $color
            ]);
            
        }

        // Check if color can be found in url
        if (isset($url)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($url), $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in title
        if (isset($title)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($title), $colorName->color_name)) {
                    return $colorName->erp_name;
                }
            }
        }

        // Check if color can be found in description
        if (isset($description)) {
            foreach ($mainColorNames as $colorName) {
                if (stristr(self::_replaceKnownProblems($description), $colorName->color_name)) {
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
