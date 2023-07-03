<?php

namespace App\Http\Controllers;

use App\Models\ZabbixTask;
use App\Models\ZabbixTaskAssigneeHistory;
use App\Models\ZabbixWebhookData;
use Auth;
use Illuminate\Http\Request;

class ZabbixTaskController extends Controller
{
    public function __construct()
    {
    }

    public function store(Request $request)
    {
        // Validation Part
        $this->validate(
            $request, [
                'task_name' => 'required',
                'zabbix_webhook_data_ids' => 'required',
                'assign_to' => 'required',
            ]
        );

        $data = $request->except('_token');

        // Save Zabbix Task
        $zabbixTask = new ZabbixTask();
        $zabbixTask->task_name = $data['task_name'];
        $zabbixTask->assign_to = $data['assign_to'];
        $zabbixTask->save();

        // Assign Zabbix Task Id to selected zabbix webhook datas
        $zabbixWebhookDatas = ZabbixWebhookData::whereIn('id', $data['zabbix_webhook_data_ids']);
        $zabbixWebhookDatas->update(['zabbix_task_id'=>$zabbixTask->id]);

        // Save assignee history
        $zabbixTaskAssigneeHistory = new ZabbixTaskAssigneeHistory();
        $zabbixTaskAssigneeHistory->zabbix_task_id = $zabbixTask->id;
        $zabbixTaskAssigneeHistory->new_assignee = $data['assign_to'];
        $zabbixTaskAssigneeHistory->user_id = Auth::user()->id;
        $zabbixTaskAssigneeHistory->save();

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Zabbix task has been created!',
            ]
        );
    }

    public function getAssigneeHistories($zabbix_task)
    {
        $assigneeHistories = ZabbixTaskAssigneeHistory::with(['user', 'newAssignee'])->where('zabbix_task_id', $zabbix_task)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $assigneeHistories,
            'message' => 'Assignee Histories get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
