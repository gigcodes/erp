<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceHistory extends Model
{
    protected $table = 'ui_device_histories';

    protected $fillable = ['id', 'user_id', 'ui_devices_id', 'uicheck_id',  'device_no', 'message', 'status', 'created_at'];

}