<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    protected $fillable = ['name', 'mail_class', 'mail_tpl', 'image_count', 'text_count', 'example_image', 'subject', 'static_template'];

    public function file()
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

}
