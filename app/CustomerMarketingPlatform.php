<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\Marketing\MarketingPlatform;

class CustomerMarketingPlatform extends Model
{
	protected $fillable = ['customer_id','marketing_platform_id','active','remark'];
    public function customer()
    {
    	return $this->belongsTo(Customer::class,'id','customer_id');
    }

    public function marketing()
    {
    	return $this->hasOne(MarketingPlatform::class,'id','marketing_platform_id');
    }
}
