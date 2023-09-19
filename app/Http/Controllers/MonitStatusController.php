<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonitStatus;

class MonitStatusController extends Controller
{
    public function listMonitStatus(Request $request)
    {
        $monitStatus =  new MonitStatus();
        if ($request->service_name) {
            $monitStatus = $monitStatus->where('service_name', 'LIKE', '%' . $request->service_name . '%');
        }

        if ($request->search_memory) {
            $monitStatus = $monitStatus->where('memory', 'LIKE', '%' . $request->search_memory . '%');
        }

        if ($request->date) {
            $monitStatus = $monitStatus->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        if ($request->search_status) {
            $monitStatus = $monitStatus->where('status', 'LIKE', '%' . $request->search_status . '%');
        }

        if ($request->search_uptime) {
            $monitStatus = $monitStatus->where('uptime', 'LIKE', '%' . $request->search_uptime . '%');
        }

        $monitStatus = $monitStatus->latest()->paginate(\App\Setting::get('pagination', 25));

        return view('monit-status.monit-status-list', compact('monitStatus'));
    }
}
