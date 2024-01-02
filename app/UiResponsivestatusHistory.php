<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiResponsivestatusHistory extends Model
{
    protected $table = 'ui_responsivestatus_histories';

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'device_no', 'ui_device_id', 'status', 'old_status', 'created_at'];

    public function stausColor()
    {
        return $this->belongsTo(SiteDevelopmentStatus::class, 'status', 'id');
    }
}
