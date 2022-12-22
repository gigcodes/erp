<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class OrderMagentoErrorLog extends Model
{
    protected $table = 'order_magento_error_logs';

    /**
     * @var string
     *
     * @SWG\Property(property="order_id",type="integer")
     * @SWG\Property(property="country_id",type="integer")
     * @SWG\Property(property="customer_id",type="integer")
     */
    protected $fillable = [
        'id',
        'order_id',
        'log_msg',
    ];
}
