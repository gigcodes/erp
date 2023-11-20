<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\VirtualminDomain;

class VirtualminDomainDnsRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'Virtual_min_domain_id',
        'identifier_id',
        'dns_type',
        'type',
        'priority',
        'content',
        'name',
        'domain_with_dns_name',
        'proxied'
    ];

    public function VirtualminDomain()
    {
        return $this->belongsTo(VirtualminDomain::class, 'Virtual_min_domain_id');
    }
}
