<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualminDomainHistory extends Model
{
    use HasFactory;

    protected $table = 'virtualmin_domains_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
