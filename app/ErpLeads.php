<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class ErpLeads extends Model
{
    //
    //use SoftDeletes;
    use Mediable;
    protected $fillable = [
        'lead_status_id',
        'customer_id',
        'product_id',
        'brand_id',
        'category_id',
        'color',
        'size',
        'min_price',
        'max_price',
        'brand_segment',
        'gender',
        'created_at',
        'updated_at',
    ];

    public function status_changes()
    {
        return $this->hasMany('App\StatusChange', 'model_id')->where('model_type', 'App\ErpLeads')->latest();
    }

    public function customer()
    {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }
}
