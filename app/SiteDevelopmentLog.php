<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteDevelopmentLog extends Model
{
    protected $table = 'site_development_logs';

    protected $fillable = ['log'];
}
