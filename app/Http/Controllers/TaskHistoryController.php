<?php

namespace App\Http\Controllers;

use App\Task;
use App\Models\Tasks\TaskHistoryForStartDate;
use App\Models\Tasks\TaskHistoryForCost;
use App\TaskDueDateHistoryLog;

class TaskHistoryController extends Controller {

    public function __construct() {
    }

    public function historyStartDate() {
        $list = TaskHistoryForStartDate::with('updatedBy')->where('task_id', request('id'))->orderBy('id', 'DESC')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="5%">#</th>
                <th width="5%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td><input type="radio" name="radio_for_approve" value="' . $single->id . '" /></td>
                    <td>' . $single->id . '</td>
                    <td>' . ($single->updatedBy ? $single->updatedBy->name : '-') . '</td>
                    <td>' . $single->old_value . '</td>
                    <td>' . $single->new_value . '</td>
                    <td>' . $single->created_at . '</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="6">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';
        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function historyDueDate() {
        $list = TaskDueDateHistoryLog::with(['users'])->where('task_id', request('id'))->orderBy('id', 'DESC')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>' . $single->id . '</td>
                    <td>' . ($single->users ? $single->users->name : '-') . '</td>
                    <td>' . $single->old_due_date . '</td>
                    <td>' . $single->new_due_date . '</td>
                    <td>' . $single->created_at . '</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';
        return respJson(200, '', ['data' => implode('', $html)]);
    }

    public function historyCost() {
        $list = TaskHistoryForCost::with('updatedBy')->where('task_id', request('id'))->orderBy('id', 'DESC')->get();

        $html = [];
        $html[] = '<table class="table table-bordered">';
        $html[] = '<thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Update By</th>
                <th width="20%" style="word-break: break-all;">Old Value</th>
                <th width="20%" style="word-break: break-all;">New Value</th>
                <th width="20%">Created at</th>
            </tr>
        </thead>';
        if ($list->count()) {
            foreach ($list as $single) {
                $html[] = '<tr>
                    <td>' . $single->id . '</td>
                    <td>' . ($single->updatedBy ? $single->updatedBy->name : '-') . '</td>
                    <td>' . $single->old_value . '</td>
                    <td>' . $single->new_value . '</td>
                    <td>' . $single->created_at . '</td>
                </tr>';
            }
        } else {
            $html[] = '<tr>
                <td colspan="5">No records found.</td>
            </tr>';
        }
        $html[] = '</table>';
        return respJson(200, '', ['data' => implode('', $html)]);
    }
}
