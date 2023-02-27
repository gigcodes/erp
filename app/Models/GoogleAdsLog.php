<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAdsLog extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'type',
        'module',
        'message',
        'response',
        'user_ip_address',
    ];

    public function user(){
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
