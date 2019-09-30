<?php
// IF YOU UPDATE THIS FILE, UPDATE IT IN THE SCRAPERSOLOLUXURY REPOSITORY AS WELL

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class LogScraper extends Model
{
    protected $table = 'log_scraper';
    protected $fillable = ['ip_address', 'website', 'url', 'sku', 'brand', 'title', 'description', 'properties', 'images', 'size_system', 'currency', 'price', 'discounted_price',];

    public static function LogScrapeValidationUsingRequest($request, $isExcel = 0)
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
        $errorLog .= self::validateBrand(!empty($request->brand) ? $request->brand : '');

        // Validate title
        $errorLog .= self::validateTitle($request->title);

        // Validate description
        $warningLog .= self::validateDescription($request->description);

        // Validate size_system
        $errorLog .= self::validateSizeSystem(!empty($request->size_system) ? $request->size_system : '');

        // Validate properties
        // TODO

        // Validate image warnings
        $warningLog .= self::validateImageWarnings($request->images);

        // Validate image errors
        $errorLog .= self::validateImageErrors($request->images);

        // Validate currency
        $errorLog .= self::validateCurrency($request->currency);

        // Validate price
        $errorLog .= self::validatePrice($request->price);

        // Validate discounted price
        $errorLog .= self::validateDiscountedPrice($request->discounted_price);

        // Find existing record
        $logScraper = LogScraper::where('website', $request->website)->where('sku', $request->sku)->first();

        // Create new record if not found
        if ($logScraper == null) {
            $logScraper = new LogScraper();
        }

        // For excels we only need the SKU
        if ($isExcel == 1 && isset($request->sku)) {
            // Replace errors with warnings
            $errorLog = str_replace('[error]', '[warning]', $errorLog);

            // Update warningLog
            $warningLog = $errorLog . $warningLog;

            // Empty error log
            $errorLog = '';
        }

        // Update values
        $logScraper->ip_address = self::getRealIp();
        $logScraper->website = $request->website ?? null;
        $logScraper->url = $request->url ?? null;
        $logScraper->sku = $request->sku ?? null;
        $logScraper->brand = $request->brand ?? null;
        $logScraper->category = isset($request->properties->category) ? serialize($request->properties->category) : null;
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
        $logScraper->raw_data = $isExcel == 0 ? serialize($request->all()) : null;
        $logScraper->save();

        // Update modified date
        $logScraper->touch();
        $logScraper->save();

        // Return true or false
        return $errorLog;
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

    public static function validateImageWarnings($images)
    {
        // Check if we have a value
        if (empty($images)) {
            return "[warning] Product without images\n";
        }

        // Return an empty string
        return "";
    }

    public static function validateImageErrors($images)
    {
        // Check image URLS
        foreach ($images as $image) {
            if (!filter_var($image, FILTER_VALIDATE_URL)) {
                return "[error] One or more images has an invalid URL\n";
            }
        }

        // Check if we have an array
        if ($images != '' && !is_array($images)) {
            return "[error] Images must be an array\n";
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

        // Check for comma's
        if ( stristr($price,',')) {
            return "[error] Comma in the price\n";
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
        } elseif (!empty($_SERVER[ 'REMOTE_ADDR' ])) {
            $ip = $_SERVER[ 'REMOTE_ADDR' ];
        } else {
            $ip = "none";
        }

        // Return IP
        return $ip;
    }
}
