<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiLanguage extends Model
{
    protected $table = 'ui_languages';

    protected $fillable = ['id', 'user_id', 'uicheck_id',  'languages_id', 'message', 'status', 'estimated_time', 'created_at'];
}
