<?php

namespace App\Models\Seo;

use App\StoreWebsite;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoProcess extends Model
{
    use HasFactory;

    protected $table = "seo_process";

    protected $fillable = [
        'website_id',
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
        return $this->hasMany(SeoKeyword::class, 'seo_process_id');
    }

    public function website()
    {
        return $this->belongsTo(StoreWebsite::class, 'website_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
