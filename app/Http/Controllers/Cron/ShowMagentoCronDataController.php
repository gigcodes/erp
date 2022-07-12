<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use App\StoreWebsite;
use App\MagentoCronData;

class ShowMagentoCronDataController extends Controller
{
    public function cronStatus(){
       return array('missed','error','success','pending','running');
    }

    public function MagentoCron(Request $request)
    {
        $status = $this->cronStatus();
        $website = StoreWebsite::all()->pluck('website','id')->toArray();
        $data = new MagentoCronData();
        $skip = empty($request->page) ? 0 : $request->page;
        //echo"<pre>";print_r($request->all());die;
        if(isset($request->website)){
            $data = $data->where('store_website_id',$request->website);
        }

        if(isset($request->status)){
            $data = $data->where('cronstatus',$request->status);
        }

        if(isset($request->create_at)){ 

            $date = \Carbon\Carbon::parse($request->create_at)->format('Y-m-d'); 
           // $date = date('Y-m-d', strtotime($request->create_at));
            $data = $data->where('cron_created_at','like',$date. '%');
        }


        $data = $data->orderBy('id','desc')->skip($skip * Setting::get('pagination'))->limit('25')->get();

        if ($request->ajax()) {
            $count = $request->count;
            $view = view('magento_cron_data.index_ajax', compact('data'))->render();
            return response()->json(['html'=>$view, 'page'=>$request->page, 'count'=>$count]);
        }
        
        return view('magento_cron_data.index', compact('data','status','website'));
    }
}
