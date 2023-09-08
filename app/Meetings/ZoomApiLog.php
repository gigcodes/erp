<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomApiLog extends Model
{
    use HasFactory;

    protected $table = 'zoom_api_logs';

    protected $fillable = [
        'request_url',
        'type',
        'request_headers',
        'request_data',
        'response_status',
        'response_data',
    ];
}
