<?php

namespace App;

use App\Models\UicheckHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UiDeviceBuilderIoData extends Model
{
    protected $table = 'ui_device_builder_io_datas';

    protected $fillable = [
        'id',
        'uicheck_id',
        'ui_device_id',
        'title',
        'html',
        'builder_created_date',
        'builder_last_updated',
        'builder_created_by',
        'builder_last_updated_by'
    ];

}
