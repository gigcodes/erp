<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailingTemplateFile extends Model
{
    protected $fillable = ['mailing_id', 'path'];
}
