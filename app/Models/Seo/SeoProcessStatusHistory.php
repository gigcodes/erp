<?php

namespace App\Models\Seo;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoProcessStatusHistory extends Model
{
    use HasFactory;
    protected $table = "seo_process_status_history";
    protected $fillable = [
        'user_id',
        'seo_process_id',
        'type',
        'seo_process_status_id',
    ];

    /**
     * Modal relationship
     */
    public function status()
    {
        return $this->belongsTo(SeoProcessStatus::class, 'seo_process_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
