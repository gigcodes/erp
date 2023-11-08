<?php

namespace App\Zabbix;

use IntelliTrend\Zabbix\ZabbixApi as CoreZabbixApi;

class ZabbixApi extends CoreZabbixApi
{
    public function __construct()
    {
        parent::__construct();
        $this->login(env('ZABBIX_HOST'), env('ZABBIX_USERNAME'), env('ZABBIX_PASSWORD'));
    }
}