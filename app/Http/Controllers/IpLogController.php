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

    public function whitelistIP(Request $request)
    {
        $validatedData = $request->validate([
            'server_name' => 'required',
            'ip_address' => 'required|ip',
            'comment' => 'required',
        ]);

        $serverName = $validatedData['server_name'];
        $ipAddress = $validatedData['ip_address'];
        $comment = $validatedData['comment'];

        $command = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'webaccess-firewall.sh' .  '-f ' .$serverName . '-i' . $ipAddress . '-c ' .$comment;

        $allOutput = [];
        $allOutput[] = $command;
        $result = exec($command, $allOutput);

        if ($result == '') {
            $result = 'Not any response';
        } elseif ($result == 0) {
            $result = 'Command run success Response ' . $result;
        } elseif ($result == 1) {
            $result = 'Command run Fail Response ' . $result;
        } else {
            $result = is_array($result) ? json_encode($result, true) : $result;
        }

        \Log::info(print_r($result, true));
        
        return response()->json(['message' => $result, 'code' => 200]);
    }
}
