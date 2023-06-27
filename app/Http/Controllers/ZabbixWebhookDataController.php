<?php

namespace App\Http\Controllers;

use App\Models\ZabbixWebhookData;
use App\ZabbixStatus;
use App\ZabbixWebhookDataRemarkHistory;
use App\ZabbixWebhookDataStatusHistory;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ZabbixWebhookDataController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $eventStart = $request->get('event_start');

        $zabbixWebhookDatas = ZabbixWebhookData::latest();

        if (! empty($keyword)) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->where(function ($q) use ($keyword) {
                $q->orWhere('subject', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('message', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('event_name', 'LIKE', '%' . $keyword . '%');
            });
        }

        if ($eventStart) {
            $zabbixWebhookDatas = $zabbixWebhookDatas->whereDate('event_start', $eventStart);
        }

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate(10);

        $zabbixStatuses = ZabbixStatus::pluck("name", "id")->toArray();

        return view('zabbix-webhook-data.index', compact('zabbixWebhookDatas', 'zabbixStatuses'));
    }

    public function storeZabbixStatus(Request $request)
    {
        $input = $request->except(['_token']);

        $data = ZabbixStatus::create($input);

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

    public function getRemarks($zabbix_webhook_data)
    {
        $remarks = ZabbixWebhookDataRemarkHistory::with(['user'])->where('zabbix_webhook_data_id', $zabbix_webhook_data)->get();

        return response()->json([
            'status' => true,
            'data' => $remarks,
            'message' => 'Remark get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function storeRemark(Request $request)
    {
        $input = $request->except(['_token']);
        $input['user_id'] = Auth::user()->id;

        $zabbixWebhookDataRemarkHistory = ZabbixWebhookDataRemarkHistory::create($input);

        if ($zabbixWebhookDataRemarkHistory) {
            $update = ZabbixWebhookData::where('id', $request->zabbix_webhook_data_id)->update(['remarks' => $request->remarks]);
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

    public function updateStatus(Request $request)
    {
        try {
            $zaabbixWebhookData = ZabbixWebhookData::findOrFail($request->zabbix_webhook_data);
            $old_status = $zaabbixWebhookData->zabbix_status_id;

            $zaabbixWebhookData->zabbix_status_id = $request->status;

            $zaabbixWebhookData->save();

            $history = new ZabbixWebhookDataStatusHistory();
            $history->zabbix_webhook_data_id = $zaabbixWebhookData->id;
            $history->old_status_id = $old_status;
            $history->new_status_id = $request->status;
            $history->user_id = Auth::user()->id;
            $history->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Status updated successfully',
                ], 200
            );
        } catch(Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Status not updated.',
                ], 500
            );
        }
    }

    public function issuesSummary(Request $request)
    {
        $perPage = 10; // Number of records per page

        $zabbixWebhookDatas = ZabbixWebhookData::latest();
        $zabbixWebhookDatas = $zabbixWebhookDatas->where('severity', 'high');

        $zabbixWebhookDatas = $zabbixWebhookDatas->paginate($perPage);

        return response()->json($zabbixWebhookDatas);
    }
}
