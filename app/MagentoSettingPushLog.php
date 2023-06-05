<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoSettingPushLog extends Model
{
    protected $table = 'magento_setting_push_logs';

    protected $fillable = [
        'store_website_id',
        'setting_id',
        'command',
        'command_output',
        'status',
        'job_id',
        'command_server',
    ];
}
