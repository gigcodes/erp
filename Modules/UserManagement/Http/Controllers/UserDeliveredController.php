<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\DeveloperTask;
use App\Task;
use App\TaskStatus;

use App\UserAvaibility;
// use App\Hubstaff\HubstaffActivity;
// use App\Hubstaff\HubstaffPaymentAccount;

class UserDeliveredController extends Controller {

    public function index() {
        return view('usermanagement::user-delivered.index', [
            'title' => "User Delivered",
            'table' => "listUserDelivered",
            'urlLoadData' => route('user-management.user-delivered.load-data'),
            'listUsers' => User::dropdown([
                'is_active' => 1
            ]),
        ]);
    }

    public function loadData() {
        try {
            $count = 0;
            $data = [];

            $isPrint = !request()->ajax();

            $stDate = request('srchDateFrom');
            $enDate = request('srchDateTo');
            if ($stDate && $enDate) {
                $filterDates = dateRangeArr($stDate, $enDate);
                $filterDatesOnly = array_column($filterDates, 'date');

                // if ($isPrint) {
                //     _p($filterDatesOnly);
                //     _p($filterDates);
                // }

                $q = UserAvaibility::from('user_avaibilities as ua');
                $q->leftJoin('users as u', 'ua.user_id', '=', 'u.id');
                if (!isAdmin()) {
                    $q->where('u.id', loginId());
                }
                if ($srch = request('srchUser')) {
                    $q->where('u.id', $srch);
                }
                $q->where(function ($query) use ($stDate, $enDate) {
                    $query->whereRaw(" ('" . $stDate . "' BETWEEN ua.from AND ua.to) ")
                        ->orWhereRaw(" ('" . $enDate . "' BETWEEN ua.from AND ua.to) ");
                    // $query->where('ua.from', '<=', $stDate)->orWhere('ua.to', '<=', $enDate);
                });
                // if (request('is_active')) {
                //     $q->where('users.is_active', request('is_active') == 1 ? 1 : 0);
                // }
                $q->select([
                    'u.id',
                    'u.name',
                    \DB::raw('ua.date AS uaDays'),
                    \DB::raw('ua.from AS uaFrom'),
                    \DB::raw('ua.to AS uaTo'),
                    \DB::raw('ua.start_time AS uaStTime'),
                    \DB::raw('ua.end_time AS uaEnTime'),
                    \DB::raw('ua.lunch_time AS uaLunchTime'),
                ]);
                $q->orderBy('ua.id', 'DESC');
                $userAvaibilities = $q->get();

                $count = $userAvaibilities->count();

                // if ($isPrint) {
                //     _p($userAvaibilities->toArray());
                // }

                if ($count) {
                    $userIds = [];

                    // Prepare user's data
                    $userArr = [];
                    foreach ($userAvaibilities as $single) {
                        $userIds[] = $single->id;

                        $single->uaStTime = date('H:i:00', strtotime($single->uaStTime));
                        $single->uaEnTime = date('H:i:00', strtotime($single->uaEnTime));
                        $single->uaLunchTime = date('H:i:00', strtotime($single->uaLunchTime));

                        $single->uaDays = UserAvaibility::getAvailableDays($single->uaDays);
                        $availableDates = UserAvaibility::getAvailableDates($single->uaFrom, $single->uaTo, $single->uaDays, $filterDatesOnly);
                        $availableSlots = UserAvaibility::dateWiseHourlySlots($availableDates, $single->uaStTime, $single->uaEnTime, $single->uaLunchTime);

                        foreach ($availableSlots as $date => $slots) {
                            if (!$slots || isset($userArr[$single->id]['dates'][$date])) {
                                continue;
                            }
                            $userArr[$single->id]['id'] = $single->id;
                            $userArr[$single->id]['name'] = $single->name;
                            $userArr[$single->id]['lunch'] = substr($single->uaLunchTime, 0, 5);
                            $userArr[$single->id]['dates'][$date] = [
                                'stTime' => $slots[0]['st'],
                                'enTime' => $slots[count($slots) - 1]['en'],
                            ];
                        }
                    }


                    // Get Tasks & Developer Tasks -- Arrange with End time & Mins
                    if ($userIds) {
                        $tasks = $this->getTaskList([
                            'userIds' => $userIds,
                            'stDate' => $stDate,
                            'enDate' => $enDate,
                        ]);

                        if ($tasks) {
                            foreach ($tasks as $task) {
                                $stDate = date('Y-m-d', strtotime($task->st_date));
                                $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date . ' + ' . $task->est_minutes . 'minutes'));

                                if (!isset($userArr[$task->assigned_to]['dates'][$stDate])) {
                                    continue;
                                }

                                if (!isset($userArr[$task->assigned_to]['dates'][$stDate]['planned_tasks'])) {
                                    $userArr[$task->assigned_to]['dates'][$stDate]['planned_tasks'] = [];
                                }
                                $userArr[$task->assigned_to]['dates'][$stDate]['planned_tasks'][] = [
                                    'id' => $task->id,
                                    'typeId' => $task->type . '-' . $task->id,
                                    'subject' => $task->title,
                                    'stDate' => $task->st_date,
                                    'enDate' => $task->en_date,
                                    'status' => $task->status,
                                    'status2' => TaskStatus::printName($task->status),
                                    'mins' => $task->est_minutes,
                                    'stTime' => date('H:i', strtotime($task->st_date)),
                                    'enTime' => date('H:i', strtotime($task->en_date)),
                                ];
                            }
                        }
                    }

                    if ($isPrint) {
                        _p($userArr);
                    }

                    // Arange for datatable
                    foreach ($userArr as $user) {
                        foreach ($user['dates'] as $date => $dateRow) {
                            $planned_tasks = $dateRow['planned_tasks'] ?? [];
                            if ($planned_tasks) {
                                $temp = [];
                                foreach ($planned_tasks as $task) {
                                    $temp[] = '<div class="div-slot" title="' . $task['subject'] . ' (' . $task['status2'] . ')" >' . $task['typeId'] . ' (' . $task['stTime'] . ' - ' . $task['enTime'] . ')' . '</div>';
                                }
                                $planned_tasks = $temp;
                            }

                            $data[] = [
                                'name' => $user['name'],
                                'date' => $date,
                                'availability' => date('H:i', strtotime($dateRow['stTime'])) . ' - ' . date('H:i', strtotime($dateRow['enTime'])),
                                'lunch' => ($user['lunch'] ?: '-'),
                                'planned' => $planned_tasks ? implode('', $planned_tasks) : '-',
                                'actual' => '-',
                            ];
                        }
                    }
                }

                return respJson(200, '', [
                    'draw' => request('draw'),
                    'recordsTotal' => $count,
                    'recordsFiltered' => $count,
                    'data' => $data
                ]);
            } else {
                return respJson(400, 'From and To Date is required.');
            }
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    function getTaskList($wh = []) {
        $userIds = $wh['userIds'] ?? [0];
        $stDate = $wh['stDate'] ?? NULL;
        $enDate = $wh['enDate'] ?? NULL;

        $stDate = $stDate . ' 00:00:00';
        $enDate = $enDate . ' 23:59:59';

        $sql = "SELECT
            listdata.*
            FROM (
            (
                SELECT 
                    id, 
                    'T' AS type, 
                    assign_to AS assigned_to, 
                    task_subject AS title, 
                    start_date AS st_date, 
                    COALESCE(approximate, 0) AS est_minutes,
                    status
                FROM 
                    tasks 
                WHERE 
                1
                AND start_date IS NOT NULL
                AND start_date BETWEEN ? AND ?
                AND deleted_at IS NULL
                AND assign_to IN (" . implode(',', $userIds) . ") 
            )
            UNION
            (
                SELECT 
                    id, 
                    'DT' AS type, 
                    assigned_to AS assigned_to, 
                    subject AS title, 
                    start_date AS st_date, 
                    COALESCE(estimate_minutes, 0) AS est_minutes,
                    status
                FROM developer_tasks
                WHERE 1
                AND start_date IS NOT NULL
                AND start_date BETWEEN ? AND ?
                AND deleted_at IS NULL
                AND assigned_to IN (" . implode(',', $userIds) . ")
            )
        ) AS listdata
        ORDER BY listdata.st_date ASC";

        $tasks = \DB::select($sql, [
            $stDate,
            $enDate,
            $stDate,
            $enDate,
        ]);

        return $tasks;
    }
}
