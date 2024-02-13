<?php

namespace App;

use App\Models\SocialMessages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialContact extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const TEXT_INSTA = 'instagram';

    const TEXT_FB = 'page';

    protected $fillable = ['account_id', 'name', 'social_config_id', 'platform', 'conversation_id'];

    public function socialConfig()
    {
        return $this->belongsTo(\App\Social\SocialConfig::class);
    }

    public function socialContactThread()
    {
        return $this->hasMany(\App\SocialContactThread::class)->orderBy('sending_at', 'DESC');
    }

    public function getLatestSocialContactThread()
    {
        return $this->hasone(\App\SocialContactThread::class, 'social_contact_id')->orderBy('sending_at', 'DESC');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SocialMessages::class, 'social_contact_id');
    }
}
