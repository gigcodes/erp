<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wetransfer;
use seo2websites\ErpExcelImporter\ErpExcelImporter;
use App\Setting;

class WeTransferController extends Controller
{
    public function index()
    {
        $wetransfers = Wetransfer::orderBy('id')->paginate(Setting::get('pagination'));
        return view('wetransfer.index',['wetransfers' => $wetransfers]);
    }

    /**
     * @SWG\Get(
     *   path="/wetransfer",
     *   tags={"Wetransfer"},
     *   summary="Get wetransfer link",
     *   operationId="get-wetransfer-link",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function getLink()
    {
    	$wetransfer = Wetransfer::where('is_processed',0)->first();
    	if($wetransfer == null){
    		return json_encode(['error' => 'Nothing to process now']);
    	}
    	return json_encode($wetransfer);
    }

    /**
     * @SWG\Post(
     *   path="/wetransfer-file-store",
     *   tags={"Wetransfer"},
     *   summary="store wetransfer file",
     *   operationId="store-wetransfer-file",
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true, 
     *          type="string" 
     *      ),
     * )
     *
     */
    public function storeFile(Request $request)
    {
        $wetransfer = Wetransfer::find($request->id);
        if($request->status){
            $wetransfer->is_processed = 2;
            $wetransfer->update();
            return json_encode(['success' => 'Wetransfer Status has been updated']);
        }

        if($request->file){

            $wetransfer->is_processed = 1;
            $wetransfer->update();
            $file = $request->file('file');
            $attachments_array = [];
            if (class_exists('\\seo2websites\\ErpExcelImporter\\ErpExcelImporter')) {
                $attachments = ErpExcelImporter::excelZipProcess($file, $file->getClientOriginalName(), $wetransfer->supplier, '', $attachments_array);
                
            }

            return json_encode(['success' => 'Wetransfer has been stored']);
        }	
    }
}
