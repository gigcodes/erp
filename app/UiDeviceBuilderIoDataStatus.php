<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiDeviceBuilderIoDataStatus extends Model
{
    protected $table = 'ui_device_builder_io_data_statuses';

    protected $fillable = [
        'name',
        'color',
    ];
}
