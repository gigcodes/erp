<?php

namespace App\Http\Controllers;

use App\Models\ZabbixWebhookData;
use Illuminate\Http\Request;

class ZabbixWebhookDataController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $eventStart = $request->get('event_start');

        $zabbixWebhookDatas = ZabbixWebhookData::latest();

        if (! empty($keyword)) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->where(function ($q) use ($keyword) {
                $q->orWhere('subject', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('message', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('event_name', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($eventStart) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->whereDate('event_start', $eventStart);
        }

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate(10);

        return view('zabbix-webhook-data.index', compact('zabbixWebhookDatas'));
    }
}
