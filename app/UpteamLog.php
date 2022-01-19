<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpteamLog extends Model
{
    protected $table = "upteam_product_logs";
    protected $fillable = [
        'log_description'
	];
}
