<?php

namespace App\Models\Seo;

use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcess extends Model
{
    use HasFactory;

    protected $table = 'seo_process';

    protected $fillable = [
        'website_id',
        'word_count',
        'suggestion',
        'user_id',
        'price',
        'is_price_approved',
        'google_doc_link',
        'seo_process_status_id',
        'live_status_link',
        'published_at',
        'status',
    ];

    /**
     * Model relationships
     */
    public function keywords()
    {
        return $this->hasMany(SeoProcessKeyword::class, 'seo_process_id');
    }

    public function seoChecklist()
    {
        return $this->hasMany(SeoProcessRemark::class, 'seo_process_id')->whereHas('processStatus', function ($query) {
            $query->where('type', 'seo_approval');
        });
    }

    public function publishChecklist()
    {
        return $this->hasMany(SeoProcessRemark::class, 'seo_process_id')->whereHas('processStatus', function ($query) {
            $query->where('type', 'publish');
        });
    }

    public function website()
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seoStatus()
    {
        return $this->belongsTo(SeoProcessStatus::class, 'seo_process_status_id');
    }
}
