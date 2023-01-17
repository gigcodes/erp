<?php

namespace App\Http\Controllers;

use App\PurchaseProductOrderLog;
use App\Sop;
use App\SopPermission;
use App\SopCategory;// sop category model
use App\User;
// use App\Mail\downloadData;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SopController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        // $users = User::limit(10)->get();
        $usersop = Sop::with(['purchaseProductOrderLogs', 'user']);

        if ($request->search) {
            $usersop = $usersop->where('name', 'like', '%'.$request->search.'%')->orWhere('content', 'like', '%'.$request->search.'%');
        }

        $usersop = $usersop->limit(10)->paginate(10);

        $total_record = $usersop->total();
        $category_result = SopCategory::all();
        return view('products.sop', compact('usersop', 'total_record', 'users','category_result'));
    }

    public function sopnamedata_logs(Request $request)
    {
        $log_data = PurchaseProductOrderLog::with(['updated_by', 'sop', 'sop.user'])->where('purchase_product_order_id', $request->id)
            ->where('header_name', $request->header_name)
            ->orderByDesc('id')
            ->get();

        return response()->json(['log_data' => $log_data, 'code' => 200]);
    }

    public function delete($id)
    {
        $usersop = Sop::findOrFail($id);
        $usersop->delete();

        return response()->json([
            'message' => 'Data deleted Successfully!',
        ]);
    }

    /**
     * Sop category add in table
     *
     * @param Request $request
     * @return void
     */
    function categoryStore(Request $request){
        $category = SopCategory::where('category_name', $request->category_name)->first();
        if ($category) {
            return response()->json(['success' => false, 'message' => 'Category already existed']);
        }
        try {
            $resp = SopCategory::create(['category_name'=>$request->category_name]);
            return response()->json(['success' => true, 'message' => 'Category added successfully','data'=>$resp]);
        } catch (\exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    function categorylist(){
        $category_result = SopCategory::all();
        return response()->json(['success' => true,'data'=>$category_result, 'message' =>'Record found']);
    }

    public function store(Request $request)
    {
        $sopType = $request->get('type');
        $sop = Sop::where('name', $sopType)->first();
        
        $name = Sop::where('name', $request->get('name'))->first();

        if ($name) {
            return response()->json(['success' => false, 'message' => 'Name already existed']);
        }
        
        if (! $sop) {
            $sop = new Sop();
            $sop->name = $request->get('name');
            $sop->category = implode(',',$request->get('category'));
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
        
        $only_date = $sop->created_at->todatestring();

        return response()->json(['only_date' => $only_date, 'sop' => $sop, 'user_email' => $user_email, 'params' => $params]);
    }

    public function edit(Request $request)
    {
        $sopedit = Sop::findOrFail($request->id);

        return response()->json(['sopedit' => $sopedit]);
    }

    public function update(Request $request)
    {
        $category = $request->get('category', '');
        $name = $request->get('name', '');

        $cat = Sop::where('category', $request->get('category'))->where('id', '!=', $request->id)->first();
        if ($cat) {
            return response()->json(['success' => false, 'message' => 'Category already existed']);
        }

        $sopedit = Sop::where('id', $request->id)->where('category', $category)->first();
        if ($sopedit) {
            $sopedit->name = $request->get('name', '');
            $sopedit->category = $request->get('category', '');
            $sopedit->content = $request->get('content', '');
            $updatedSop = $sopedit->save();

            $params['purchase_product_order_id'] = $request->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = $request->get('sop_old_name', '');
            $params['replace_to'] = $request->get('name', '');
            $params['created_by'] = \Auth::id();

            $log = PurchaseProductOrderLog::create($params);

            if ($sopedit) {
                return response()->json([
                    'sopedit' => $sopedit,
                    'params' => $params,
                    'type' => 'edit',
                ]);
            }
        } else {
            $sop = new Sop();
            $sop->name = $request->get('name');
            $sop->category = $request->get('category');
            $sop->content = $request->get('content');
            $sop->user_id = \Auth::id();
            $sop->save();

            $params['purchase_product_order_id'] = $sop->id;
            $params['header_name'] = 'SOP Listing Approve Logs';
            $params['replace_from'] = '-';
            $params['replace_to'] = $request->get('name');
            $params['created_by'] = \Auth::id();

            $log = PurchaseProductOrderLog::create($params);

            $user_email = User::select('email')->where('id', $sop->user_id)->get();
            // $user_email = User::select('email')->where('id', $sop->user_id)->get();
            $only_date = $sop->created_at->todatestring();

            if ($sop) {
                return response()->json([
                    'sopedit' => $sop,
                    'params' => $params,
                    'type' => 'new',
                    'only_date' => $only_date,
                    'user_email' => $user_email,
                ]);
            }
        }
    }

    public function search(Request $request)
    {
        $searchsop = $request->get('search');
        $usersop = DB::table('sops')->where('name', 'like', '%'.$searchsop.'%')->paginate(10);

        return view('products.sop', compact('usersop'));
    }

    public function downloaddata($id)
    {
        $usersop = Sop::where('id', $id)->first();
        if ($usersop) {
            $data['name'] = $usersop->name;
            $data['content'] = $usersop->content;

            $html = view('maileclipse::templates.Viewdownload', [
                'name' => $usersop->name,
                'content' => $usersop->content,
                'usersop' => $usersop = Sop::where('id', $usersop->id)->first(),

            ]);

            $pdf = new Dompdf();
            $pdf->loadHtml($html);
            $pdf->render();
            $pdf->stream(date('Y-m-d H:i:s').'SOPData.pdf');
        }
    }

    public function sopPermissionData(Request $request)
    {
        $user_id = $request->user_id;
        $permission = SopPermission::where('user_id', $user_id)->get();

        return response()->json(['status' => true, 'permissions' => $permission]);
    }

    public function sopPermissionList(Request $request)
    {
        $user_id = $request->user_id;
        $sop = $request->sop;

        $permission = SopPermission::where('user_id', $user_id);
        if ($permission->count() > 0) {
            $permission->delete();
        }
        if ($sop) {
            foreach ($sop as $sp) {
                $sopPermission = new SopPermission;
                $sopPermission->user_id = $user_id;
                $sopPermission->sop_id = $sp;
                $sopPermission->save();
            }
        }

        return response()->json(['status' => true, 'message' => 'Permission Saved successfully']);
    }

    public function sopPermissionUserList(Request $request)
    {
        $sop = Sop::find($request->sop_id);
        $sop_users = SopPermission::where('sop_id', $request->sop_id)->get()->pluck('user_id');
        $user = User::all();

        return response()->json(['user_list' => $sop_users, 'user' => $user, 'sop' => $sop]);
    }

    public function sopRemovePermission(Request $request)
    {
        $user_id = $request->user_id;
        $sop_id = $request->sop_id;
        $sop_permission = SopPermission::where('sop_id', $sop_id)->delete();
        if ($user_id) {
            foreach ($user_id as $u_id) {
                $new_permission = new SopPermission;
                $new_permission->sop_id = $sop_id;
                $new_permission->user_id = $u_id;
                $new_permission->save();
            }
        }

        return response()->json(['message' => 'Permission Apply Successfully']);
    }
}
