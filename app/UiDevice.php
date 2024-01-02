<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDevice extends Model
{
    protected $table = 'ui_devices';

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'device_no', 'languages_id', 'message', 'status', 'is_approved', 'estimated_time', 'expected_start_time', 'expected_completion_time', 'created_at'];

    public function uichecks()
    {
        return $this->belongsTo(Uicheck::class, 'uicheck_id', 'id');
    }

    public function lastUpdatedHistory()
    {
        return $this->hasOne(UiDeviceHistory::class, 'ui_devices_id', 'id')->orderBy('updated_at', 'desc');
    }

    public function lastUpdatedStatusHistory()
    {
        return $this->hasOne(UiResponsivestatusHistory::class, 'ui_device_id', 'id')->orderBy('id', 'desc');
    }

    public function uiDeviceHistories()
    {
        return $this->hasMany(UiDeviceHistory::class, 'ui_devices_id');
    }

    public function stausColor()
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }
}
