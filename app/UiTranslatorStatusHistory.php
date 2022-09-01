<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UiTranslatorStatusHistory extends Model
{
    protected $table = 'ui_translator_status_histories';

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'ui_language_id', 'language_id', 'status', 'old_status', 'created_at'];
}
