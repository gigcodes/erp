<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    protected $fillable = ['name', 'image_count', 'text_count', 'example_image'];

    public function file()
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

}
