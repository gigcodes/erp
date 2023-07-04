<?php

namespace App\Http\Controllers;

use App\ConfigRefactor;
use App\ConfigRefactorRemarkHistory;
use App\ConfigRefactorSection;
use App\ConfigRefactorStatus;
use App\ConfigRefactorStatusHistory;
use App\ConfigRefactorUserHistory;
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

    public function getRemarks($config_refactor, $column_name)
    {
        $remarks = ConfigRefactorRemarkHistory::with(['user'])
            ->where('config_refactor_id', $config_refactor)
            ->where('column_name', $column_name)
            ->latest()
            ->get();

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
            $configRefactor->{$request->column} = $request->remark;
            $configRefactor->save();

            $history = new ConfigRefactorRemarkHistory();
            $history->config_refactor_id = $configRefactor->id;
            $history->column_name = $request->column;
            $history->remarks = $request->remark;
            $history->user_id = Auth::user()->id;
            $history->save();

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
                    'message' => 'Remark not updated.',
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

            $history = new ConfigRefactorStatusHistory();
            $history->config_refactor_id = $configRefactor->id;
            $history->column_name = $request->column;
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

    public function getStatuses($config_refactor, $column_name)
    {
        $statuses = ConfigRefactorStatusHistory::with(['user', 'newStatus', 'oldStatus'])
            ->where('config_refactor_id', $config_refactor)
            ->where('column_name', $column_name)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $statuses,
            'message' => 'Status get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateUser(Request $request)
    {
        try {
            $configRefactor = ConfigRefactor::findOrFail($request->id);
            $old_user_id = $configRefactor->user_id;
            $configRefactor->user_id = $request->user_id;
            $configRefactor->save();

            $history = new ConfigRefactorUserHistory();
            $history->config_refactor_id = $configRefactor->id;
            $history->old_user = $old_user_id;
            $history->new_user = $request->user_id;
            $history->user_id = Auth::user()->id;
            $history->save();

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

    public function getUsers($config_refactor)
    {
        $users = ConfigRefactorUserHistory::with(['user', 'newUser', 'oldUser'])
            ->where('config_refactor_id', $config_refactor)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $users,
            'message' => 'User get successfully',
            'status_name' => 'success',
        ], 200);
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
