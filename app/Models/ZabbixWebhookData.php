<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZabbixWebhookData extends Model
{
    use HasFactory;

    protected $table = 'zabbix_webhook_data';

    public $fillable = [
        'subject',
        'message',
        'event_start',
        'event_name',
        'host',
        'severity',
        'operational_data',
        'event_id',
    ];
}
