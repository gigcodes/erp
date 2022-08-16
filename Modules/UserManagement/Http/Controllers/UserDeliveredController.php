<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\DeveloperTask;
use App\Task;
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

                if ($isPrint) {
                    _p($filterDatesOnly);
                    _p($filterDates);
                }

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
                    // $query->where('ua.from', '<=', $stDate)
                    //     ->orWhere('ua.to', '<=', $enDate);
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

                if ($isPrint) {
                    _p($userAvaibilities->toArray());
                }

                if ($count) {
                    $userIds = [];

                    // Prepare user's data
                    $userArr = [];
                    foreach ($userAvaibilities as $single) {
                        $userIds[] = $single->id;

                        // $single->from = date('H:i:00', strtotime($single->from));
                        // $single->to = date('H:i:00', strtotime($single->uaStTime));

                        $single->uaStTime = date('H:i:00', strtotime($single->uaStTime));
                        $single->uaEnTime = date('H:i:00', strtotime($single->uaEnTime));
                        $single->uaLunchTime = date('H:i:00', strtotime($single->uaLunchTime));

                        $single->uaDays = $single->uaDays ? explode(',', str_replace(' ', '', $single->uaDays)) : [];
                        $availableDates = UserAvaibility::getAvailableDates($single->uaFrom, $single->uaTo, $single->uaDays, $filterDatesOnly);
                        $availableSlots = UserAvaibility::dateWiseHourlySlots($availableDates, $single->uaStTime, $single->uaEnTime, $single->uaLunchTime);

                        $userArr[] = [
                            'id' => $single->id,
                            'name' => $single->name,
                            'uaLunchTime' => substr($single->uaLunchTime, 0, 5),
                            'uaDays' => $single->uaDays,
                            'availableDays' => $single->uaDays,
                            'availableDates' => $availableDates,
                            'availableSlots' => $availableSlots,
                        ];
                    }

                    if ($isPrint) {
                        _p($userArr);
                    }

                    // Get Tasks & Developer Tasks -- Arrange with End time & Mins
                    $tasksArr = [];
                    if (0 && $userIds) {
                        $tasksInProgress = $this->typeWiseTasks('IN_PROGRESS', [
                            'userIds' => $userIds
                        ]);
                        $tasksPlanned = $this->typeWiseTasks('PLANNED', [
                            'userIds' => $userIds
                        ]);

                        if ($tasksInProgress) {
                            foreach ($tasksInProgress as $task) {
                                $task->st_date = date('Y-m-d H:i:00', strtotime($task->st_date));
                                $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date . ' + ' . $task->est_minutes . 'minutes'));
                                if ($task->en_date <= date('Y-m-d H:i:s')) {
                                    $task->en_date = date('Y-m-d H:i:00', strtotime('+1 hour'));
                                    $task->est_minutes = 60;
                                } else {
                                    // $task->est_minutes = ceil((strtotime($task->en_date) - $task->st_date) / 60);
                                }

                                $tasksArr[$task->assigned_to][$task->status2][] = [
                                    'id' => $task->id,
                                    'typeId' => $task->type . '-' . $task->id,
                                    'stDate' => $task->st_date,
                                    'enDate' => $task->en_date,
                                    'status' => $task->status,
                                    'status2' => $task->status2,
                                    'mins' => $task->est_minutes,
                                ];
                            }
                        }
                        if ($tasksPlanned) {
                            foreach ($tasksPlanned as $task) {
                                $task->est_minutes = 20;
                                $task->st_date = $task->st_date ?: date('Y-m-d H:i:00');
                                $task->en_date = date('Y-m-d H:i:00', strtotime($task->st_date . ' + ' . $task->est_minutes . 'minutes'));
                                $tasksArr[$task->assigned_to][$task->status2][] = [
                                    'id' => $task->id,
                                    'typeId' => $task->type . '-' . $task->id,
                                    'stDate' => $task->st_date,
                                    'enDate' => $task->en_date,
                                    'status' => $task->status,
                                    'status2' => $task->status2,
                                    'mins' => $task->est_minutes,
                                ];
                            }
                        }
                    }
                    if ($isPrint) {
                        _p($tasksArr);
                    }

                    // Arrange tasks on users slots
                    foreach ($userArr as $k1 => $user) {
                        $userTasks = isset($tasksArr[$user['id']]) && count($tasksArr[$user['id']]) ? $tasksArr[$user['id']] : [];
                        if ($user['uaId'] && isset($user['availableSlots']) && count($user['availableSlots'])) {
                            foreach ($user['availableSlots'] as $date => $slots) {
                                foreach ($slots as $k2 => $slot) {
                                    if ($slot['type'] == 'AVL') {
                                        $res = $this->slotIncreaseAndShift($slot, $userTasks);

                                        $userTasks = $res['userTasks'] ?? [];
                                        $slot['taskIds'] = $res['taskIds'] ?? [];
                                        $slot['userTasks'] = $res['userTasks'] ?? [];
                                    }
                                    // else if ($slotRow['type'] == 'LUNCH') {
                                    //     // $userTasks = $this->slotIncreaseAndShift($userTasks, $slotKey);
                                    // }
                                    $slots[$k2] = $slot;
                                }

                                $user['availableSlots'][$date] = $slots;
                            }
                        }
                        $userArr[$k1] = $user;
                    }



                    // Arange for datatable
                    foreach ($userArr as $user) {
                        if ($user['uaId'] && isset($user['availableSlots']) && count($user['availableSlots'])) {
                            foreach ($user['availableSlots'] as $date => $slots) {
                                $divSlots = [];
                                foreach ($slots as $slot) {
                                    $title = '';
                                    $class = '';
                                    $display = [
                                        date('H:i', strtotime($slot['st'])),
                                        ' - ',
                                        date('H:i', strtotime($slot['en'])),
                                    ];
                                    if ($slot['type'] == 'AVL') {
                                        if ($slot['taskIds'] ?? []) {
                                            $display[] = ' (' . implode(', ', array_keys($slot['taskIds'])) . ')';

                                            $title = [];
                                            foreach ($slot['taskIds'] as $taskId => $taskRow) {
                                                $title[] = $taskId . ' - (' . $taskRow['status2'] . ')';
                                            }
                                            $title = implode(PHP_EOL, $title);
                                        } else {
                                            $class = 'text-secondary';
                                            $display[] = ' <a href="javascript:void(0);" data-user_id="' . $user['id'] . '" data-date="' . $date . '" data-slot="' . date('H:i', strtotime($slot['st'])) . '" onclick="funSlotAssignModal(this);" >(AVL)</a>';
                                        }
                                        // $title
                                        $display = implode('', $display);
                                    } else if (in_array($slot['type'], ['PAST', 'LUNCH'])) {
                                        $title = 'Not Available';
                                        $class = 'text-secondary';
                                        $display[] = ' (' . $slot['type'] . ')';
                                        $display = '<s>' . implode('', $display) . '</s>';
                                    }
                                    $divSlots[] = '<div class="div-slot ' . $class . '" title="' . $title . '" >' . $display . '</div>';
                                }

                                $data[] = [
                                    'name' => $user['name'],
                                    'date' => $date,
                                    'planned' => 'Availability is not set for this user.',
                                    'actual' => 'Availability is not set for this user.',
                                ];
                            }
                        } else {
                            $data[] = [
                                'name' => $user['name'],
                                'date' => '-',
                                'planned' => 'Availability is not set for this user.',
                                'actual' => 'Availability is not set for this user.',
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

    function slotIncreaseAndShift($slot, $tasks) {
        // IN_PROGRESS, PLANNED
        $checkDates = 0;

        $taskIds = [];

        if ($tasks) {
            if ($list = ($tasks['IN_PROGRESS'] ?? [])) {
                foreach ($list as $k => $task) {
                    if ($slot['mins'] > 0 && $task['mins'] > 0) {
                        if ($task['stDate'] <= $slot['en']) { // $task['stDate'] <= $slot['st'] &&
                            $taskMins = $task['mins'];
                            $slotMins = $slot['mins'];

                            if ($taskMins >= $slotMins) {
                                $slot['mins'] = 0;
                                $task['mins'] -= $slotMins;
                                $taskIds[$task['typeId']] = $task;
                            } else {
                                $task['mins'] = 0;
                                $slot['mins'] -= $taskMins;
                                $taskIds[$task['typeId']] = $task;
                            }

                            $list[$k] = $task;
                            if ($task['mins'] <= 0) {
                                unset($list[$k]);
                            }
                            if ($slot['mins'] <= 0) {
                                break;
                            }
                        }
                    }
                }
                $list = array_values($list);
                $tasks['IN_PROGRESS'] = $list;
            }

            if ($list = ($tasks['PLANNED'] ?? [])) {
                foreach ($list as $k => $task) {
                    if ($slot['mins'] > 0 && $task['mins'] > 0) {

                        if ($task['stDate'] <= $slot['en']) { // $task['stDate'] <= $slot['st'] &&
                            $taskMins = $task['mins'];
                            $slotMins = $slot['mins'];

                            if ($taskMins >= $slotMins) {
                                $slot['mins'] = 0;
                                $task['mins'] -= $slotMins;
                                $taskIds[$task['typeId']] = $task;
                            } else {
                                $task['mins'] = 0;
                                $slot['mins'] -= $taskMins;
                                $taskIds[$task['typeId']] = $task;
                            }

                            $list[$k] = $task;
                            if ($task['mins'] <= 0) {
                                unset($list[$k]);
                            }
                            if ($slot['mins'] <= 0) {
                                break;
                            }
                        }
                    }
                }
                $list = array_values($list);
                $tasks['PLANNED'] = $list;
            }
        }

        return [
            'taskIds' => $taskIds ?? [],
            'userTasks' => $tasks ?? [],
        ];
    }

    function typeWiseTasks($type, $wh = []) {
        $userIds = $wh['userIds'] ?? [0];
        $taskStatuses = [0];
        $devTaskStatuses = ['none'];

        if ($type == 'IN_PROGRESS') {
            $taskStatuses = [
                Task::TASK_STATUS_IN_PROGRESS,
            ];
            $devTaskStatuses = [
                DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS,
            ];
        } else if ($type == 'PLANNED') {
            $taskStatuses = [
                Task::TASK_STATUS_PLANNED,
            ];
            $devTaskStatuses = [
                DeveloperTask::DEV_TASK_STATUS_PLANNED,
            ];
        }

        // start_date IS NOT NULL AND approximate > 0 
        // start_date IS NOT NULL AND estimate_minutes > 0 

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
                    status,
                    (
                        CASE
                            WHEN status = '" . Task::TASK_STATUS_IN_PROGRESS . "' THEN 'IN_PROGRESS'
                            WHEN status = '" . Task::TASK_STATUS_PLANNED . "' THEN 'PLANNED'
                        END
                    ) AS status2
                FROM 
                    tasks 
                WHERE 
                1
                AND start_date IS NOT NULL
                AND deleted_at IS NULL
                AND assign_to IN (" . implode(',', $userIds) . ") 
                AND status IN ('" . implode("','", $taskStatuses) . "') 
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
                    status,
                    (
                        CASE
                            WHEN status = '" . DeveloperTask::DEV_TASK_STATUS_IN_PROGRESS . "' THEN 'IN_PROGRESS'
                            WHEN status = '" . DeveloperTask::DEV_TASK_STATUS_PLANNED . "' THEN 'PLANNED'
                        END
                    ) AS status2
                FROM developer_tasks
                WHERE 1
                AND start_date IS NOT NULL
                AND deleted_at IS NULL
                AND assigned_to IN (" . implode(',', $userIds) . ")
                AND status IN ('" . implode("','", $devTaskStatuses) . "')
            )
        ) AS listdata
        ORDER BY listdata.st_date ASC";

        $tasks = \DB::select($sql, []);

        return $tasks;
    }
}
