<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\TaskStatus;
use App\StoreWebsite;
use App\MagentoModule;
use App\AssetsManager;
use App\MagentoModuleType;
use App\MagentoModuleRemark;
use Illuminate\Http\Request;
use App\MagentoModuleHistory;
use App\MagentoModuleLogs;
use App\MagentoModuleCategory;
use App\MagentoModuleVerifiedStatus;
use App\Http\Requests\MagentoModule\MagentoModuleRequest;
use App\Http\Requests\MagentoModule\MagentoModuleRemarkRequest;
use App\MagentoModuleVerifiedStatusHistory;

class MagentoModuleController extends Controller
{
    public function __construct()
    {
        //view files
        $this->index_view = 'magento_module.index';
        $this->create_view = 'magento_module.create';
        $this->detail_view = 'magento_module.details';
        $this->edit_view = 'magento_module.edit';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (env('PRODUCTION', true)) {
            $users = User::select('name', 'id')->role('Developer')->orderby('name', 'asc')->where('is_active', 1)->get();
        } else {
            $users = User::select('name', 'id')->where('is_active', 1)->orderby('name', 'asc')->get();
        }
        $module_categories = MagentoModuleCategory::select('category_name', 'id')->where('status', 1)->get();
        $magento_module_types = MagentoModuleType::select('magento_module_type', 'id')->get();
        $task_statuses = TaskStatus::select('name', 'id')->get();
        $store_websites = StoreWebsite::select('website', 'id')->get();
        $verified_status = MagentoModuleVerifiedStatus::select('name', 'id', 'color')->get();
        $verified_status_array = $verified_status->pluck('name', 'id');

        if ($request->ajax()) {
            $items = MagentoModule::with(['lastRemark'])
                ->join('magento_module_categories', 'magento_module_categories.id', 'magento_modules.module_category_id')
                ->join('magento_module_types', 'magento_module_types.id', 'magento_modules.module_type')
                ->join('store_websites', 'store_websites.id', 'magento_modules.store_website_id')
                ->leftjoin('users', 'users.id', 'magento_modules.developer_name')
                ->leftJoin('task_statuses', 'task_statuses.id', 'magento_modules.task_status')
                ->select(
                    'magento_modules.*',
                    'magento_module_categories.category_name',
                    'magento_module_types.magento_module_type',
                    'task_statuses.name as task_name',
                    'store_websites.website',
                    'store_websites.title',
                    'users.name as developer_name1',
                    'users.id as developer_id'
                );

            if (isset($request->module) && ! empty($request->module)) {
                $items->where('magento_modules.module', 'Like', '%' . $request->module . '%');
            }

            if (isset($request->user_id) && ! empty($request->user_id)) {
                $items->where('users.user_id', $request->user_id);
            }

            if (isset($request->store_website_id) && ! empty($request->store_website_id)) {
                $items->where('magento_modules.store_website_id', $request->store_website_id);
            }

            if (isset($request->module_type) && ! empty($request->module_type)) {
                $items->where('magento_modules.module_type', $request->module_type);
            }

            if (isset($request->task_status) && ! empty($request->task_status)) {
                $items->where('magento_modules.task_status', $request->task_status);
            }

            if (isset($request->is_customized)) {
                $items->where('magento_modules.is_customized', $request->is_customized);
            }

            if (isset($request->module_category_id) && ! empty($request->module_category_id)) {
                $items->where('magento_modules.module_category_id', $request->module_category_id);
            }
            if (isset($request->site_impact)) {
                $items->where('magento_modules.site_impact', $request->site_impact);
            }

            if (isset($request->modules_status)) {
                $items->where('magento_modules.status', $request->modules_status);
            }
            if (isset($request->dev_verified_by)) {
                $items->whereIn('magento_modules.dev_verified_by', $request->dev_verified_by);
            }
            if (isset($request->lead_verified_by)) {
                $items->whereIn('magento_modules.lead_verified_by', $request->lead_verified_by);
            }
            if (isset($request->dev_verified_status_id)) {
                $items->whereIn('magento_modules.dev_verified_status_id', $request->dev_verified_status_id);
            }
            if (isset($request->lead_verified_status_id)) {
                $items->whereIn('magento_modules.lead_verified_status_id', $request->lead_verified_status_id);
            }
            $items->groupBy('magento_modules.module');
            return datatables()->eloquent($items)->addColumn('m_types', $magento_module_types)->addColumn('developer_list', $users)->addColumn('categories', $module_categories)->addColumn('website_list', $store_websites)->addColumn('verified_status', $verified_status)->toJson();
        } else {
            $title = 'Magento Module';
            $users = $users->pluck('name', 'id');
            $module_categories = $module_categories->pluck('category_name', 'id');
            $magento_module_types = $magento_module_types->pluck('magento_module_type', 'id');
            $task_statuses = $task_statuses->pluck('name', 'id');
            $store_websites = $store_websites->pluck('website', 'id');

            return view($this->index_view, compact('title', 'module_categories', 'magento_module_types', 'task_statuses', 'store_websites', 'users','verified_status','verified_status_array'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Magento Module';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $magento_module_types = MagentoModuleType::get()->pluck('magento_module_type', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->create_view, compact('module_categories', 'title', 'task_statuses', 'magento_module_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MagentoModuleRequest $request)
    {
        // dd($request->all());
        $input = $request->except(['_token']);

        $data = MagentoModule::create($input);

        if ($data) {
            $input_data = $data->toArray();
            $input_data['magento_module_id'] = $data->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            MagentoModuleHistory::create($input_data);

            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Magento Module saved successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoModule $magento_module)
    {
        $title = 'Magento Module Details';

        if (request()->ajax() && $magento_module) {
            return response()->json([
                'data' => view('magento_module.partials.data', compact('magento_module'))->render(),
                'title' => $title,
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'data' => '',
                'title' => $title,
                'code' => 500,
            ], 500);
        }

        return view($this->detail_view, compact('title', 'magento_module'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MagentoModule $magento_module)
    {
        $title = 'Magento Module';
        $module_categories = MagentoModuleCategory::where('status', 1)->get()->pluck('category_name', 'id');
        $task_statuses = TaskStatus::pluck('name', 'id');

        return view($this->edit_view, compact('module_categories', 'title', 'magento_module', 'task_statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(MagentoModuleRequest $request, MagentoModule $magento_module)
    {
        $input = $request->except(['_token']);
        $data = $magento_module->update($input);

        if ($data) {
            $input_data = $magento_module->toArray();
            $input_data['magento_module_id'] = $magento_module->id;
            unset($input_data['id']);
            $input_data['user_id'] = auth()->user()->id;
            // dd($input_data);
            MagentoModuleHistory::create($input_data);

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoModule $magento_module)
    {
        $data = $magento_module->delete();

        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRemark(MagentoModuleRemarkRequest $request)
    {
        $input = $request->except(['_token']);
        $input['user_id'] = Auth::user()->id;

        $magento_module_remark = MagentoModuleRemark::create($input);

        if ($magento_module_remark) {
            if($input['type'] == 'general') {
                $update = MagentoModule::where('id', $request->magento_module_id)->update(['last_message' => $request->remark]);
            }
            if($input['type'] == 'dev') {
                $update = MagentoModule::where('id', $request->magento_module_id)->update(['dev_last_remark' => $request->remark]);
            }
            if($input['type'] == 'lead') {
                $update = MagentoModule::where('id', $request->magento_module_id)->update(['lead_last_remark' => $request->remark]);
            }
            // dd($update, $request->magento_module_id, $request->remark);
            return response()->json([
                'status' => true,
                'message' => 'Remark added successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Remark added unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRemarks($magento_module, $type)
    {
        $remarks = MagentoModuleRemark::with(['user'])->where('magento_module_id', $magento_module)->where('type', $type)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark added successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getVerifiedStatusHistories($magento_module, $type)
    {
        $histories = MagentoModuleVerifiedStatusHistory::with(['user', 'newStatus'])->where('magento_module_id', $magento_module)->where('type', $type)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get verified status',
            'status_name' => 'success',
        ], 200);
    }

    public function updateMagentoModuleOptions(Request $request)
    {
        $oldData = MagentoModule::where('id', (int) $request->id)->first();
        $updateMagentoModule = MagentoModule::where('id', (int) $request->id)->update([$request->columnName => $request->data]);

        if ($request->columnName == 'dev_verified_status_id' || $request->columnName == 'lead_verified_status_id') {
            if ($request->columnName == 'dev_verified_status_id') {
                $type = 'dev';
                $oldStatusId = $oldData->dev_verified_status_id;
            }
            if ($request->columnName == 'lead_verified_status_id') {
                $type = 'lead';
                $oldStatusId = $oldData->lead_verified_status_id;
            }
            $this->saveVerifiedStatusHistory($oldData, $oldStatusId, $request->data, $type);
        }

        if ($updateMagentoModule) {
            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
                'status_name' => 'success',
                'code' => 200,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Updated unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    public function magentoModuleList()
    {
        $storeWebsites = StoreWebsite::pluck('title', 'id')->toArray();

        $magento_modules = MagentoModule::groupBy('module')->orderBy('module', 'asc')->get();
        $magento_modules_array = MagentoModule::orderBy('module', 'asc')->get()->toArray();
        
        $result = [];
        array_walk($magento_modules_array, function ($value, $key) use (&$result) {
            $result[$value['store_website_id']][] = $value;
        });
        $magento_modules_array=$result;
        $magento_modules_count=MagentoModule::count();
        
        return view('magento_module.magento-listing', ['magento_modules' => $magento_modules, 'storeWebsites' => $storeWebsites,'magento_modules_array'=>$magento_modules_array,'magento_modules_count'=>$magento_modules_count]);
    }

    public function magentoModuleUpdateStatuslogs(Request $request){
        
        $store_website_id=$request->store_website_id;
        $magento_module_id=$request->magento_module_id;
        
        $histories = \App\MagentoModuleLogs::select('magento_module_logs.*', 'u.name AS userName')->leftJoin('users AS u', 'u.id', 'magento_module_logs.updated_by')->where('magento_module_id', $magento_module_id)->where('store_website_id', $store_website_id)->latest()->get();

        foreach($histories as $logs){
            if($logs->store_website_id !='' && $logs->job_id!=''){
                
                $storeWebsite=StoreWebsite::where('id', $logs->store_website_id)->first();
                $assetsmanager = new AssetsManager;
                if($storeWebsite){
                    $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
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
                }
            }  
        }
        return response()->json(['code' => 200, 'data' => $histories]);
    }
    public function runMagentoCacheFlushCommand($magento_module_id,$store_website_id,$client_id,$cwd){
        $updated_by=auth()->user()->id;
        $cmd="bin/magento cache:flush";
        \Log::info("Start cache:flush");

        $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
        $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $parameters = [
            'command' => $cmd, 
            'cwd' => $cwd,
            'is_sudo' => true, 
            'timeout_sec' => 300, 
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

        $headers = [];
        $headers[] = 'Authorization: Basic '.$key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \Log::info("API result: ".$result);
        \Log::info("API Error Number: ".curl_errno($ch));
        if (curl_errno($ch)) {
            \Log::info("API Error: ".curl_error($ch));
            MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' => curl_error($ch)]);
        }
        $response = json_decode($result);

        curl_close($ch);
                
        if(isset($response->errors)){
            $message='';
            foreach($response->errors as $error){
                $message.=" ".$error->code.":".$error->title.":".$error->detail;
            }
            MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' => $message]);
            \Log::info($message);
        }else{
            if(isset($response->data) && isset($response->data->jid) ){
                $job_id=$response->data->jid;
                $status="Success";
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Success", 'response' => 'Success', 'job_id' => $job_id]);
                \Log::info("Job Id:".$job_id);
            }else{
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' =>"Job Id not found in response"]);
                    
                \Log::info("Job Id not found in response!");
            }
        }

        \Log::info("End cache:flush");
        return true;
    }
    public function magentoModuleUpdateStatus(Request $request){

        $store_website_id=$request->store_website_id;
        $magento_module_id=$request->magento_module_id;
        $status=$request->status;
        $magento_modules = MagentoModule::where('id',$magento_module_id)->where('store_website_id',$store_website_id)->first();
        if(!$magento_modules){
            return response()->json(['status' => 500,  'message' => 'The Magento module is not found on the store website!']);
        }

        $updated_by=auth()->user()->id;
        $cmd="bin/magento module:disable ". $magento_modules->module;

        if($status){
            $cmd="bin/magento module:enable ". $magento_modules->module;
        }
        $cmd.=" && bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento cache:flush";
        \Log::info("Start Magento module change status");
        $storeWebsite=StoreWebsite::where('id', $store_website_id)->first();
        $cwd='';
        $assetsmanager = new AssetsManager;
        if($storeWebsite){
            $cwd=$storeWebsite->working_directory;
            $assetsmanager = AssetsManager::where('id', $storeWebsite->assets_manager_id)->first();
        }

        if($assetsmanager && $assetsmanager->client_id!='')
        {
            
            
            $client_id=$assetsmanager->client_id;
            //$url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/commands";
            $url="https://s10.theluxuryunlimited.com:5000/api/v1/clients/".$client_id."/scripts";
            $key=base64_encode("admin:86286706-032e-44cb-981c-588224f80a7d");
            
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $parameters = [
                //'command' => $cmd,
                'script' => base64_encode($cmd), 
                'cwd' => $cwd,
                'is_sudo' => true, 
                'timeout_sec' => 900, 
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $headers = [];
            $headers[] = 'Authorization: Basic '.$key;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            \App\LogRequest::log($startTime, $url, 'POST', json_encode($parameters), json_decode($result), 
            $httpcode,App\Http\Controllers\MagentoModuleController::class, 'update');
            
            \Log::info("API result: ".$result);
            \Log::info("API Error Number: ".curl_errno($ch));
            if (curl_errno($ch)) {
                \Log::info("API Error: ".curl_error($ch));
                
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' => curl_error($ch)]);
                
                return response()->json(['code' => 500, 'message' =>curl_error($ch)]);
            }
            
            $response = json_decode($result);

            curl_close($ch);
                
            if(isset($response->errors)){ 
                $message='';
                foreach($response->errors as $error){
                    $message.=" ".$error->code.":".$error->title.":".$error->detail;
                }
                
                MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' => $message]);
                \Log::info($message);
                return response()->json(['code' => 500, 'message' => $message]);

            }else{
                if(isset($response->data) && isset($response->data->jid) ){
                    $job_id=$response->data->jid;
                    $status="Success";
                    
                    MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Success", 'response' => 'Success', 'job_id' => $job_id]);
                    $magento_modules->status=$status;
                    $magento_modules->save();
                    \Log::info("Job Id:".$job_id);
                    //$this->runMagentoCacheFlushCommand($magento_module_id,$store_website_id,$client_id,$cwd);
                    return response()->json(['code' => 200, 'data' => $magento_modules,'message'=>'Magento module status change successfully']);
                }else{
                    MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' =>"Job Id not found in response"]);
                    
                    \Log::info("Job Id not found in response!");
                    return response()->json(['code' => 500, 'message' => 'Job Id not found in response!']);
                }
            }
        }else{
            
            MagentoModuleLogs::create(['magento_module_id' => $magento_module_id,'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => "Error", 'response' => "Assets Manager & Client id not found the Store Website!"]);
            
            \Log::info("Assets Manager & Client id not found the Store Website!");
            
            return response()->json(['code' => 500, 'message' => 'Assets Manager & Client id not found the Store Website!']);
        }

        \Log::info("End Magento module change status");

        return response()->json(['status' => 500,  'message' => 'error!']);
    }
    
    public function storeVerifiedStatus(Request $request)
    {
        $input = $request->except(['_token']);

        $data = MagentoModuleVerifiedStatus::create($input);

        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Stored successfully',
                'status_name' => 'success',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something error occurred',
                'status_name' => 'error',
            ], 500);
        }
    }

    protected function saveVerifiedStatusHistory($magentoModule, $oldStatusId, $newStatusId, $statusType)
    {
        $history = new MagentoModuleVerifiedStatusHistory();
        $history->magento_module_id = $magentoModule->id;
        $history->old_status_id = $oldStatusId;
        $history->new_status_id = $newStatusId;
        $history->type = $statusType;
        $history->user_id = Auth::user()->id;
        $history->save();

        return true;
    }

    public function verifiedStatusUpdate(Request $request)
    {
        $statusColor = $request->all();
        $data = $request->except('_token');
        foreach ($statusColor['color_name'] as $key => $value) {
            $magentoModuleVerifiedStatus = MagentoModuleVerifiedStatus::find($key);
            $magentoModuleVerifiedStatus->color = $value;
            $magentoModuleVerifiedStatus->save();
        }

        return redirect()->back()->with('success', 'The verified status color updated successfully.');
    }
}
