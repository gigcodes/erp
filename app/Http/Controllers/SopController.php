<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\ChatMessagesController;
use App\PurchaseProductOrderLog;
use App\PurchaseProductOrder;
use Illuminate\Support\Facades\DB;
use App\Sop;
use App\User;
use App\ChatMessage;
use Cache;
use Auth;
use App\Mail\viewdownload;
// use App\Mail\downloadData;
use App\AutoCompleteMessage;
use Dompdf\Dompdf;
use App\SopPermission;



class SopController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $usersop = Sop::with(['purchaseProductOrderLogs', 'user']);
 
        if ($request->search) {
            $usersop = $usersop->where('name', 'like', '%'.$request->search.'%')->orWhere('content', 'like', '%'.$request->search.'%');
        }

        $usersop = $usersop->latest()->paginate(10);

        $total_record = $usersop->total();
       
        return view('products.sop', compact('usersop', 'total_record','users'));
    }

    public function sopnamedata_logs(Request $request)
    {
        $log_data = PurchaseProductOrderLog::with(['updated_by', 'sop', 'sop.user'])->where('purchase_product_order_id', $request->id)
       ->where('header_name', $request->header_name)
       ->orderByDesc('id')
       ->get();

        return response()->json(['log_data' => $log_data ,'code' => 200]);
    }

    public function delete($id)
    {
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
            $sop->user_id = \Auth::id();
            $sop->save();
          
            $params['purchase_product_order_id'] = $sop->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = '-';
            $params['replace_to'] = $request->get('name');
            $params['created_by'] = \Auth::id();

            $log = PurchaseProductOrderLog::create($params);
        }
        
        $user_email = User::select('email')->where('id', $sop->user_id)->get();
        // $user_email = User::select('email')->where('id', $sop->user_id)->get();
        $only_date = $sop->created_at->todatestring();

        return response()->json(['only_date' => $only_date,'sop' => $sop, 'user_email' => $user_email, 'params' => $params]);
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

    public function search(Request $request)
    {
        $searchsop = $request->get('search');
        $usersop = DB::table('sops')->where('name', 'like', '%'.$searchsop.'%')->paginate(10);

        return view('products.sop', compact('usersop'));
    }

    function downloaddata($id){
        $usersop = Sop::where("id", $id)->first();
        if ($usersop) {
            $data["name"]      = $usersop->name;
            $data["content"]       = $usersop->content;
           
            $html = view('maileclipse::templates.Viewdownload', [
                'name'   => $usersop->name,
                'content'     => $usersop->content,
                'usersop' =>        $usersop = Sop::where("id", $usersop->id)->first()
    
              
            ]);
            
            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream(date('Y-m-d H:i:s') . 'SOPData.pdf');
           
        }
       
    }

   public function sopPermissionData(Request $request)
   {
       $user_id = $request->user_id;
       $permission = SopPermission::where("user_id",$user_id)->get();
       return response()->json(['status' => true, 'permissions' => $permission]);
   }

   public function sopPermissionList(Request $request)
   {
       $user_id = $request->user_id;
       $sop = $request->sop;

       $permission = SopPermission::where("user_id",$user_id);
       if ($permission->count() > 0) {
           $permission->delete();
       }
       foreach ($sop as $sp){
            $sopPermission = new SopPermission;
            $sopPermission->user_id = $user_id;
            $sopPermission->sop_id = $sp;
            $sopPermission->save();
       }
       return response()->json(['status' => true, 'message' => "Permission Saved successfully"]);
   }
       
}
