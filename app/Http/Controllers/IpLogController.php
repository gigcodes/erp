<?php

namespace App\Http\Controllers;
use App\IpLog;

use Illuminate\Http\Request;

class IpLogController extends Controller
{
    public function getIPLogs(Request $request)
    {  

        $logs = New IpLog();

        if ($request->search_ip) {
            $logs = $logs->where('ip', 'LIKE', '%' . $request->search_ip . '%');
        }
        if ($request->search_message) {
            $logs = $logs->where('message', 'LIKE', '%' . $request->search_message . '%');
        }  
        if ($request->email_ids) {
            $logs = $logs->WhereIn('email', $request->email_ids);
        }
        if ($request->status) {
            $logs = $logs->where('status', 'LIKE', '%' . $request->status . '%');
        } 
        if ($request->date) {
            $logs = $logs->where('created_at', 'LIKE', '%' . $request->date . '%');
        }

        $logs = $logs->latest()->paginate(25);
      
        return view('IpLogs.ip-log-list', compact('logs'));
       
    }
}
