<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowActionMessage extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'sender_name',
        'sender_email_address',
        'subject',
        'html_content',
        'reply_to_email',
        'sender_email_as_reply_to',
        'deleted',
    ];
}
