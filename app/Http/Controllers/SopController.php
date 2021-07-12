<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\PurchaseProductOrderLog;
use App\PurchaseProductOrder;
use Illuminate\Support\Facades\DB;
use App\Sop;
use Cache;
use Auth;

class SopController extends Controller
{
    function index(Request $request){
        
        $usersop = Sop::with('purchaseProductOrderLogs');
 
   if($request->search){

       $usersop = $usersop->where('name', 'like', '%'.$request->search.'%')
       ->orWhere('content', 'like', '%'.$request->search.'%');
   }

        $usersop = $usersop->latest()->paginate(15);

        $total_record = $usersop->total();
       
       return view('products.sop', compact('usersop','total_record'));

   }

   function sopnamedata_logs(Request $request){
     
       $log_data = PurchaseProductOrderLog::where('purchase_product_order_id',$request->id)
       ->join('users','purchase_product_order_logs.created_by','users.id')
       ->where('header_name',$request->header_name);

       $log_data = $log_data->orderBy('purchase_product_order_logs.id','ASC')
       ->select('purchase_product_order_logs.*','users.*','purchase_product_order_logs.created_at as log_created_at')
       ->get();

       return response()->json(['log_data' => $log_data ,'code' => 200]);

  }

  public function delete($id){
       $usersop =Sop::findOrFail($id);
       $usersop->delete();

       return response()->json([
           'message' => 'Data deleted Successfully!'
       ]);
       
    }
   
   public function store(Request $request)
   {
      
       $sopType = $request->get('type');
       $sop = Sop::where('name', $sopType)->first();

       if (!$sop) {
           $sop = new Sop();
           $sop->name = $request->get('name');
           $sop->content = $request->get('content');
           $sop->save();
          
           $params['purchase_product_order_id'] = $sop->id;
           $params['header_name'] = 'SOP Listing Approve Logs';
           $params['replace_from'] = '-';
           $params['replace_to'] = $request->get('name');
           $params['created_by'] = \Auth::id();

           $log = PurchaseProductOrderLog::create($params);
       }
      
       $only_date = $sop->created_at->todatestring();

         return response()->json(['only_date' => $only_date,'sop' => $sop, 'params' => $params]);
     }

   public function edit(Request $request)
   {
       
       $sopedit = Sop::findOrFail($request->id);
     
      return response()->json(['sopedit' => $sopedit]);
   }
   public function update(Request $request)
   {
       $sopedit =  Sop::findOrFail($request->id);

       $sopedit->name    = $request->get("name", "");
       $sopedit->content    = $request->get("content", "");
       $updatedSop =    $sopedit->save();
         
       $params['purchase_product_order_id'] = $request->id;
       $params['header_name'] = 'SOP Listing Approve Logs';
       $params['replace_from'] = $request->get("sop_old_name", "");
       $params['replace_to'] = $request->get("name", "");
       $params['created_by'] = \Auth::id();

       $log = PurchaseProductOrderLog::create($params);
    
       if ($sopedit) {
           return response()->json([
               'sopedit' => $sopedit,
               'params' => $params
           ]);
       }
   }

   public function search(Request $request){
       
           $searchsop = $request->get('search');
           $usersop = DB::table('sops')->where('name', 'like', '%'.$searchsop.'%')->paginate(10);

       return view('products.sop', compact('usersop'));
   }
}
