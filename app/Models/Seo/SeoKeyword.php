<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    use HasFactory;

    protected $table = 'seo_keywords';
    protected $fillable = [
        'seo_process_id',
        'keyword',
        'value',
        'content',
        'word_count',
        'status'
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
