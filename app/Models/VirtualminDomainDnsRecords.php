<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualminDomainDnsRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'Virtual_min_domain_id',
        'dns_type',
        'content',
        'name',
        'domain_with_dns_name',
        'proxied'
    ];
}
