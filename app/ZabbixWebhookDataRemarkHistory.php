<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\ZabbixWebhookData;
use Illuminate\Database\Eloquent\Model;

class ZabbixWebhookDataRemarkHistory extends Model
{
    public $fillable = [
        'zabbix_webhook_data_id',
        'remarks',
        'user_id',
    ];

    public function zabbixWebhookData()
    {
        return $this->belongsTo(ZabbixWebhookData::class, 'zabbix_webhook_data_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}