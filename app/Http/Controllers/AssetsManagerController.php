<?php

namespace App\Http\Controllers;

use App\AssetsManager;
use App\CashFlow;
use DB;
use App\User;
use App\AssetManamentUpdateLog;
use Illuminate\Http\Request;

class AssetsManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $archived = 0;
        if ($request->archived == 1) {
            $archived = 1;
        }

        $category = DB::table('assets_category')->get();

        $search = request("search", "");
        $paymentCycle = request("payment_cycle", "");
        $assetType = request("asset_type", "");
        $purchaseType = request("purchase_type", "");

        $assets = new AssetsManager;

        if (!empty($search)) {
            $assets = $assets->where(function ($q) use ($search) {
                $q->where("name", "LIKE", "%" . $search . "%")->orWhere("provider_name", "LIKE", "%" . $search . "%");
            });
        }

        if (!empty($paymentCycle)) {
            $assets = $assets->where("payment_cycle", $paymentCycle);
        }

        if (!empty($assetType)) {
            $assets = $assets->where("asset_type", $assetType);
        }

        if (!empty($purchaseType)) {
            $assets = $assets->where("purchase_type", $purchaseType);
        }
        // $assets = $assets->orderBy("due_date", "ASC");
        $assetsIds = $assets->select('id')->get()->toArray();
        $assets = $assets->orderBy('due_date', 'asc')->paginate(25);

        //Cash Flows
        $cashflows = \App\CashFlow::whereIn('cash_flow_able_id', $assetsIds)->where(['cash_flow_able_type' => 'App\AssetsManager'])->get();
        $users = User::all();
        return view('assets-manager.index', compact('assets', 'category', 'cashflows', 'users'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'asset_type' => 'required',
            'start_date' => 'required',
            'category_id' => 'required',
            'purchase_type' => 'required',
            'payment_cycle' => 'required',
            'amount' => 'required',
        ]);

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = DB::table('assets_category')
                ->Where('cat_name', $othercat)
                ->first();

            if (!empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = DB::table('assets_category')->insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');
        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        $data['ip_name'] = json_encode($request->ip_name);
        $data['server_update'] = json_encode($request->server_update);
        $insertData = AssetsManager::create($data);
        if ($request->input('payment_cycle') == 'One time') {
            //create entry in table cash_flows
            \App\CashFlow::create(
                [
                    'description' => 'Asset Manager Payment for ' . $insertData->name,
                    'date' => date('Y-m-d'),
                    'amount' => $request->input('amount'),
                    'type' => 'pending',
                    'currency' => $insertData->currency,
                    'cash_flow_able_type' => 'App\AssetsManager',
                    'cash_flow_able_id' => $insertData->id,

                ]
            );
        }
        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
    $assets = AssetsManager::find($id);
    $reply_categories = ReplyCategory::all();
    $users_array = Helpers::getUserArray(User::all());
    $emails = [];

    return view('assets-manager.show', [
    'assets'  => $assets,
    'reply_categories'  => $reply_categories,
    'users_array'  => $users_array,
    'emails'  => $emails
    ]);
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'asset_type' => 'required',
            'start_date' => 'required',
            'category_id' => 'required',
            'purchase_type' => 'required',
            'payment_cycle' => 'required',
            'amount' => 'required',
        ]);

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = DB::table('assets_category')
                ->Where('cat_name', $othercat)
                ->first();

            if (!empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = DB::table('assets_category')->insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');
        
        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        if($request->input('old_user_name') != $request->input('user_name') || $request->input('old_password') != $request->input('password')){
            $assetLog = new AssetManamentUpdateLog();
            $assetLog->assetmenament_id =  $id;
            $assetLog->user_id =  \Auth::user()->id;
            $assetLog->user_name =  $request->input('old_user_name');
            $assetLog->password =  $request->input('old_password');
            $assetLog->ip =  $request->input('old_ip');
            $assetLog->save();
        }
        $data['ip_name'] = json_encode($request->ip_name);
        $data['server_update'] = json_encode($request->server_update);
        //dd($data);
        AssetsManager::find($id)->update($data);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data['archived'] = 1;
        AssetsManager::find($id)->update($data);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets deleted successfully');
    }

    public function addNote($id, Request $request)
    {
        $assetmanager = AssetsManager::findOrFail($id);
        $notes = $assetmanager->notes;
        if (!is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $assetmanager->notes = $notes;
        $assetmanager->save();

        return response()->json([
            'status' => 'success',
        ]);
    }
    public function paymentHistory(request $request)
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        $paymentData = CashFlow::where('cash_flow_able_id', $asset_id)
            ->where('cash_flow_able_type', 'App\AssetsManager')
            ->where('type', 'paid')
            ->orderBy('date', 'DESC')
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td>' . $history->amount . '</td>';
                $html .= '<td>' . $history->date . '</td>';
                $html .= '<td>' . $history->description . '</td>';
                $html .= '</tr>';

                $i++;
            }
            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html, 'success' => true], 200);

    }

    public function assetManamentLog(request $request)
    {
        $asset_id = $request->input('asset_id');
        //dd($asset_id);
        $html = '';
        //\DB::enableQueryLog(); 
        $assetLogs = AssetManamentUpdateLog::select('asset_manamentupdate_logs.*', 'users.name AS userName')
            ->leftJoin('users', 'users.id', '=', 'asset_manamentupdate_logs.user_id')
            ->where('asset_manamentupdate_logs.assetmenament_id', $asset_id)
            ->orderBy('asset_manamentupdate_logs.id', 'DESC')
            ->get();
        //dd(\DB::getQueryLog());
        $i = 1;
        //dd($assetLogs);
        if (count($assetLogs) > 0) {
            foreach ($assetLogs as $assetLog) {
                $html .= '<tr>';
                $html .= '<td>' . $assetLog->id . '</td>';
                $html .= '<td>' . $assetLog->userName . '</td>';
                $html .= '<td>' . $assetLog->user_name . '</td>';
                $html .= '<td>' . $assetLog->password . '</td>';
                $html .= '<td>' . $assetLog->created_at . '</td>';
                $html .= '</tr>';
                $i++;
            }
            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">Record not found</td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html, 'success' => true], 200);

    }
}
