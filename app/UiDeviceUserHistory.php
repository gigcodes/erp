<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceUserHistory extends Model
{
    protected $table = 'ui_device_user_histories';

    protected $fillable = ['user_id', 'uicheck_id', 'ui_device_id', 'old_user_id', 'new_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldUser()
    {
        return $this->belongsTo(User::class, 'old_user_id');
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id');
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
