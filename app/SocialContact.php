<?php

namespace App;

use App\Social\SocialConfig;
use App\Models\SocialMessages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialContact extends Model
{
    const INSTAGRAM = 1;

    const FACEBOOK = 2;

    const TEXT_INSTA = 'instagram';

    const TEXT_FB = 'page';

    protected $fillable = ['account_id', 'name', 'social_config_id', 'platform', 'conversation_id', 'can_reply'];

    protected $casts = [
        'can_reply' => 'boolean',
    ];

    public function socialConfig(): BelongsTo
    {
        return $this->belongsTo(SocialConfig::class);
    }

    public function messages(): HasMany
    {
        return $this
            ->hasMany(SocialMessages::class, 'social_contact_id')
            ->orderBy('created_time', 'ASC');
    }
}
