<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckDeviceAttachment extends Model
{
    protected $table = 'uicheck_device_attachments';

    protected $fillable = ['id', 'user_id', 'device_no', 'uicheck_id',  'languages_id', 'attachment', 'created_at'];
}
