<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatSetting extends Model
{
    protected $fillable = [
        'chat_name', 'vendor', 'instance_id', 'workspace_id', 'is_active',
    ];
}
