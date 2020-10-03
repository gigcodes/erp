<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailinglistTemplate extends Model
{
    protected $fillable = ['name', 'mail_class', 'mail_tpl', 'image_count', 'text_count', 'example_image', 'subject', 'static_template', 'category_id', 'store_website_id'];

    public function file()
    {
        return $this->hasMany(MailingTemplateFile::class, 'mailing_id', 'id');
    }

    public function category(){
        return $this->hasOne(MailinglistTemplateCategory::class, 'id', 'category_id');
    }

    public function storeWebsite(){
        return $this->hasOne(StoreWebsite::class, 'id', 'store_website_id');
    }

}
