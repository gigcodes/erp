<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LogListMagento extends Model
{
    public static function log($productId, $message, $severity = 'info')
    {
        // Write to log file
        Log::channel('listMagento')->$severity($message);

        // Write to database
        $logListMagento = new LogListMagento();
        $logListMagento->product_id = $productId;
        $logListMagento->message = $message;
        $logListMagento->save();

        // Return
        return;
    }
}
