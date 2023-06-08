<?php

namespace App\Http\Controllers\Cron;

use App\CronStatus;
use App\Setting;
use App\StoreWebsite;
use App\MagentoCronData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MagentoCommand;
use App\MagentoCommandRunLog;
use App\AssetsManager;

class ShowMagentoCronDataController extends Controller
{
    public function cronStatus()
    {
        return ['missed', 'error', 'success', 'pending', 'running'];
    }

    public function MagentoCron(Request $request)
    {
        // $status = $this->cronStatus();
        $status = CronStatus::all();

        $website = StoreWebsite::all()->pluck('website', 'id')->toArray();
        $magentoCronWebsites = MagentoCronData::whereNotNull('website')->where("website", "!=", "NULL")->groupby('website')->pluck("website");

        $data = new MagentoCronData();
        $skip = empty($request->page) ? 0 : $request->page;
        //echo"<pre>";print_r($request->all());die;
        if (isset($request->website)) {
            $data = $data->where('website', $request->website);
        }

        if (isset($request->status)) {
            $data = $data->where('cronstatus', $request->status);
        }

        if (isset($request->create_at)) {
            $date = \Carbon\Carbon::parse($request->create_at)->format('Y-m-d');
            // $date = date('Y-m-d', strtotime($request->create_at));
            $data = $data->where('cron_created_at', 'like', $date . '%');
        }

        $data = $data->where(function ($query) {
            $query->orWhere([
                ["cronstatus", "=", 'pending'],
                ["cron_executed_at", "=", NULL]
            ])
            ->orWhere([
                ["cronstatus", "=", 'pending'],
                ["cron_executed_at", "=", "0000-00-00 00:00:00"]
            ])
            ->orWhere("cronstatus", "=", 'error')
            ->orWhere("cronstatus", "=", 'missed')
            ->orWhere("cronstatus", "=", 'success');
        });

        $data = $data->orderBy('id', 'desc')->skip($skip * Setting::get('pagination'))->limit('25')->get();

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('magento_cron_data.index_ajax', compact('data'))->render();

            return response()->json(['html' => $view, 'page' => $request->page, 'count' => $count]);
        }

        return view('magento_cron_data.index', compact('data', 'status', 'website', 'magentoCronWebsites'));
    }
    public function runMagentoCron(Request $request)
    {
       
        try {
            if(!isset($request->id) ||$request->id==''){
                return response()->json(['code' => 500, 'message' => 'Requested data is missing!']);
            }
           
            $magentoCronData= MagentoCronData::where('id',$request->id)->first();
            
            if(!$magentoCronData){
                return response()->json(['code' => 500, 'message' => 'Magento Cron Data is not found!']);
            }

            $commands=MagentoCommand::where('assets_manager_id',$magentoCronData->store_website_id)->where('command_type', 'like', '%' . $magentoCronData->job_code . '%')->get();
            
            if(!$commands){
                return response()->json(['code' => 500, 'message' => 'Magento Cron Command is not found!']);
            }
            foreach($commands as $command){
                $command_id=$command->id;
                $comd = \Artisan::call('command:MagentoCreatRunCommand', ['id' => $command_id]);
            }
            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully! Please check command logs']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function statusColor(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $cronStatus = CronStatus::find($key);
            $cronStatus->color = $value;
            $cronStatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }
    
    public function commandHistoryLog(Request $request)
    {
        try {

            if(!isset($request->id) ||$request->id==''){
                return response()->json(['code' => 500, 'message' => 'Requested data is missing!']);
            }
           
            $magentoCronData= MagentoCronData::where('id',$request->id)->first();
            
            if(!$magentoCronData){
                return response()->json(['code' => 500, 'message' => 'Magento Cron Data is not found!']);
            }

            $commands=MagentoCommand::where('assets_manager_id',$magentoCronData->store_website_id)->where('command_type', 'like', '%' . $magentoCronData->job_code . '%')->get();
            
            if(!$commands){
                return response()->json(['code' => 500, 'message' => 'Magento Cron Command is not found!']);
            }
            foreach($commands as $command){
                $commands_id[]=$command->id;
            }
            $postHis = MagentoCommandRunLog::select('magento_command_run_logs.*', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'magento_command_run_logs.user_id')
            ->whereIn('command_id', $commands_id)->orderby('id', 'DESC')->get();

            foreach($postHis as $logs){
                $magCom = MagentoCommand::find($logs->command_id);
                    if($magCom->website_ids == 'ERP'){
                        $logs->website="ERP";
                    }else{
                        $logs->website=$magCom->website->title;
                    }
                    $logs->working_directory=$magCom->working_directory;
                    
                if($logs->website_ids !='' && $logs->job_id!=''){
                    if($magCom->website_ids==$logs->website_ids){
                        $assetsmanager = AssetsManager::where('id', $magCom->assets_manager_id)->first();
                    }else{
                        $assetsmanager = AssetsManager::where('website_id', $logs->website_ids)->first();
                    }
                    
                    if($assetsmanager && $assetsmanager->client_id!=''){
                        $client_id=$assetsmanager->client_id;
                        $job_id=$logs->job_id;
                        $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands/".$job_id;
                        $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                        
                        $headers = [];
                        $headers[] = 'Authorization: Basic '.$key;
                        //$headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            
                        }
                        $response = json_decode($result);
                        if(isset($response->data) && isset($response->data->result) ){
                            $result=$response->data->result;
                            $message='';
                            if(isset($result->stdout) && $result->stdout!=''){
                                $message.='Output: '.$result->stdout;
                            }
                            if(isset($result->stderr) && $result->stderr!=''){
                                $message.='Error: '.$result->stderr;
                            }
                            if(isset($result->summary) && $result->summary!=''){
                                $message.='summary: '.$result->summary;
                            }
                            if($message!=''){
                                $logs->response=$message;
                            }
                        }

                        curl_close($ch);
                    }
                        
                    
                }
            }

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
