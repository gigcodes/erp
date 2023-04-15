<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seo\SeoProcessStatus;

class SeoKeywordRemark extends Model
{
    use HasFactory;
    protected $table = 'seo_keyword_remarks';
    
    protected $fillable = [
        'seo_keywords_id',
        'seo_process_status_id',
        'remarks',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Model realtinship
     */
    public function processStatus()
    {
        return $this->belongsTo(SeoProcessStatus::class, 'seo_process_status_id');
    }
    
}
