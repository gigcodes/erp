<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Http\Controllers\WhatsAppController;
use App\MagentoLogHistory;
use App\StoreWebsite;
use Auth;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

use App\ProductPushErrorLog;
use App\Exports\MagentoProductCommonError;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class MagentoProductPushErrors extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "List | Magento Log Errors";

        $websites = StoreWebsite::get();

        return view('magento-product-error.index', compact('title', 'websites'));
    }

    public function records(Request $request)
    {
        $keyword = $request->get("keyword");

        if($request->website !== '' && $request->website !== 'all'){
            $records = ProductPushErrorLog::whereHas('store_website', function($q) use($request){
                $q->where('id', $request->website);
            });
//                ->where('response_status','error');
        }else{

            $records = ProductPushErrorLog::with('store_website');
//                ->where('response_status','error');
        }


        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("message", "LIKE", "%$keyword%");
            });
        }
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("message", "LIKE", "%$keyword%");
            });
        }

        if(!empty($request->log_date)){
            $log_date = date("Y-m-d", strtotime($request->log_date));
//            dd($log_date);
            $records = $records->whereBetween('created_at', [$log_date.' 00:00:00', $log_date. ' 23:59:59']);
        }


        $records = $records->latest()->paginate(50);

        $recorsArray = [];

        foreach ($records as $row) {

            $recorsArray[] = [
                'product_id'      => $row->product_id,
                'updated_at'      => $row->created_at->format('d-m-y H:i:s'),
                'store_website'   => $row->store_website->title,
                'message'         => str_limit($row->message, 30, 
                    '<a data-logid='.$row->id.' class="message_load">...</a>'),
                'request_data'    => str_limit($row->message, 30,
                    '<a data-logid='.$row->id.' class="request_data_load">...</a>'),
                'response_data'   => str_limit($row->response_data, 30, 
                    '<a data-logid='.$row->id.' class="response_data_load">...</a>'),
//                'response_status' => $row->response_status,
                'response_status' => ' <select class="form-control" name="error_status" id="error_status" data-log_id="'.$row->id.'">
 <option value="" ></option>
 <option value="error" '.($row->response_status == 'error' ? 'selected' : '' ).'>Error</option>
 <option value="php" '.($row->response_status === 'php' ? 'selected' : '' ).'>Php</option>
 <option value="magento" '.($row->response_status == 'magento' ? 'selected' : '' ).'>Magento</option>
</select> <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-logs-history" title="Show Logs History" data-id="'.$row->id.'">
                <i class="fa fa-info-circle"></i>
            </button>',
            ];
        }    

        return response()->json([
            "code"       => 200,
            "data"       => $recorsArray,
            "pagination" => (string) $records->links(),
            "total"      => $records->total(),
            "page"       => $records->currentPage(),
        ]);
        
    }

    public function getLoadDataValue(Request $request)
    {
        
        $records = ProductPushErrorLog::where('id',$request->id)->first();

        $fulltextvalue = $records[$request->field];
        
        return response()->json(["code" => 200, "data" => $fulltextvalue]);
    }

    public function groupErrorMessage(Request $request){

        $records = ProductPushErrorLog::where('response_status','error')
            ->whereDate('created_at',Carbon::now()->format('Y-m-d'))
            ->latest('count')
            ->groupBy('message')
            ->select(\DB::raw('*,COUNT(message) AS count'))
            ->get();

        $recordsArr = []; 

        //START - Purpose : Comment code , Data sorting - DEVTASK-20123
        // foreach($records as $row){

        //     $recordsArr[] = [
        //         'count' => $row->count,
        //         'message' => $row->message,

        //     ];
        // }

        foreach($records as $row){
            
            if (strpos($row->message, 'Failed readiness') !== false) {

                if (array_key_exists("Failed_readiness",$recordsArr))
                {
                    $recordsArr['Failed_readiness']['count'] = $recordsArr['Failed_readiness']['count'] + 1;
                    $recordsArr['Failed_readiness']['message'] = 'Failed readiness';
                }else{
                    $recordsArr['Failed_readiness'] = [
                        'count' => 1,
                        'message' => $row->message,
    
                    ];
                }
            }else{
                $recordsArr[] = [
                    'count' => $row->count,
                    'message' => $row->message,

                ];
            }
        }
        
        usort($recordsArr, function($a, $b) {
            return $a['count'] - $b['count'];
        });

        rsort($recordsArr);
        //END - DEVTASK-20123

        // echo "<pre>";
        // print_r($recordsArr);
        // exit;

        $filename = 'Today Report Magento Errors.xlsx';
        return Excel::download(new MagentoProductCommonError($recordsArr),$filename);
    }

    //START - Purpose : Open modal and get data - DEVTASK-20123
    public function groupErrorMessageReport(Request $request)
    {
        $records = ProductPushErrorLog::where('response_status','error')
            ->whereDate('created_at',Carbon::now()->format('Y-m-d'))
            ->latest('count')
            ->groupBy('message')
            ->select(\DB::raw('*,COUNT(message) AS count'),)
            ->get();

        $recordsArr = []; 
        foreach($records as $row){
            
            if (strpos($row->message, 'Failed readiness') !== false) {

                if (array_key_exists("Failed_readiness",$recordsArr))
                {
                    $recordsArr['Failed_readiness']['count'] = $recordsArr['Failed_readiness']['count'] + 1;
                    $recordsArr['Failed_readiness']['message'] = 'Failed readiness';
                }else{
                    $recordsArr['Failed_readiness'] = [
                        'count' => 1,
                        'message' => $row->message,
    
                    ];
                }
            }else{
                $recordsArr[] = [
                    'count' => $row->count,
                    'message' => $row->message,

                ];
            }
        }
        
        usort($recordsArr, function($a, $b) {
            return $a['count'] - $b['count'];
        });

        rsort($recordsArr);

        return response()->json(["code" => 200, "data" => $recordsArr]);
    }
    //END - DEVTASK-20123

    public function getHistory(Request $request, $id){

        $log_module = MagentoLogHistory::join('users','users.id','magento_log_history.user_id')->where('log_id', $id)->select('magento_log_history.*','users.name')->get();

        if($log_module) {
            return $log_module;
        }
        return 'error';
    }
    public function changeStatus(Request $request, $id){

        $log = ProductPushErrorLog::where('id', $id)->first();
        $logged_user = $request->user();

        if($log){
            $old_value = $log->response_status;
            $log->response_status = $request->type;
            $log->save();

            MagentoLogHistory::create([
                'log_id' => $log->id,
                'user_id' => $logged_user->id,
                'old_value' => $old_value,
                'new_value' => $request->type,
            ]);
        }
        return response()->json(true);

    }
}
