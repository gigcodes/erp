<?php

namespace App\Http\Controllers;

use Auth;
use App\Task;
use App\User;
use Illuminate\Http\Request;
use App\Models\ZabbixWebhookData;
use App\Models\ZabbixTaskAssigneeHistory;

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
                'task_name'               => 'required',
                'zabbix_webhook_data_ids' => 'required',
                'assign_to'               => 'required',
            ]
        );

        $data = $request->except('_token');

        // Create task directly in tasks table instead of zabbix_tasks(this is not need now) table.
        $task = Task::where('task_subject', $data['task_name'])->where('assign_to', $data['assign_to'])->first();
        if (! $task) {
            $data['assign_from']  = Auth::id();
            $data['is_statutory'] = 0;
            $data['task_details'] = $data['task_name'];
            $data['task_subject'] = $data['task_name'];
            $data['assign_to']    = $data['assign_to'];

            $task = Task::create($data);

            if ($data['assign_to']) {
                $task->users()->attach([$data['assign_to'] => ['type' => User::class]]);
            }
        }

        // Assign Zabbix Task Id to selected zabbix webhook datas
        $zabbixWebhookDatas = ZabbixWebhookData::whereIn('id', $data['zabbix_webhook_data_ids']);
        $zabbixWebhookDatas->update(['zabbix_task_id' => $task->id]);

        return response()->json(
            [
                'code'    => 200,
                'data'    => [],
                'message' => 'Zabbix task has been created!',
            ]
        );
    }

    public function getAssigneeHistories($zabbix_task)
    {
        $assigneeHistories = ZabbixTaskAssigneeHistory::with(['user', 'newAssignee'])->where('zabbix_task_id', $zabbix_task)->latest()->get();

        return response()->json([
            'status'      => true,
            'data'        => $assigneeHistories,
            'message'     => 'Assignee Histories get successfully',
            'status_name' => 'success',
        ], 200);
    }
}
