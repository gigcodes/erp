<?php

namespace App\Http\Controllers;

use App\Models\MonitorLog;
use App\Models\MonitorServer;
use App\Models\MonitorServersUptime;
use App\OrderStatus;
use App\PurchaseStatus;
use App\ReadOnly\ShippingStatus;
use App\ReturnExchangeStatus;
use App\StatusMapping;
use App\StatusMappingHistory;
use Illuminate\Http\Request;
use Auth;

class MonitorServerController extends Controller
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
        $status = $request->get('status');

        $monitorServers = MonitorServer::latest();

        if (! empty($keyword)) {
            $monitorServers = $monitorServers->where(function ($q) use ($keyword) {
                $q->orWhere('monitor_servers.ip', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.label', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.type', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.status', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.error', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.rtime', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.last_online', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.last_offline', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('monitor_servers.last_offline_duration', 'LIKE', '%' . $keyword . '%');
            });
        }

        if($status){
            $monitorJenkinsBuilds = $monitorServers->Where('status', $status);
        }

        $monitorServers = $monitorServers->paginate(25);

        return view('monitor-server.index', compact('monitorServers'));
    }

    public function list(Request $request)
    {
        $perPage = 10; // Number of records per page

        $monitorServers = MonitorServer::latest();
        $monitorServers = $monitorServers->where('status', 'Off');
        $monitorServers = $monitorServers->paginate($perPage);

        return response()->json($monitorServers);    
    }

    public function getServerUptimes(Request $request, $id)
    {
        $data = MonitorServersUptime::where('server_id', $id)->orderby('created_at', 'desc')->paginate(20);
        $paginateHtml = $data->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $data, 'message' => 'Server uptimes found']);
    }

    public function getServerUsers(Request $request, $id)
    {
        $monitorServer = MonitorServer::findOrFail($id);
        $serverUsers = $monitorServer->monitorUsers()->paginate(20);
        $paginateHtml = $serverUsers->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $serverUsers, 'message' => 'Server users found']);
    }

    public function getServerHistory(Request $request, $id)
    {
        $logHistory = MonitorLog::where('server_id', $id)->paginate(25);
        $paginateHtml = $logHistory->links()->render();

        return response()->json(['code' => 200, 'paginate' => $paginateHtml, 'data' => $logHistory, 'message' => 'Server history found']);
    }

    public function logHistoryTruncate()
    {
        MonitorLog::truncate();
        MonitorServersUptime::truncate();

        return redirect()->route('monitor-server.index')->withSuccess('data Removed succesfully!');
    }
}
