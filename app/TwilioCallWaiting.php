<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioCallWaiting extends Model
{
    protected $table = 'twilio_call_waitings';

    protected $fillable = ['call_sid', 'account_sid', 'from', 'to', 'store_website_id', 'status'];

    public function storeWebsite()
    {
        return $this->belongsTo(\App\StoreWebsite::class);
    }
}
