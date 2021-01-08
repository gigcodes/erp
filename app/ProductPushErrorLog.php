<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPushErrorLog extends Model
{
    protected $fillable = [
        'url',
        'product_id',
        'message',
        'request_data',
        'response_data',
        'response_status',
        'store_website_id',
        'url',
    ];

    public static function log($url = null, $productId, $message, $status = null, $storeWebsiteId = null, $request_data = null, $response_data = null, $logId = null)
    {
        // Write to database
        $logListMagento                   = new ProductPushErrorLog();
        $logListMagento->url              = $url;
        $logListMagento->product_id       = $productId;
        $logListMagento->message          = $message;
        $logListMagento->store_website_id = $storeWebsiteId;
        $logListMagento->response_status  = $status;
        $logListMagento->request_data     = json_encode($request_data);
        $logListMagento->response_data    = json_encode($response_data);
        if ($logId) {
            $logListMagento->log_list_magento_id = $logId;
        }
        $logListMagento->save();
        return;
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    public function store_website()
    {
        return $this->belongsTo('App\StoreWebsite');
    }
}
