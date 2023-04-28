<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoProcessRemark extends Model
{
    use HasFactory;

    protected $table = 'seo_process_remarks';

    public $timestamps = false;

    protected $fillable = [
        'seo_process_id',
        'seo_process_status_id',
        'remark',
        'index',
    ];

    /**
     * Model realtinship
     */
    public function processStatus()
    {
        return $this->belongsTo(SeoProcessStatus::class, 'seo_process_status_id');
    }
}
