<?php

// IF YOU UPDATE THIS FILE, UPDATE IT IN THE SCRAPERSOLOLUXURY REPOSITORY AS WELL

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class LogScraper extends Model
{
    protected $table = 'log_scraper';
    protected $fillable = ['ip_address', 'website', 'url', 'sku', 'brand', 'title', 'description', 'properties', 'images', 'size_system', 'currency', 'price', 'discounted_price',];

    public static function LogScrapeValidationUsingRequest($request)
    {
        // Set empty log for errors and warnings
        $errorLog = "";
        $warningLog = "";

        // Validate the website
        $errorLog .= self::validateWebsite($request->website);

        // Validate URL
        $errorLog .= self::validateUrl($request->url);

        // Validate SKU
        $errorLog .= self::validateSku($request->sku);

        // Validate brand
        $errorLog .= self::validateBrand($request->brand);

        // Validate title
        $errorLog .= self::validateTitle($request->title);

        // Validate description
        $warningLog .= self::validateDescription($request->description);

        // Validate size_system
        $errorLog .= self::validateSizeSystem($request->size_system);

        // Validate properties
        // TODO

        // Validate images
        $errorLog .= self::validateImages($request->images);

        // Validate currency
        $errorLog .= self::validateCurrency($request->currency);

        // Validate price
        $errorLog .= self::validatePrice($request->price);

        // Validate discounted price
        $errorLog .= self::validateDiscountedPrice($request->discounted_price);

        // Create new record
        $logScraper = new LogScraper();
        $logScraper->ip_address = self::getRealIp();
        $logScraper->website = $request->website ?? null;
        $logScraper->url = $request->url ?? null;
        $logScraper->sku = $request->sku ?? null;
        $logScraper->brand = $request->brand ?? null;
        $logScraper->title = $request->title ?? null;
        $logScraper->description = $request->description ?? null;
        $logScraper->properties = isset($request->properties) ? serialize($request->properties) : null;
        $logScraper->images = isset($request->images) ? serialize($request->images) : null;
        $logScraper->size_system = $request->size_system ?? null;
        $logScraper->currency = $request->currency ?? null;
        $logScraper->price = $request->price ?? null;
        $logScraper->discounted_price = $request->discounted_price ?? null;
        $logScraper->is_sale = $request->is_sale ?? 0;
        $logScraper->validated = empty($errorLog) ? 1 : 0;
        $logScraper->validation_result = $errorLog . $warningLog;
        $logScraper->save();
    }

    public static function validateWebsite($website)
    {
        // Check if we have a value
        if (empty($website)) {
            return "[error] Website cannot be empty\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateUrl($url)
    {
        // Check if we have a value
        if (empty($url)) {
            return "[error] URL cannot be empty\n";
        }

        // Check if the URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return "[error] URL is not valid\n";
        }


        // Return an empty string
        return "";
    }

    public static function validateSku($sku)
    {
        // Check if we have a value
        if (empty($sku)) {
            return "[error] SKU cannot be empty\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateBrand($brand)
    {
        // Check if we have a value
        if (empty($brand)) {
            return "[error] Brand cannot be empty\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateTitle($title)
    {
        // Check if we have a value
        if (empty($title)) {
            return "[error] Title cannot be empty\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateDescription($description)
    {
        // Check if we have a value
        if (empty($description)) {
            return "[warning] Description is empty\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateSizeSystem($sizeSystem)
    {
        // Check if we have a value
        if (empty($sizeSystem)) {
            return "[error] Size system is missing\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateImages($images)
    {
        // Check if we have a value
        if (empty($images)) {
            return "[error] Images cannot be empty\n";
        }

        // Check if we have an array
        if (!is_array($images)) {
            return "[error] Images must be an array\n";
        }

        // Check image URLS
        foreach ($images as $image) {
            if (!filter_var($image, FILTER_VALIDATE_URL)) {
                return "[error] One or more images has an invalid URL\n";
            }
        }

        // Return an empty string
        return "";
    }

    public static function validateCurrency($currency)
    {
        // Check if we have a value
        if (empty($currency)) {
            return "[error] Currency cannot be empty\n";
        }

        // Check for three characters
        if (strlen($currency) != 3) {
            return "[error] Currency must be exactly three characters\n";
        }

        // Return an empty string
        return "";
    }

    public static function validatePrice($price)
    {
        // Check if we have a value
        if (empty($price)) {
            return "[error] Price cannot be empty\n";
        }

        // Check if price is a float value
        if ((float)$price == 0) {
            return "[error] Price must be of type float/double\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateDiscountedPrice($discountedPrice)
    {
        // Check if discounted price is a float value
        if (!empty($discountedPrice) && (float)$discountedPrice == 0) {
            return "[error] Discounted price must be of type float/double\n";
        }

        // Return an empty string
        return "";
    }

    private static function getRealIp()
    {
        // Check which IP to use
        if (!empty($_SERVER[ 'HTTP_CLIENT_IP' ])) {
            $ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
        } elseif (!empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])) {
            $ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        } else {
            $ip = $_SERVER[ 'REMOTE_ADDR' ];
        }

        // Return IP
        return $ip;
    }
}
