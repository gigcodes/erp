<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreWebsiteTwilioNumber extends Model
{
    protected $table = 'store_website_twilio_numbers';

    protected $fillable = [
        'store_website_id', 'twilio_active_number_id'
    ];

    public function store_website()
    {
        return $this->hasOne("\App\StoreWebsite","id", "store_website_id");
    }

}
