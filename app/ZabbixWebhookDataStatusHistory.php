<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use App\Models\ZabbixWebhookData;
use Illuminate\Database\Eloquent\Model;

class ZabbixWebhookDataStatusHistory extends Model
{
    public function zabbixWebhookData()
    {
        return $this->belongsTo(ZabbixWebhookData::class, 'zabbix_webhook_data_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(ZabbixStatus::class, 'new_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}