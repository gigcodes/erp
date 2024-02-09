<?php

namespace App\Social;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="config_id",type="integer")
     * @SWG\Property(property="caption",type="string")
     * @SWG\Property(property="post_body",type="string")
     * @SWG\Property(property="post_by",type="string")
     * @SWG\Property(property="posted_on",type="datetime")
     * @SWG\Property(property="status",type="string")
     */
    use Mediable;

    protected $fillable = [
        'config_id',
        'caption',
        'hashtag',
        'post_body',
        'post_by',
        'translation_approved_by',
        'posted_on',
        'status',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialConfig::class, 'config_id');
    }
}
