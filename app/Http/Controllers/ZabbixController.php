<?php

namespace App\Http\Controllers;

use App\Host;
use App\Problem;
use App\ZabbixHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ZabbixController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Host::with('items');

            return datatables()->eloquent($query)->toJson();
        }

        return view('zabbix.index');
    }

    public function problems(Request $request)
    {
        if ($request->ajax()) {
            $query = Problem::select('eventid', 'objectid', 'name', 'hostname');

            return datatables()->eloquent($query)->toJson();
        }

        return view('zabbix.problem');
    }

    public function history(Request $request)
    {
        if ($request->ajax()) {
            $dueDate = Carbon::now()->subDays(2);
            $query = ZabbixHistory::whereDate('created_at', '>', $dueDate)->where('hostid', $request->hostid)->orderBy('created_at', 'desc')->get();

            foreach ($query as $val) {
                $host = Host::where('hostid', $val->hostid)->first();
                $val['hostname'] = $host->name;
            }

            return response()->json(['status' => 200, 'data' => $query]);
        }
    }
}
