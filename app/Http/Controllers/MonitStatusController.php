<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonitStatus;
use Auth;
use App\AssetsManager;
use App\Models\MonitUnitCommandRunLogs;

class MonitStatusController extends Controller
{
    public function listMonitStatus(Request $request)
    {
        $assetsmanager = AssetsManager::where('monit_api_url', '!=', '')->get();

        //MonitStatus::truncate();

        $iii = 0;
        $monitStatusArray = [];
        if(!empty($assetsmanager)){

            foreach ($assetsmanager as $key => $value) {
            
                // URL of the XML data source
                $url = $value->monit_api_url;
                
                // Your username and password for authentication
                $username = $value->monit_api_username;
                $password = $value->monit_api_password;

                // Initialize cURL session
                $ch = curl_init($url);

                // Set cURL options for authentication
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

                // Execute the cURL request
                $response = curl_exec($ch);

                // Check for cURL errors
                if (curl_errno($ch)) {
                } else {
                    // Parse and process the XML data
                    $xml = simplexml_load_string($response);

                    if(!empty($xml)){

                        $json = json_encode($xml);
                        $xmlArray = json_decode($json,TRUE);

                        MonitStatus::where('xmlid', $xmlArray['server']['id'])->delete();

                        foreach ($xmlArray['service'] as $key => $valueXaml) {

                            /*$monitStatusArray[$iii]['url'] = $url;
                            $monitStatusArray[$iii]['username'] = $username;
                            $monitStatusArray[$iii]['password'] = $password;*/

                            $url = $url;
                            $username = $username;
                            $password = $password;

                            //$monitStatusArray[$iii]['id'] = '';
                            $id = '';
                            if(!empty($xmlArray['server']['id'])){
                                //$monitStatusArray[$iii]['id'] = $xmlArray['server']['id'];
                                $id = $xmlArray['server']['id'];
                            }

                            //$monitStatusArray[$iii]['service_name'] = '';
                            $service_name = '';
                            if(!empty($valueXaml['name'])){
                                //$monitStatusArray[$iii]['service_name'] = $valueXaml['name'];
                                $service_name = $valueXaml['name'];
                            }
                            
                            //$monitStatusArray[$iii]['uptime'] = '';
                            $uptime = '';
                            if(!empty($xmlArray['server']['uptime'])){
                                //$monitStatusArray[$iii]['uptime'] = $xmlArray['server']['uptime'];
                                $uptime = $xmlArray['server']['uptime'];
                            }

                            //$monitStatusArray[$iii]['status'] = $valueXaml['status'];
                            $status = $valueXaml['status'];

                            //$monitStatusArray[$iii]['memory'] = '';
                            $memory = '';
                            if(!empty($valueXaml['memory'])){
                                $memory = json_encode($valueXaml['memory']);
                                //$monitStatusArray[$iii]['memory'] = json_encode($valueXaml['memory']);
                            }
                            
                            //$monitStatusArray[$iii]['server'] = '';
                            $ip = '';
                            if(!empty($value['ip'])){
                                //$monitStatusArray[$iii]['server'] = $value['ip'];
                                 $ip = $value['ip'];
                            }

                            /*$monitStatusArray[$iii]['dir'] = "/home/prod-1-1/current/";
                            $iii++;*/

                            MonitStatus::create(['service_name' => $service_name, 'status' => $status, 'uptime' => $uptime, 'memory' => json_encode($memory), 'url' => $url, 'username' => $username, 'password' => $password, 'xmlid' => $id.'-'.strtolower($service_name), 'ip' => $ip]);
                        }
                    }
                    
                }

                // Close cURL session
                curl_close($ch);

            }
        }

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

        //$monitStatus = $monitStatus->latest()->paginate(\App\Setting::get('pagination', 25));
        $monitStatus = $monitStatus->latest()->get();

        return view('monit-status.monit-status-list', compact('monitStatus'));
    }

    public function runCommand(Request $request)
    {
        try {

            $url = 'http://s10.theluxuryunlimited.com:5000/execute';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $ipAddress = $request->server_ip; // Valid IP address as a string

            // Ensure it's a string before use
            $ipString = (string)$ipAddress;

            $parameters = [
                'command' => $request->command,
                'server' => $ipString,
                'dir' => "/home/prod-1-1/current/"
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $result = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \Log::info('API result: ' . $result);
            \Log::info('API Error Number: ' . curl_errno($ch));

            $response = json_decode($result);

            curl_close($ch);
            
            MonitUnitCommandRunLogs::create(['created_by' => auth()->user()->id, 'xmlid' => $request->id, 'request_data' => json_encode($parameters), 'response_data' => json_decode($result)]);


            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function monitApiHistory($id)
    {
        $datas = MonitUnitCommandRunLogs::with('user')
            ->where('xmlid', $id)
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
