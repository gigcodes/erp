<?php

namespace App\Http\Controllers;

use App\OrderStatus;
use App\PurchaseStatus;
use App\StatusMapping;
use App\StatusMappingHistory;
use Illuminate\Http\Request;
use Auth;

class StatusMappingController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['orderStatuses'] = OrderStatus::pluck('status', 'id')->all();
        $data['purchaseStatuses'] = PurchaseStatus::pluck('name', 'id')->all();

        $data['statusMappings'] = StatusMapping::latest()->get();

        return view('status-mappings.index', $data);
    }

    public function store(Request $request)
    {
        $input = $request->except(['_token']);
        
        $statusMapping = StatusMapping::where('order_status_id', $input['orderStatusId'])->first();
        
        if ($statusMapping) {
            return response()->json([
                'status' => false,
                'message' => 'Order status mapping already created',
                'status_name' => 'error',
            ], 500);
        } else {
            $statusMapping = new StatusMapping;
            $statusMapping->order_status_id = $input['orderStatusId'];
            $statusMapping->save();

            if ($statusMapping) {
                return response()->json([
                    'status' => true,
                    'message' => 'Mapping created successfully',
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
    }

    public function update(Request $request, $id) 
    {
        $input = $request->except(['_token']);

        $statusMapping = StatusMapping::where('id', $id)->first();
        
        if (!$statusMapping) {
            return response()->json([
                'status' => false,
                'message' => 'Record not exists',
                'status_name' => 'error',
            ], 500);
        } else {
            $oldStatusId = $statusMapping->purchase_status_id;
            $statusMapping->purchase_status_id = $input['purchaseStatusId'];
            $statusMapping->save();

            $lastUpdatedUser = $this->saveStatusMappingHistory($statusMapping, $oldStatusId, $input['purchaseStatusId'], "Purchase");

            if ($statusMapping) {
                return response()->json([
                    'status' => true,
                    'message' => 'Mapping updated successfully',
                    'status_name' => 'success',
                    'data' => [
                        'lastUpdatedUser' => $lastUpdatedUser
                    ]
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'something error occurred',
                    'status_name' => 'error',
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $statusMapping = StatusMapping::where('id', $id)->first();

        if ($statusMapping) {
            $oldStatusId = $statusMapping->purchase_status_id;
            $statusMapping->purchase_status_id = '';
            $statusMapping->save();

            $lastUpdatedUser = $this->saveStatusMappingHistory($statusMapping, $oldStatusId, '', "Purchase");

            return response()->json([
                'status' => true,
                'message' => 'Mapping Deleted successfully',
                'status_name' => 'success',
                'data' => [
                    'lastUpdatedUser' => $lastUpdatedUser
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Deleted unsuccessfully',
                'status_name' => 'error',
            ], 500);
        }
    }

    protected function saveStatusMappingHistory($statusMapping, $oldStatusId, $newStatusId, $statusType)
    {
        $history = new StatusMappingHistory();
        $history->status_mapping_id = $statusMapping->id;
        $history->old_status_id = $oldStatusId;
        $history->new_status_id = $newStatusId;
        $history->status_type = $statusType;
        $history->user_id = Auth::user()->id;
        $history->save();

        return $history->user->name;
    }
}
