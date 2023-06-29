<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceHistory extends Model
{
    protected $table = 'ui_device_histories';

    protected $fillable = ['id', 'user_id', 'ui_devices_id', 'uicheck_id',  'device_no', 'message', 'status', 'estimated_time', 'expected_completion_time', 'is_estimated_time_approved', 'created_at'];

    public function stausColor()
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }

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
        return $this->belongsTo(UiDevice::class, 'ui_devices_id');
    }
}
