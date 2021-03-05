<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'queue', 'payload', 'attempts',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public $timestamps = false;

    protected $hidden = [
    ];

    const JOBS_LIST = [
        "product"           => "Product Queue",
        "magento"           => "Magento Queue",
        "magentoone"        => "Magento product push Queue 1",
        "magentotwo"        => "Magento product push Queue 2",
        "magentothree"      => "Magento product push Queue 3",
        "supplier_products" => "Supplier product push",
        "customer_message"  => "Customer message queue",
        "watson_push"       => "Watson push queue",
    ];
}
