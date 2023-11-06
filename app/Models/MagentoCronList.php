<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MagentoCronList extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'cron_name', 'last_execution_time', 'last_message', 'cron_status', 'frequency',
    ];

    public function website()
    {
        return $this->belongsTo(\App\StoreWebsite::class, 'website_ids');
    }
}
