<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceLog extends Model
{
    protected $table = 'ui_device_logs';

    protected $fillable = ['user_id', 'uicheck_id', 'ui_device_id', 'start_time', 'end_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uicheck()
    {
        return $this->belongsTo(Uicheck::class);
    }

    public function uiDevice()
    {
        return $this->belongsTo(UiDevice::class);
    }
}
