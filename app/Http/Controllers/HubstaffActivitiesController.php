<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\HubstaffTaskEfficiency;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffActivitySummary;
use App\Hubstaff\HubstaffMember;
use App\PaymentReceipt;
use App\Task;
use App\Team;
use App\User;
use App\UserRate;
use Artisan;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HubstaffActivitiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $title = "Hubstaff Activities";

        return view("hubstaff.activities.index", compact('title'));

    }

    public function notification()
    {
        $title = "Hubstaff Notification";

        return view("hubstaff.activities.notification.index", compact('title'));
    }

    public function notificationRecords(Request $request)
    {
        $records = \App\Hubstaff\HubstaffActivityNotification::join("users as u", "hubstaff_activity_notifications.user_id", "u.id");
        $keyword = request("keyword");
        if (!empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where("u.name", "LIKE", "%$keyword%");
            });
        }

        if ($request->start_date != null) {
            $records = $records->whereDate("start_date", ">=", $request->start_date . " 00:00:00");
        }

        if ($request->end_date != null) {
            $records = $records->whereDate("start_date", "<=", $request->end_date . " 23:59:59");
        }

        $records = $records->select(["hubstaff_activity_notifications.*", "u.name as user_name"])->get();
        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    public function notificationReasonSave(Request $request)
    {
        if ($request->id != null) {
            $hnotification = \App\Hubstaff\HubstaffActivityNotification::find($request->id);
            if ($hnotification != null) {
                $hnotification->reason = $request->reason;
                $hnotification->save();
                return response()->json(["code" => 200, "data" => [], "message" => "Added succesfully"]);
            }
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Requested id is not in database"]);
    }

    public function changeStatus(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(["code" => 500, "data" => [], "message" => "only admin can change status."]);
        }
        if ($request->id != null) {
            $hnotification = \App\Hubstaff\HubstaffActivityNotification::find($request->id);
            if ($hnotification != null) {
                $hnotification->status = $request->status;
                $hnotification->save();
                return response()->json(["code" => 200, "data" => [], "message" => "changed succesfully"]);
            }
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Requested id is not in database"]);
    }

    public function getActivityUsers(Request $request)
    {
        $title      = "Hubstaff Activities";
        $start_date = $request->start_date ? $request->start_date : date('Y-m-d', strtotime("-1 days"));
        $end_date   = $request->end_date ? $request->end_date : date('Y-m-d', strtotime("-1 days"));
        $user_id    = $request->user_id ? $request->user_id : null;
        $task_id    = $request->task_id ? $request->task_id : null;

        if ($task_id > 0) {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->where('hubstaff_activities.task_id', '=', $task_id)->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
        } else {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=', $start_date)->whereDate('hubstaff_activities.starts_at', '<=', $end_date);
        }

        if (Auth::user()->isAdmin()) {
            $query = $query;
            $users = User::all()->pluck('name', 'id')->toArray();
        } else {
            $members = Team::join('team_user', 'team_user.team_id', 'teams.id')->where('teams.user_id', Auth::user()->id)->distinct()->pluck('team_user.user_id');
            if (!count($members)) {
                $members = [Auth::user()->id];
            } else {
                $members[] = Auth::user()->id;
            }
            $query = $query->whereIn('hubstaff_members.user_id', $members);
            $users = User::whereIn('id', $members)->pluck('name', 'id')->toArray();
        }

        if ($request->user_id) {
            $query = $query->where('hubstaff_members.user_id', $request->user_id);
        }

        $activities = $query->select(DB::raw("
        hubstaff_activities.user_id,
        SUM(hubstaff_activities.tracked) as total_tracked,DATE(hubstaff_activities.starts_at) as date,hubstaff_members.user_id as system_user_id")
        )->groupBy('date', 'user_id')->orderBy('date', 'desc')->get();

        $activityUsers = collect([]);

        foreach ($activities as $activity) {
            $a = [];

            $efficiencyObj = HubstaffTaskEfficiency::where('user_id', $activity->user_id)->first();

            // all activities

            if (isset($efficiencyObj->id) && $efficiencyObj->id > 0) {
                $a['admin_efficiency'] = $efficiencyObj->admin_input;
                $a['user_efficiency']  = $efficiencyObj->user_input;
                $a['efficiency']       = (Auth::user()->isAdmin()) ? $efficiencyObj->admin_input : $efficiencyObj->user_input;

            } else {
                $a['admin_efficiency'] = "";
                $a['user_efficiency']  = "";

                $a['efficiency'] = "";

            }

            if ($activity->system_user_id) {
                $user = User::find($activity->system_user_id);
                if ($user) {
                    $activity->userName = $user->name;
                } else {
                    $activity->userName = '';
                }
            } else {
                $activity->userName = '';
            }

            // send hubstaff activities
            $ac            = DB::select(DB::raw("SELECT hubstaff_activities.* FROM hubstaff_activities where DATE(starts_at) = '" . $activity->date . "' and user_id = " . $activity->user_id));
            $totalApproved = 0;
            $isAllSelected = 0;
            $a['tasks']    = [];
            $lsTask        = [];
            foreach ($ac as $ar) {
                $taskSubject = '';
                if ($ar->task_id) {
                    if ($ar->is_manual) {
                        $task = DeveloperTask::where('id', $ar->task_id)->first();
                        if ($task) {
                            $taskSubject = $ar->task_id . '||#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('id', $ar->task_id)->first();
                            if ($task) {
                                $taskSubject = $ar->task_id . '||#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    } else {
                        $task = DeveloperTask::where('hubstaff_task_id', $ar->task_id)->orWhere('lead_hubstaff_task_id', $ar->task_id)->first();
                        if ($task) {
                            $taskSubject = $ar->task_id . '||#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('hubstaff_task_id', $ar->task_id)->orWhere('lead_hubstaff_task_id', $ar->task_id)->first();
                            if ($task) {
                                $taskSubject = $ar->task_id . '||#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    }
                }
                $lsTask[] = $taskSubject;
            }

            $a['tasks'] = array_unique($lsTask);

            $hubActivitySummery = HubstaffActivitySummary::where('date', $activity->date)->where('user_id', $activity->system_user_id)->orderBy('created_at', 'desc')->first();
            if ($request->status == 'approved') {
                if ($hubActivitySummery && $hubActivitySummery->final_approval == 1) {
                    if ($hubActivitySummery->forworded_person == 'admin') {
                        $status         = 'Approved by admin';
                        $totalApproved  = $hubActivitySummery->accepted;
                        $totalNotPaid   = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');
                        $forworded_to   = $hubActivitySummery->receiver;
                        $final_approval = 1;

                        $a['user_id']        = $activity->user_id;
                        $a['total_tracked']  = $activity->total_tracked;
                        $a['date']           = $activity->date;
                        $a['userName']       = $activity->userName;
                        $a['forworded_to']   = $forworded_to;
                        $a['status']         = $status;
                        $a['totalApproved']  = $totalApproved;
                        $a['totalNotPaid']   = $totalNotPaid;
                        $a['final_approval'] = $final_approval;
                        $a['note']           = $hubActivitySummery->rejection_note;
                        $activityUsers->push($a);
                    }
                }
            } else if ($request->status == 'forwarded_to_lead') {
                if ($hubActivitySummery) {
                    if ($hubActivitySummery->forworded_person == 'team_lead' && $hubActivitySummery->final_approval == 0) {
                        $status         = 'Pending for team lead approval';
                        $totalApproved  = $hubActivitySummery->accepted;
                        $totalNotPaid   = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');
                        $forworded_to   = $hubActivitySummery->receiver;
                        $final_approval = 0;

                        $a['user_id']        = $activity->user_id;
                        $a['total_tracked']  = $activity->total_tracked;
                        $a['date']           = $activity->date;
                        $a['userName']       = $activity->userName;
                        $a['forworded_to']   = $forworded_to;
                        $a['status']         = $status;
                        $a['totalApproved']  = $totalApproved;
                        $a['totalNotPaid']   = $totalNotPaid;
                        $a['final_approval'] = $final_approval;
                        $a['note']           = $hubActivitySummery->rejection_note;
                        $activityUsers->push($a);
                    }
                }
            } else if ($request->status == 'forwarded_to_admin') {
                if ($hubActivitySummery) {
                    if ($hubActivitySummery->forworded_person == 'admin' && $hubActivitySummery->final_approval == 0) {
                        $status         = 'Pending for admin approval';
                        $totalApproved  = $hubActivitySummery->accepted;
                        $totalNotPaid   = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');
                        $forworded_to   = $hubActivitySummery->receiver;
                        $final_approval = 0;

                        $a['user_id']        = $activity->user_id;
                        $a['total_tracked']  = $activity->total_tracked;
                        $a['date']           = $activity->date;
                        $a['userName']       = $activity->userName;
                        $a['forworded_to']   = $forworded_to;
                        $a['status']         = $status;
                        $a['totalApproved']  = $totalApproved;
                        $a['totalNotPaid']   = $totalNotPaid;
                        $a['final_approval'] = $final_approval;
                        $a['note']           = $hubActivitySummery->rejection_note;
                        $activityUsers->push($a);
                    }
                }
            } else if ($request->status == 'new') {
                if (!$hubActivitySummery) {
                    $status         = 'New';
                    $totalApproved  = 0;
                    $totalNotPaid   = 0;
                    $forworded_to   = Auth::user()->id;
                    $final_approval = 0;

                    $a['user_id']        = $activity->user_id;
                    $a['total_tracked']  = $activity->total_tracked;
                    $a['date']           = $activity->date;
                    $a['userName']       = $activity->userName;
                    $a['forworded_to']   = $forworded_to;
                    $a['status']         = $status;
                    $a['totalApproved']  = $totalApproved;
                    $a['totalNotPaid']   = $totalNotPaid;
                    $a['final_approval'] = $final_approval;
                    $a['note']           = '';
                    $activityUsers->push($a);
                }
            } else {
                if ($hubActivitySummery) {
                    if ($hubActivitySummery->forworded_person == 'admin') {
                        if ($hubActivitySummery->final_approval == 1) {
                            $status = 'Approved by admin';
                        } else {
                            $status = 'Pending for admin approval';
                        }
                    }
                    if ($hubActivitySummery->forworded_person == 'team_lead') {
                        $status = 'Pending for team lead approval';
                    }
                    if ($hubActivitySummery->forworded_person == 'user') {
                        $status = 'Pending for approval';
                    }

                    $totalApproved = $hubActivitySummery->accepted;
                    $totalNotPaid  = HubstaffActivity::whereDate('starts_at', $activity->date)->where('user_id', $activity->user_id)->where('status', 1)->where('paid', 0)->sum('tracked');
                    $forworded_to  = $hubActivitySummery->receiver;
                    if ($hubActivitySummery->final_approval) {
                        $final_approval = 1;
                    } else {
                        $final_approval = 0;
                    }
                    $note = $hubActivitySummery->rejection_note;
                } else {
                    $forworded_to   = Auth::user()->id;
                    $status         = 'New';
                    $totalApproved  = 0;
                    $totalNotPaid   = 0;
                    $final_approval = 0;
                    $note           = null;
                }
                $a['user_id']        = $activity->user_id;
                $a['total_tracked']  = $activity->total_tracked;
                $a['date']           = $activity->date;
                $a['userName']       = $activity->userName;
                $a['forworded_to']   = $forworded_to;
                $a['status']         = $status;
                $a['totalApproved']  = $totalApproved;
                $a['totalNotPaid']   = $totalNotPaid;
                $a['final_approval'] = $final_approval;
                $a['note']           = $note;
                $activityUsers->push($a);

            }
        }

        //dd($activityUsers);
        $status = $request->status;

        return view("hubstaff.activities.activity-users", compact('title', 'status', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id', 'task_id'));
    }

    public function getActivityDetails(Request $request)
    {

        if (!$request->user_id || !$request->date || $request->user_id == "" || $request->date == "") {
            return response()->json(['message' => '']);
        }

        $activityrecords = DB::select(DB::raw("SELECT CAST(starts_at as date) AS OnDate,  SUM(tracked) AS total_tracked, hour( starts_at ) as onHour
        FROM hubstaff_activities where DATE(starts_at) = '" . $request->date . "' and user_id = " . $request->user_id . "
        GROUP BY hour( starts_at ) , day( starts_at )"));
        // $activityrecords  = HubstaffActivity::whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id)->select('hubstaff_activities.*')->get();

        $admins = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();

        $teamLeaders = [];

        $users = User::select('name', 'id')->get();

        $hubstaff_member    = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
        $hubActivitySummery = null;
        if ($hubstaff_member) {
            $system_user_id     = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::where('date', $request->date)->where('user_id', $system_user_id)->orderBy('created_at', 'DESC')->first();
            $teamLeaders = User::join('teams', 'teams.user_id', 'users.id')->join('team_user', 'team_user.team_id', 'teams.id')->where('team_user.user_id', $system_user_id)->distinct()->select('users.name', 'users.id')->get();
        }
        $approved_ids = [0];
        if ($hubActivitySummery) {
            if ($hubActivitySummery->approved_ids) {
                $approved_ids = json_decode($hubActivitySummery->approved_ids);
            }

            if ($hubActivitySummery->final_approval) {
                if (!Auth::user()->isAdmin()) {
                    return response()->json([
                        'message' => 'Already approved',
                    ], 500);
                }
            }
        }

        foreach ($activityrecords as $record) {
            $activities = DB::select(DB::raw("SELECT hubstaff_activities.*
            FROM hubstaff_activities where DATE(starts_at) = '" . $request->date . "' and user_id = " . $request->user_id . " and hour(starts_at) = " . $record->onHour . ""));
            $totalApproved = 0;
            $isAllSelected = 0;
            foreach ($activities as $a) {
                if (in_array($a->id, $approved_ids)) {
                    $isAllSelected = $isAllSelected + 1;
                    $a->status     = 1;
                    $hubAct        = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
                        $totalApproved = $totalApproved + $a->tracked;
                    }
                    $a->totalApproved = $a->tracked;
                } else {
                    $a->status        = 0;
                    $a->totalApproved = 0;
                }
                $taskSubject = '';
                if ($a->task_id) {
                    if ($a->is_manual) {
                        $task = DeveloperTask::where('id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    } else {
                        $task = DeveloperTask::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    }

                }

                $a->taskSubject = $taskSubject;
            }
            if ($isAllSelected == count($activities)) {
                $record->sample = 1;
            } else {
                $record->sample = 0;
            }
            $record->activities    = $activities;
            $record->totalApproved = $totalApproved;
        }
        $user_id = $request->user_id;
        $isAdmin = false;
        if (Auth::user()->isAdmin()) {
            $isAdmin = true;
        }
        $isTeamLeader = false;
        $isLeader     = Team::where('user_id', Auth::user()->id)->first();
        if ($isLeader) {
            $isTeamLeader = true;
        }
        $taskOwner = false;
        if (!$isAdmin && !$isTeamLeader) {
            $taskOwner = true;
        }
        $date = $request->date;

        $member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
        return view("hubstaff.activities.activity-records", compact('activityrecords', 'user_id', 'date', 'hubActivitySummery', 'teamLeaders', 'admins', 'users', 'isAdmin', 'isTeamLeader', 'taskOwner', 'member'));
    }

    public function approveActivity(Request $request)
    {
        if (!$request->forworded_person) {
            return response()->json([
                'message' => 'Please forword someone',
            ], 500);
        }
        if ($request->forworded_person == 'admin') {
            $forword_to = $request->forword_to_admin;
        }
        if ($request->forworded_person == 'team_lead') {
            $forword_to = $request->forword_to_team_leader;
        }
        if ($request->forworded_person == 'user') {
            $forword_to = $request->forword_to_user;
        }

        $approvedArr = [];
        $rejectedArr = [];
        if ($request->activities && count($request->activities) > 0) {
            $approved = 0;
            foreach ($request->activities as $id) {
                $hubActivity = HubstaffActivity::where('id', $id)->first();
                //    $hubActivity->update(['status' => 1]);
                $approved      = $approved + $hubActivity->tracked;
                $approvedArr[] = $id;
            }
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity     = $query->select('hubstaff_members.user_id')->first();
            $user_id      = $activity->user_id;
            $rejected     = $totalTracked - $approved;
            $rejectedArr  = $query  = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id)->whereNotIn('hubstaff_activities.id', $approvedArr)->pluck('hubstaff_activities.id')->toArray();

            $approvedJson = json_encode($approvedArr);
            if (count($rejectedArr) > 0) {
                $rejectedJson = json_encode($rejectedArr);
            } else {
                $rejectedJson = null;
            }
            if (!$request->rejection_note) {
                $request->rejection_note = '';
            } else {
                $request->rejection_note = $request->previous_remarks . ' || ' . $request->rejection_note . ' ( ' . Auth::user()->name . ' ) ';
            }

            $hubActivitySummery                   = new HubstaffActivitySummary;
            $hubActivitySummery->user_id          = $user_id;
            $hubActivitySummery->date             = $request->date;
            $hubActivitySummery->tracked          = $totalTracked;
            $hubActivitySummery->accepted         = $approved;
            $hubActivitySummery->rejected         = $rejected;
            $hubActivitySummery->approved_ids     = $approvedJson;
            $hubActivitySummery->rejected_ids     = $rejectedJson;
            $hubActivitySummery->sender           = Auth::user()->id;
            $hubActivitySummery->receiver         = $forword_to;
            $hubActivitySummery->forworded_person = $request->forworded_person;
            $hubActivitySummery->rejection_note   = $request->rejection_note;
            $hubActivitySummery->save();

            // $hubActivitySummery = HubstaffActivitySummary::where('date',$request->date)->where('user_id',$user_id)->first();
            // if($hubActivitySummery) {
            //     $hubActivitySummery->tracked = $totalTracked;
            //     $hubActivitySummery->accepted = $approved;
            //     $hubActivitySummery->rejected = $rejected;
            //     $hubActivitySummery->rejection_note = $request->rejection_note;
            //     $hubActivitySummery->save();
            // }
            // else {
            //     $hubActivitySummery = new HubstaffActivitySummary;
            //     $hubActivitySummery->user_id = $user_id;
            //     $hubActivitySummery->date =  $request->date;
            //     $hubActivitySummery->tracked = $totalTracked;
            //     $hubActivitySummery->accepted = $approved;
            //     $hubActivitySummery->rejected = $rejected;
            //     $hubActivitySummery->rejection_note = $request->rejection_note;
            //     $hubActivitySummery->save();
            // }

            return response()->json([
                'totalApproved' => $approved,
            ], 200);
        }
        return response()->json([
            'message' => 'Can not update data',
        ], 500);
    }

    public function finalSubmit(Request $request)
    {
        $approvedArr = [];
        $rejectedArr = [];
        $approved    = 0;
        $member      = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();

        if (!$member) {
            return response()->json([
                'message' => 'Hubstaff member not mapped with erp',
            ], 500);
        }
        if (!$member->user_id) {
            return response()->json([
                'message' => 'Hubstaff member not mapped with erp',
            ], 500);
        }

        $rejection_note = '';
        $prev           = '';
        if ($request->previous_remarks) {
            $prev = $request->previous_remarks . ' || ';
        }

        $rejection_note = $prev . $request->rejection_note;
        if ($rejection_note != '') {
            $rejection_note = $rejection_note . ' ( ' . Auth::user()->name . ' ) ';
        }

        if ($request->activities && count($request->activities) > 0) {

            $dateWise = [];
            foreach ($request->activities as $id) {
                $hubActivity = HubstaffActivity::where('id', $id)->first();
                $hubActivity->update(['status' => 1]);
                $approved               = $approved + $hubActivity->tracked;
                $approvedArr[]          = $id;
                if($request->isTaskWise) {
                    $superDate              = date("Y-m-d", strtotime($hubActivity->starts_at));
                    $dateWise[$superDate][] = $hubActivity;
                }
            }

            // started to check date wiser
            if (!empty($dateWise)) {
                $totalApproved = 0;
                foreach ($dateWise as $dk => $dateW) {
                    if (!empty($dateW)) {
                        $approvedArr = [];
                        $approved    = 0;
                        $totalTracked    = 0;

                        $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                            ->whereDate('hubstaff_activities.starts_at', $dk)
                            ->where('hubstaff_activities.user_id', $request->user_id);

                        $totalTracked = $query->sum('tracked');
                        $activity     = $query->select('hubstaff_members.user_id')->first();
                        $user_id      = $activity->user_id;


                        $hubActivitySummery = HubstaffActivitySummary::where('user_id', $user_id)->where('date', $dk)->where('forworded_person', 'admin')->where('final_approval', 1)->first();
                        $approveIDs = [];
                        $rejectedIds = [];
                        if($hubActivitySummery) {
                            $approveIDs = json_decode($hubActivitySummery->approved_ids);
                            $rejectedIds = json_decode($hubActivitySummery->rejected_ids);
                        }

                        foreach ($dateW as $dw) {
                            if(!in_array($dw->id, $approveIDs) && !in_array($dw->id, $rejectedIds)) {
                                $dw->update(['status' => 1]);
                                $approved      = $approved + $dw->tracked;
                                $approvedArr[] = $dw->id;
                            }
                        }

                        $totalApproved += $approved;

                        $approvedJson = null;
                        if (count($approvedArr) > 0) {
                            $approvedJson = json_encode($approvedArr);
                        }

                        

                        if ($hubActivitySummery) {

                            $aprids = array_merge($approveIDs, $approvedArr);

                            $hubActivitySummery->tracked      = $totalTracked;
                            $hubActivitySummery->accepted     = $hubActivitySummery->accepted + $approved;
                            $hubActivitySummery->approved_ids = json_encode($aprids);
                            $hubActivitySummery->sender       = Auth::user()->id;
                            $hubActivitySummery->receiver     = Auth::user()->id;
                            $hubActivitySummery->rejection_note = $rejection_note.PHP_EOL.$hubActivitySummery->rejection_note;
                            $hubActivitySummery->save();
                        } else {
                            $hubActivitySummery                   = new HubstaffActivitySummary;
                            $hubActivitySummery->user_id          = $user_id;
                            $hubActivitySummery->date             = $dk;
                            $hubActivitySummery->tracked          = $totalTracked;
                            $hubActivitySummery->accepted         = $approved;
                            $hubActivitySummery->approved_ids     = $approvedJson;
                            $hubActivitySummery->sender           = Auth::user()->id;
                            $hubActivitySummery->receiver         = Auth::user()->id;
                            $hubActivitySummery->forworded_person = 'admin';
                            $hubActivitySummery->final_approval   = 1;
                            $hubActivitySummery->rejection_note = $rejection_note;
                            $hubActivitySummery->save();
                        }
                    }
                }

                return response()->json([
                    'totalApproved' => (float)$totalApproved / 60,
                ], 200);
            } else {
                $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id);

                $totalTracked = $query->sum('tracked');
                $activity     = $query->select('hubstaff_members.user_id')->first();
                $user_id      = $activity->user_id;
                $rejected     = $totalTracked;
                $rejectedArr  = $query  = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', $request->date)->where('hubstaff_activities.user_id', $request->user_id)->pluck('hubstaff_activities.id')->toArray();
            }

        } else {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->whereDate('hubstaff_activities.starts_at', $request->date)
                ->where('hubstaff_activities.user_id', $request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity     = $query->select('hubstaff_members.user_id')->first();
            $user_id      = $activity->user_id;
            $rejected     = $totalTracked;
            $rejectedArr  = $query  = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
                ->whereDate('hubstaff_activities.starts_at', $request->date)
                ->where('hubstaff_activities.user_id', $request->user_id)
                ->pluck('hubstaff_activities.id')
                ->toArray();
        }

        if (count($approvedArr) > 0) {
            $approvedJson = json_encode($approvedArr);
        } else {
            $approvedJson = null;
        }

        if (count($rejectedArr) > 0) {
            $rejectedJson = json_encode($rejectedArr);
        } else {
            $rejectedJson = null;
        }

        

        $hubActivitySummery = HubstaffActivitySummary::where('user_id', $user_id)->where('date', $request->date)->where('forworded_person', 'admin')->where('final_approval', 1)->first();

        if ($hubActivitySummery) {
            $hubActivitySummery->tracked        = $totalTracked;
            $hubActivitySummery->accepted       = $approved;
            $hubActivitySummery->rejected       = $rejected;
            $hubActivitySummery->approved_ids   = $approvedJson;
            $hubActivitySummery->rejected_ids   = $rejectedJson;
            $hubActivitySummery->sender         = Auth::user()->id;
            $hubActivitySummery->receiver       = Auth::user()->id;
            $hubActivitySummery->rejection_note = $rejection_note;
            $hubActivitySummery->save();
        } else {
            $hubActivitySummery                   = new HubstaffActivitySummary;
            $hubActivitySummery->user_id          = $user_id;
            $hubActivitySummery->date             = $request->date;
            $hubActivitySummery->tracked          = $totalTracked;
            $hubActivitySummery->accepted         = $approved;
            $hubActivitySummery->rejected         = $rejected;
            $hubActivitySummery->approved_ids     = $approvedJson;
            $hubActivitySummery->rejected_ids     = $rejectedJson;
            $hubActivitySummery->sender           = Auth::user()->id;
            $hubActivitySummery->receiver         = Auth::user()->id;
            $hubActivitySummery->forworded_person = 'admin';
            $hubActivitySummery->final_approval   = 1;
            $hubActivitySummery->rejection_note   = $rejection_note;
            $hubActivitySummery->save();
        }

        $requestData = new Request();
        $requestData->setMethod('POST');
        $min     = $approved / 60;
        $min     = number_format($min, 2);
        $message = 'Hi, your time for ' . $request->date . ' has been approved. Total approved time is ' . $min . ' minutes.';
        $requestData->request->add(['summery_id' => $hubActivitySummery->id, 'message' => $message, 'status' => 1]);
        app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'time_approval');

        return response()->json([
            'totalApproved' => $approved,
        ], 200);
        return response()->json([
            'message' => 'Can not update data',
        ], 500);
    }

    public function approvedPendingPayments(Request $request)
    {
        $title      = "Approved pending payments";
        $start_date = $request->start_date ? $request->start_date : date("Y-m-d");
        $end_date   = $request->end_date ? $request->end_date : date("Y-m-d");
        $user_id    = $request->user_id ? $request->user_id : null;
        if ($user_id) {
            $activityUsers = DB::select(DB::raw("select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0 and hubstaff_members.user_id = " . $user_id . ") as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id"));
        } else {
            $activityUsers = DB::select(DB::raw("select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0) as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id"));
        }

        foreach ($activityUsers as $activity) {
            $user              = User::find($activity->system_user_id);
            $latestRatesOnDate = UserRate::latestRatesOnDate($activity->starts_at, $user->id);
            if ($activity->total_tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                $total            = ($activity->total_tracked / 60) / 60 * $latestRatesOnDate->hourly_rate;
                $activity->amount = number_format($total, 2);
            } else {
                $activity->amount = 0;
            }
            $activity->userName = $user->name;
        }
        $users = User::all()->pluck('name', 'id')->toArray();
        return view("hubstaff.activities.approved-pending-payments", compact('title', 'activityUsers', 'start_date', 'end_date', 'users', 'user_id'));
    }

    public function submitPaymentRequest(Request $request)
    {
        $this->validate($request, [
            'amount'    => 'required',
            'user_id'   => 'required',
            'starts_at' => 'required',
        ]);

        $payment_receipt                 = new PaymentReceipt;
        $payment_receipt->date           = date('Y-m-d');
        $payment_receipt->rate_estimated = $request->amount;
        $payment_receipt->status         = 'Pending';
        $payment_receipt->user_id        = $request->user_id;
        $payment_receipt->remarks        = $request->note;
        $payment_receipt->save();

        $hubstaff_user_id = HubstaffMember::where('user_id', $request->user_id)->first()->hubstaff_user_id;

        HubstaffActivity::whereDate('starts_at', $request->starts_at)->where('user_id', $hubstaff_user_id)->where('status', 1)->where('paid', 0)->update(['paid' => 1]);
        return redirect()->back()->with('success', 'Successfully submitted');
    }

    public function submitManualRecords(Request $request)
    {
        if ($request->starts_at && $request->starts_at != '' && $request->total_time > 0 && $request->task_id > 0) {
            $member = HubstaffMember::where('user_id', Auth::user()->id)->first();
            if ($member) {
                $firstId = HubstaffActivity::orderBy('id', 'asc')->first();
                if ($firstId) {
                    $previd = $firstId->id - 1;
                } else {
                    $previd = 1;
                }
                // if($request->task_type == 'devtask') {
                //     $devtask = DeveloperTask::find($request->task_id);
                //     if($devtask) {
                //         if($request->role == 'developer') {
                //             $devtask->hubstaff_task_id = $request->task_id;
                //         }
                //         else if($request->role == 'lead') {
                //             $devtask->lead_hubstaff_task_id = $request->task_id;
                //         }
                //         else if($request->role == 'tester') {
                //             $devtask->tester_hubstaff_task_id = $request->task_id;
                //         }
                //         else {
                //             $devtask->hubstaff_task_id = $request->task_id;
                //         }
                //         $devtask->save();
                //     }
                // }

                // if($request->task_type == 'devtask') {
                //     $task = Task::find($request->task_id);
                //     if($task) {
                //         if($request->role == 'developer') {
                //             $task->hubstaff_task_id = $request->task_id;
                //         }
                //         else if($request->role == 'lead') {
                //             $task->lead_hubstaff_task_id = $request->task_id;
                //         }
                //         else if($request->role == 'tester') {
                //             $task->tester_hubstaff_task_id = $request->task_id;
                //         }
                //         else {
                //             $task->hubstaff_task_id = $request->task_id;
                //         }
                //         $task->save();
                //     }
                // }

                if (!$request->user_notes) {
                    $request->user_notes = '';
                }
                $activity             = new HubstaffActivity;
                $activity->id         = $previd;
                $activity->task_id    = $request->task_id;
                $activity->user_id    = $member->hubstaff_user_id;
                $activity->starts_at  = $request->starts_at;
                $activity->tracked    = $request->total_time * 60;
                $activity->keyboard   = 0;
                $activity->mouse      = 0;
                $activity->overall    = 0;
                $activity->status     = 0;
                $activity->is_manual  = 1;
                $activity->user_notes = $request->user_notes;
                $activity->save();
                return response()->json(["message" => 'Successful'], 200);
            }
            return response()->json(["message" => 'Hubstaff member not found'], 500);
        } else {
            return response()->json(["message" => 'Fill all the data first'], 500);
        }
    }
    public function fetchActivitiesFromHubstaff(Request $request)
    {
        if (!$request->starts_at || $request->starts_at == '') {
            return response()->json(['message' => 'Select date first'], 500);
        }
        $starts_at = $request->starts_at;
        $member    = $hubstaff_user_id    = HubstaffMember::where('user_id', Auth::user()->id)->first();
        if ($member) {
            $hubstaff_user_id = $member->hubstaff_user_id;
        } else {
            return response()->json(['message' => 'Hubstaff member not found'], 500);
        }
        try {
            $exitCode = Artisan::call('hubstaff:load_past_activities', [
                'start' => $starts_at, 'user_ids' => $hubstaff_user_id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Successful'], 200);
    }

    /*
     * process to Add Efficiency
     *
     *@params Request $request
     *@return
     */
    public function AddEfficiency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'efficiency' => 'required',
            'user_id'    => 'required',
            'type'       => 'required',
            'date'       => 'required',
            'hour'       => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->first()], 500);

        } else {
            // $requestArr = $request->all();

            // if(Auth::user()->isAdmin())
            // {
            //     $admin_input = (isset($requestArr['efficiency'])) ? $requestArr['efficiency'] : '';
            //     $user_input =  '';

            // }else
            // {
            //     $admin_input = "";
            //     $user_input = (isset($requestArr['efficiency'])) ? $requestArr['efficiency'] : '';

            // }

            // $user_id = (isset($requestArr['user_id'])) ? $requestArr['user_id'] : '';
            $admin_input = null;
            $user_input  = null;
            if ($request->type == 'admin') {
                $admin_input = $request->efficiency;
            } else {
                $user_input = $request->efficiency;
            }
            $insert_array = array(
                'user_id'     => $request->user_id,
                'admin_input' => $admin_input,
                'user_input'  => $user_input,
                'date'        => $request->date,
                'time'        => $request->hour,
            );

            $userObj = HubstaffTaskEfficiency::where('user_id', $request->user_id)->where('date', $request->date)->where('time', $request->hour)->first();
            if ($userObj) {
                if ($request->type == 'admin') {
                    $user_input = $userObj->user_input;
                } else {
                    $admin_input = $userObj->admin_input;
                }
                $userObj->update(['admin_input' => $admin_input, 'user_input' => $user_input]);
            } else {
                HubstaffTaskEfficiency::create($insert_array);
            }
        }

        return response()->json(['message' => 'Successful'], 200);
    }

    public function taskActivity(Request $request)
    {
        $task_id = $request->task_id;
        $user_id = $request->user_id;

        /*$query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->where('hubstaff_activities.task_id', '=',$task_id);

        $activities  = $query->select(DB::raw("
        hubstaff_activities.user_id,
        SUM(hubstaff_activities.tracked) as total_tracked,
        DATE(hubstaff_activities.starts_at) as date,
        hubstaff_members.user_id as system_user_id
        ")
        )->where("task_id",$task_id)
        ->where("hubstaff_activities.user_id",$user_id)
        ->groupBy('task_id')
        ->orderBy('date','desc')
        ->get();*/

        // check the task created date
        $task = \App\DeveloperTask::where(function ($q) use ($task_id) {
            $q->orWhere("hubstaff_task_id", $task_id)->orWhere("lead_hubstaff_task_id", $task_id)->orWhere("team_lead_hubstaff_task_id", $task_id)->orWhere("tester_hubstaff_task_id", $task_id);
        })->first();

        if (!$task) {
            $task = \App\Task::where(function ($q) use ($task_id) {
                $q->orWhere("hubstaff_task_id", $task_id)->orWhere("lead_hubstaff_task_id", $task_id);
            })->first();
        }

        $date = ($task) ? $task->created_at : date("1998-02-02");

        $activityrecords = DB::select(DB::raw("SELECT CAST(starts_at as date) AS OnDate,  SUM(tracked) AS total_tracked, hour( starts_at ) as onHour
        FROM hubstaff_activities where task_id = '" . $task_id . "' and user_id = " . $user_id . "
        GROUP BY hour( starts_at ) , day( starts_at ) order by OnDate desc"));
        // $activityrecords  = HubstaffActivity::whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id)->select('hubstaff_activities.*')->get();

        $admins = User::join('role_user', 'role_user.user_id', 'users.id')->join('roles', 'roles.id', 'role_user.role_id')
            ->where('roles.name', 'Admin')->select('users.name', 'users.id')->get();

        $teamLeaders = [];

        $users = User::select('name', 'id')->get();

        $hubstaff_member    = HubstaffMember::where('hubstaff_user_id', $user_id)->first();
        $hubActivitySummery = null;
        if ($hubstaff_member) {
            $system_user_id     = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::whereDate('date', ">=", $date)->where('user_id', $system_user_id)->orderBy('created_at', 'DESC')->get();
            $teamLeaders        = User::join('teams', 'teams.user_id', 'users.id')->join('team_user', 'team_user.team_id', 'teams.id')->where('team_user.user_id', $system_user_id)->distinct()->select('users.name', 'users.id')->get();
        }

        $approved_ids = [0];
        if ($hubActivitySummery) {
            if (!$hubActivitySummery->isEmpty()) {
                foreach ($hubActivitySummery as $hubA) {
                    if (isset($hubA->approved_ids)) {
                        $approved_idsArr = json_decode($hubA->approved_ids);
                        if (!empty($approved_idsArr) && is_array($approved_idsArr)) {
                            $approved_ids = array_merge($approved_ids, $approved_idsArr);
                        }
                    }
                }
            }
        }

        foreach ($activityrecords as $record) {

            $activities = DB::select(DB::raw("SELECT hubstaff_activities.* FROM hubstaff_activities where task_id = " . $task_id . " and DATE(starts_at) = '" . $record->OnDate . "' and user_id = " . $user_id . " and hour(starts_at) = " . $record->onHour . ""));

            $totalApproved = 0;
            $isAllSelected = 0;

            foreach ($activities as $a) {

                if (in_array($a->id, $approved_ids)) {

                    $isAllSelected = $isAllSelected + 1;
                    $a->status     = 1;

                    $hubAct = HubstaffActivity::where('id', $a->id)->first();
                    if ($hubAct) {
                        $totalApproved = $totalApproved + $a->tracked;
                    }

                    $a->totalApproved = $a->tracked;
                } else {
                    $a->status        = 0;
                    $a->totalApproved = 0;
                }

                $taskSubject = '';
                if ($a->task_id) {
                    if ($a->is_manual) {
                        $task = DeveloperTask::where('id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    } else {
                        $task = DeveloperTask::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                        if ($task) {
                            $taskSubject = '#DEVTASK-' . $task->id . '-' . $task->subject;
                        } else {
                            $task = Task::where('hubstaff_task_id', $a->task_id)->orWhere('lead_hubstaff_task_id', $a->task_id)->first();
                            if ($task) {
                                $taskSubject = '#TASK-' . $task->id . '-' . $task->task_subject;
                            }
                        }
                    }

                }

                $a->taskSubject = $taskSubject;
            }
            if ($isAllSelected == count($activities)) {
                $record->sample = 1;
            } else {
                $record->sample = 0;
            }
            $record->activities    = $activities;
            $record->totalApproved = $totalApproved;
        }
        $user_id = $request->user_id;
        $isAdmin = false;
        if (Auth::user()->isAdmin()) {
            $isAdmin = true;
        }
        $isTeamLeader = false;
        $isLeader     = Team::where('user_id', Auth::user()->id)->first();
        if ($isLeader) {
            $isTeamLeader = true;
        }
        $taskOwner = false;
        if (!$isAdmin && !$isTeamLeader) {
            $taskOwner = true;
        }
        //$date = $request->date;

        $member = HubstaffMember::where('hubstaff_user_id', $request->user_id)->first();
        $isTaskWise = true;
        return view("hubstaff.activities.activity-records", compact('activityrecords', 'user_id', 'date', 'hubActivitySummery', 'teamLeaders', 'admins', 'users', 'isAdmin', 'isTeamLeader', 'taskOwner', 'member','isTaskWise'));

    }
}
