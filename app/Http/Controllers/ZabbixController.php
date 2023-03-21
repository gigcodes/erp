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
        $search_data =  Problem::orderBy('id', 'asc')->get();

        $problems = Problem::orderBy('id', 'asc');

        if(!empty($request->host_name))
        {
            $problems->where('hostname',$request->host_name);
        }
        if(!empty($request->problem))
        {
            $problems->where('name',$request->problem);
        }
        if(!empty($request->event_id))
        {
            $problems->where('eventid',$request->event_id);
        }
        if(!empty($request->object_id))
        {
            $problems->where('objectid',$request->object_id);
        }
        $problems = $problems->paginate(25)->appends(request()->except(['page']));

        $totalentries = count($problems);

        return view('zabbix.problem',compact('problems','totalentries','search_data'));
    }

    public function history(Request $request)
    {
        if ($request->ajax()) {
            $dueDate = Carbon::now()->subDays(2);
            $query = ZabbixHistory::whereDate('created_at', '>', $dueDate)->where('host_id', $request->hostid)->orderBy('created_at', 'desc')->get();
            foreach ($query as $val) {
                $host = Host::where('hostid', $val->host_id)->first();
                $val['hostname'] = $host->name;
            }

            return response()->json(['status' => 200, 'data' => $query]);
        }
    }
}
