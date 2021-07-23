<?php

namespace App\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class LogListMagento extends Model
{

    protected $fillable = [
        'product_id',
        'queue',
        'queue_id',
        'size_chart_url',
        'extra_attributes',
        'message',
        'created_at',
        'updated_at',
        'magento_status',
        'store_website_id',
        'sync_status',
        'languages',
        'user_id',
    ];

    public static function log($productId, $message, $severity = 'info', $storeWebsiteId = null, $syncStatus = null, $languages = null)
    {
        // Write to log file
        Log::channel('listMagento')->$severity($message);

        // Write to database
        $logListMagento                   = new LogListMagento();
        $logListMagento->product_id       = $productId;
        $logListMagento->message          = $message;
        $logListMagento->store_website_id = $storeWebsiteId;
        $logListMagento->sync_status      = $syncStatus;
        $logListMagento->languages        = $languages;
        $logListMagento->save();

        // Return
        return $logListMagento;
    }

    public static function updateMagentoStatus($id, $status)
    {
        return self::where('id', $id)->update([
            'magento_status' => $status,
        ]);
    }

    public function product()
    {
        return $this->hasOne(\App\Product::class, 'id', 'product_id');
    }

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }
}
