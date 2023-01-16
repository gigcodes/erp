<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckLanguageMessageHistory extends Model
{
    protected $table = 'uicheck_language_message_histories';

    protected $fillable = ['id', 'user_id', 'uicheck_id',  'ui_languages_id', 'languages_id', 'message', 'status', 'estimated_time', 'created_at'];
}
