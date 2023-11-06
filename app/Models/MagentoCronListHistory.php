<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagentoCronListHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cron_id', 'user_id', 'store_website_id', 'server_ip', 'request_data', 'response_data', 'job_id', 'status', 'working_directory', 'last_execution_time'
    ];

    public function website()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'website_ids');
    }
}








