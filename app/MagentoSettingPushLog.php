<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSettingPushLog extends Model
{
	protected $table = "magento_setting_push_logs";
    protected $fillable = [
        'store_website_id',
        'command'
	];
}
