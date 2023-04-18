<?php

namespace App\Models\Seo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoProcessStatus extends Model
{
    use HasFactory;
    protected $table = "seo_process_status";

    protected $fillable = [
        'type',
        'label'
    ];
    
}
