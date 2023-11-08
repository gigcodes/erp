<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualminDomainDnsLogs extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'dns_type', 'created_by', 'command', 'job_id', 'status', 'response'];
}
