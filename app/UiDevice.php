<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDevice extends Model
{
    protected $table = 'ui_devices';

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'device_no', 'languages_id', 'message', 'status', 'estimated_time', 'created_at'];
}
