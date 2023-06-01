<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorUser extends Model
{
    use HasFactory;

    public $fillable = [
        'user_name',
        'password',
        'password_reset_hash',
        'password_reset_timestamp',
        'rememberme_token',
        'level',
        'name',
        'mobile',
        'discord',
        'pushover_key',
        'pushover_device',
        'webhook_url',
        'webhook_json',
        'telegram_id',
        'jabber',
        'email',
    ];

    /**
     * The monitorServers that belong to the monitorUser.
     */
    public function monitorServers()
    {
        return $this->belongsToMany(MonitorServer::class);
    }
}