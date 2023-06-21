<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiResponseMessagesTranslation extends Model
{
    protected $table = 'api_response_messages_translations';

    protected $fillable = [
        'id', 'store_website_id', 'key', 'lang_code', 'lang_name', 'value'
    ];

    public function storeWebsite()
    {
        return $this->hasOne(\App\StoreWebsite::class, 'id', 'store_website_id');
    }

    public function websiteStoreView()
    {
        return $this->hasOne(\App\WebsiteStoreView::class, 'code', 'lang_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id', 'id');
    }
}
