<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceBuilderIoDataDownloadHistory extends Model
{
    protected $table = 'ui_device_builder_io_data_download_histories';

    protected $fillable = [
        'user_id',
        'ui_device_builder_io_data_id',
        'downloaded_at',
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
