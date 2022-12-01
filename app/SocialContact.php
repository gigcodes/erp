<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialContact extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const TEXT_INSTA = 'instagram';

    const TEXT_FB = 'page';

    protected $fillable = ['account_id', 'name', 'social_config_id', 'platform'];

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
}
