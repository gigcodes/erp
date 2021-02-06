<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blogger extends Model
{
    protected $fillable = ['name','phone','default_phone','instagram_handle','city','country','followers','followings','avg_engagement','fake_followers','email','rating','whatsapp_number','other','agency','industry','brands'];

    protected $casts = [
        'brands' => 'array'
    ];

    public function chat_message()
    {
        return $this->hasMany(ChatMessage::class,'blogger_id');
    }

    public function payments()
    {
        return $this->hasMany(BloggerPayment::class);
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'cash_flow_able');
    }
}
