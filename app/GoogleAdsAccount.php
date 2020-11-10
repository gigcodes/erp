<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsAccount extends Model
{
    protected $table='googleadsaccounts';
    protected $fillable=['account_name','store_websites','config_file_path','notes','status'];
}
