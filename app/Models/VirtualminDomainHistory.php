<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class VirtualminDomainHistory extends Model
{
    use HasFactory;

    protected $table = 'virtualmin_domains_histories';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
