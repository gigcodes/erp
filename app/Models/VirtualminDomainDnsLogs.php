<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualminDomainDnsLogs extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'created_by', 'command', 'job_id', 'status', 'response'];
}
