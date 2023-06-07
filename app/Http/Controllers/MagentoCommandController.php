<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use App\StoreWebsite;
use App\MagentoCommand;
use Illuminate\Http\Request;
use App\MagentoCommandRunLog;
use App\AssetsManager;
use App\LogRequest;

class MagentoCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $limit = Setting::get('pagination') ?? config('site.pagination.limit');
            $magentoCommand = MagentoCommand::paginate($limit)->appends(request()->except(['page']));
            $magentoCommandListArray = MagentoCommand::whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command_type','command_name')->toArray();
            $allMagentoCommandListArray = MagentoCommand::select(
                \DB::raw("CONCAT(COALESCE(`command_name`,''),' (',COALESCE(`command_type`,''),')') AS command"),'id','command_type')->whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command','id')->toArray();
            
            $assetsmanager = AssetsManager::all();
            $websites = StoreWebsite::all();
            $users = User::all();

            return view('magento-command.index', compact('magentoCommand', 'websites', 'users','magentoCommandListArray','assetsmanager','allMagentoCommandListArray'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    public function search(Request $request)
    {
        $magentoCommand = MagentoCommand::whereNotNull('id');
        $magentoCommandListArray = MagentoCommand::whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command_type','command_name')->toArray();
        if (! empty($request->website)) {
            $magentoCommand->where('website_ids', $request->website);
        }
        if (! empty($request->command_name)) {
            $magentoCommand->where('command_name', 'like', '%' . $request->command_name . '%');
        }
        if (! empty($request->user_id)) {
            $magentoCommand->where('user_id', '=', $request->user_id);
        }
        $limit = Setting::get('pagination') ?? config('site.pagination.limit');
        $magentoCommand = $magentoCommand->paginate($limit);
        $users = User::all();
        $websites = StoreWebsite::all();
        $allMagentoCommandListArray = MagentoCommand::select(
            \DB::raw("CONCAT(COALESCE(`command_name`,''),' (',COALESCE(`command_type`,''),')') AS command"),'id','command_type')->whereNotNull('command_type')->whereNotNull('command_name')->groupBy('command_type')->get()->pluck('command','id')->toArray();
        $assetsmanager = AssetsManager::all();
        return view('magento-command.index', compact('magentoCommandListArray','magentoCommand', 'websites', 'users','assetsmanager','allMagentoCommandListArray'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userPermissions = array_filter((!empty($request->user_permission) ? $request->user_permission : array()));
            
            if (isset($request->id) && $request->id > 0) {
                $mCom = MagentoCommand::where('id', $request->id)->first();
                $type = 'Update';

                if(!empty($mCom->user_permission)){
                    $editUserPermissions = explode(',', $mCom->user_permission);
                    
                    $userPermissions = array_unique(array_merge($userPermissions, $editUserPermissions));
                }
            } else {
                $mCom = new MagentoCommand();
                $type = 'Created';
                $loginUserId = \Auth::user()->id ?? '';

                if(strlen($loginUserId) > 0 && !in_array($loginUserId, $userPermissions)){
                    array_push($userPermissions, $loginUserId);
                }
            }
        
            $mCom->user_id = \Auth::user()->id ?? '';
            $mCom->website_ids = isset($request->websites_ids) ? implode(',', $request->websites_ids) : $mCom->websites_ids;
            $mCom->command_name = $request->command_name;
            $mCom->command_type = $request->command_type;
            $mCom->working_directory = $request->working_directory;
            $mCom->assets_manager_id = $request->assets_manager_id;
            $mCom->user_permission = implode(',', array_filter($userPermissions));
            $mCom->save();

            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function runCommand(Request $request)
    {
        try {
            $comd = \Artisan::call('command:MagentoCreatRunCommand', ['id' => $request->id]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
    
    public function runOnMultipleWebsite(Request $request)
    {
        $command_id= $request->command_id;
        $websites_ids= $request->websites_ids;
        try {
            $comd = \Artisan::call('command:MagentoRunCommandOnMultipleWebsite', ['id' => $command_id,'websites_ids' => $websites_ids] );

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully! Please check the command\'s preview response for more information']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {
            $magentoCom = MagentoCommand::find($request->id);
            $websites = StoreWebsite::all();
            $ops = '';
            foreach ($websites as $website) {
                $selected = '';
                if ($magentoCom->website_ids == $website->id) {
                    $selected = 'selected';
                }
                $ops .= '<option value="' . $website->id . '" ' . $selected . '>' . $website->name . '</option>';
            }

            return response()->json(['code' => 200, 'data' => $magentoCom, 'ops' => $ops, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function commandHistoryLog(Request $request)
    {
        try {
            $postHis = MagentoCommandRunLog::select('magento_command_run_logs.*', 'u.name AS userName')
            ->leftJoin('users AS u', 'u.id', 'magento_command_run_logs.user_id')
            ->where('command_id', '=', $request->id)->orderby('id', 'DESC')->get();

            foreach($postHis as $logs){
                $logs->status='';
                if($logs->website_ids !='' && $logs->job_id!=''){
                    $magCom = MagentoCommand::find($logs->command_id);
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
                        \Log::info("API Response: ".$result);
                        if(isset($response->data) && isset($response->data->result) ){
                            $logs->status=$response->data->status;
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
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
                        LogRequest::log($startTime, $url, 'POST', [], json_decode($response), $httpcode, \App\Http\Controllers\MagentoCommandController::class, 'commandHistoryLog');
                    }
                        
                    
                }
            }

            return response()->json(['code' => 200, 'data' => $postHis, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MagentoCommand  $magentoCommand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $postman = MagentoCommand::where('id', '=', $request->id)->delete();

            return response()->json(['code' => 200, 'data' => $postman, 'message' => 'Deleted successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function userPermission(Request $request)
    {
        try {
            $magentoCommands = MagentoCommand::where('website_ids', 'like', '%,'.$request->persmission_website.',%')
                ->orWhere('website_ids', 'like', '%,'.$request->persmission_website.'%')
                ->orWhere('website_ids', 'like', '%'.$request->persmission_website.',%')
                ->orWhere('website_ids', $request->persmission_website)
                ->get();

            foreach ($magentoCommands as $magentoCommand) {
                $userPermissions = array_filter(explode(',', $magentoCommand->user_permission));

                if(!in_array($request->persmission_user, $userPermissions)){
                    array_push($userPermissions, $request->persmission_user);
                }
                $userPermissionFormatted = implode(',', array_filter($userPermissions));
                
                $isPermissionUpdated = MagentoCommand::where('id', '=', $magentoCommand->id)->update(
                    [
                        'user_permission' => $userPermissionFormatted,
                    ]
                );
            }

            return response()->json(['code' => 200, 'message' => 'Permission Updated successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            \Log::error('Magento Command User permission Error => ' . json_decode($e) . ' #id #' . $request->id ?? '');
            //$this->PostmanErrorLog($request->id ?? '', 'Postman User permission Error', $msg, 'postman_request_creates');
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
