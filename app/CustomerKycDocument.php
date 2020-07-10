<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerKycDocument extends Model
{
    const TYPE = [
        "Unknown"
    ];

    protected $fillable = [
        'customer_id','type','url','path','created_at','updated_at'
    ];

    public function customer()
    {
        return $this->hasOne(App\Customer::class, "id", "customer_id");
    }
}
