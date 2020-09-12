<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplateCategory extends Model
{

    protected $table  = 'mailinglist_template_categories';

    protected $fillable = [
        'title', 'user_id',
    ];

}
