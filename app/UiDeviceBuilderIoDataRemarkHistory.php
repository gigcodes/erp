<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceBuilderIoDataRemarkHistory extends Model
{
    protected $table = 'ui_device_builder_io_data_remark_histories';

    protected $fillable = [
        'user_id',
        'ui_device_builder_io_data_id',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uiDeviceBuilderIoData()
    {
        return $this->belongsTo(UiDeviceBuilderIoData::class);
    }
}
