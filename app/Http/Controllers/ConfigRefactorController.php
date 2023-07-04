<?php

namespace App\Http\Controllers;

use App\ConfigRefactor;
use App\ConfigRefactorSection;
use App\ConfigRefactorStatus;
use App\Models\ZabbixWebhookData;
use App\User;
use App\ZabbixStatus;
use App\ZabbixWebhookDataRemarkHistory;
use App\ZabbixWebhookDataStatusHistory;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ConfigRefactorController extends Controller
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
        // $keyword = $request->get('keyword');
        // $eventStart = $request->get('event_start');

        $configRefactors = ConfigRefactor::with('configRefactorSection')
            ->join('config_refactor_sections', 'config_refactor_sections.id', 'config_refactors.config_refactor_section_id');

        // if (! empty($keyword)) {
        //     $zabbixWebhookDatas = $zabbixWebhookDatas->where(function ($q) use ($keyword) {
        //         $q->orWhere('subject', 'LIKE', '%' . $keyword . '%')
        //             ->orWhere('message', 'LIKE', '%' . $keyword . '%')
        //             ->orWhere('event_name', 'LIKE', '%' . $keyword . '%');
        //     });
        // }

        // if ($eventStart) {
        //     $zabbixWebhookDatas = $zabbixWebhookDatas->whereDate('event_start', $eventStart);
        // }

        $configRefactors = $configRefactors->paginate(10);

        $configRefactorStatuses = ConfigRefactorStatus::pluck("name", "id")->toArray();
        $users = User::select('name', 'id')->role('Developer')->orderby('name', 'asc')->where('is_active', 1)->get();
        $users = $users->pluck('name', 'id');

        return view('config-refactor.index', compact('configRefactors', 'configRefactorStatuses', 'users'));
    }

    public function storeStatus(Request $request)
    {
        $input = $request->except(['_token']);

        $data = ConfigRefactorStatus::create($input);

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
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_remark = $configRefactor->{$request->column};

            $configRefactor->{$request->column} = $request->remark;

            $configRefactor->save();

            // $history = new ZabbixWebhookDataStatusHistory();
            // $history->zabbix_webhook_data_id = $zaabbixWebhookData->id;
            // $history->old_status_id = $old_status;
            // $history->new_status_id = $request->status;
            // $history->user_id = Auth::user()->id;
            // $history->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Remark updated successfully',
                ], 200
            );
        } catch(Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Status not updated.',
                ], 500
            );
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_status = $configRefactor->{$request->column};

            $configRefactor->{$request->column} = $request->status;

            $configRefactor->save();

            // $history = new ZabbixWebhookDataStatusHistory();
            // $history->zabbix_webhook_data_id = $zaabbixWebhookData->id;
            // $history->old_status_id = $old_status;
            // $history->new_status_id = $request->status;
            // $history->user_id = Auth::user()->id;
            // $history->save();

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

    public function updateUser(Request $request)
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_user_id = $configRefactor->user_id;

            $configRefactor->user_id = $request->user_id;

            $configRefactor->save();

            // $history = new ZabbixWebhookDataStatusHistory();
            // $history->zabbix_webhook_data_id = $zaabbixWebhookData->id;
            // $history->old_status_id = $old_status;
            // $history->new_status_id = $request->status;
            // $history->user_id = Auth::user()->id;
            // $history->save();

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'User updated successfully',
                ], 200
            );
        } catch(Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not updated.',
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
