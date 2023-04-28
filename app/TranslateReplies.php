<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class TranslateReplies extends Model
{
    /**
     * Fillables for the database
     *
     *
     * @var array
     */
    protected $table = 'translate_replies';

    protected $fillable = [
        'replies_id',
        'translate_from',
        'translate_to',
        'translate_text',
        'status',
        'updated_by_user_id',
        'approved_by_user_id',
        'created_by',
        'updated_by',
    ];
}
