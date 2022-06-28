<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoDevScripUpdateLog extends Model
{
    protected $table = "magento_dev_script_update_logs";
    protected $fillable = [
        'store_website_id', 
        'website', 
        'response', 
        'site_folder', 
        'website', 
        'error', 
    ];
}
