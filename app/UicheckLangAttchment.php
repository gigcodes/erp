<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UicheckLangAttchment extends Model
{
    protected $table = 'uicheck_lang_attchments';

    protected $fillable = ['id', 'user_id', 'uicheck_id', 'ui_languages_id',  'languages_id', 'attachment', 'created_at'];
}
