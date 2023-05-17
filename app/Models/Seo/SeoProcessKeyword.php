<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessKeyword extends Model
{
    use HasFactory;

    protected $table = 'seo_process_keywords';

    public $timestamps = false;

    protected $fillable = [
        'seo_process_id',
        'name',
        'index',
    ];

    /**
     * Model accrssor and mutator
     */
    public function getKeywordTypeAttribute()
    {
        $data = $this->remarks()->first();

        return $data->processStatus->type;
    }

    /**
     * Model relationships
     */
    public function seoRemarks()
    {
        return $this->hasMany(SeoKeywordRemark::class, 'seo_keywords_id')->whereHas('processStatus', function ($query) {
            $query->where('type', 'seo_approval');
        });
    }

    public function publishRemarks()
    {
        return $this->hasMany(SeoKeywordRemark::class, 'seo_keywords_id')->whereHas('processStatus', function ($query) {
            $query->where('type', 'publish');
        });
    }
}
