<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarnishStatsLogs extends Model
{
    use HasFactory;

    protected $fillable = ['request_data', 'response_data'];
}
