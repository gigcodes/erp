<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainDnsRecordsHistory extends Model
{
    use HasFactory;

    protected $fillable = ['Virtual_min_domain_id', 'user_id', 'command', 'output', 'status', 'error'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
