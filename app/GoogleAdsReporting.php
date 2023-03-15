<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAdsReporting extends Model
{
    protected $table = 'google_ads_reportings';

    protected $fillable = ['google_customer_id', 'name', 'impression', 'click', 'cost_micros'];
}
