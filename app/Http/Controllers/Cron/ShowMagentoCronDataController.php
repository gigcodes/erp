<?php

namespace App\Http\Controllers\Cron;

use App\Setting;
use App\StoreWebsite;
use App\MagentoCronData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MagentoCommand;

class ShowMagentoCronDataController extends Controller
{
    public function cronStatus()
    {
        return ['missed', 'error', 'success', 'pending', 'running'];
    }

    public function MagentoCron(Request $request)
    {
        $status = $this->cronStatus();
        $website = StoreWebsite::all()->pluck('website', 'id')->toArray();
        $data = new MagentoCronData();
        $skip = empty($request->page) ? 0 : $request->page;
        //echo"<pre>";print_r($request->all());die;
        if (isset($request->website)) {
            $data = $data->where('store_website_id', $request->website);
        }

        if (isset($request->status)) {
            $data = $data->where('cronstatus', $request->status);
        }

        if (isset($request->create_at)) {
            $date = \Carbon\Carbon::parse($request->create_at)->format('Y-m-d');
            // $date = date('Y-m-d', strtotime($request->create_at));
            $data = $data->where('cron_created_at', 'like', $date . '%');
        }

        $data = $data->orderBy('id', 'desc')->skip($skip * Setting::get('pagination'))->limit('25')->get();

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('magento_cron_data.index_ajax', compact('data'))->render();

            return response()->json(['html' => $view, 'page' => $request->page, 'count' => $count]);
        }

        return view('magento_cron_data.index', compact('data', 'status', 'website'));
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

            $command=MagentoCommand::where('website_ids',$magentoCronData->store_website_id)->where('command_type', 'like', '%' . $magentoCronData->job_code . '%')->first();
            
            if(!$command){
                return response()->json(['code' => 500, 'message' => 'Magento Cron Command is not found!']);
            }
            $command_id=$command->id;
            $comd = \Artisan::call('command:MagentoCreatRunCommand', ['id' => $command_id]);

            return response()->json(['code' => 200, 'message' => 'Magento Command Run successfully! Please check command logs']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }
}
