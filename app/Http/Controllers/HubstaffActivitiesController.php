<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Hubstaff\HubstaffActivity;
use App\User;
use DB;
use Artisan;
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

        if($request->start_date != null) {
            $records = $records->whereDate("start_date",">=",$request->start_date. " 00:00:00");
        }

        if($request->end_date != null) {
            $records = $records->whereDate("start_date","<=",$request->end_date. " 23:59:59");
        }

        $records = $records->select(["hubstaff_activity_notifications.*", "u.name as user_name"])->get();
        return response()->json(["code" => 200, "data" => $records, "total" => count($records)]);
    }

    public function notificationReasonSave(Request $request )
    {
        if($request->id != null) {
            $hnotification = \App\Hubstaff\HubstaffActivityNotification::find($request->id);
            if($hnotification != null) {
                $hnotification->reason = $request->reason;
                $hnotification->save();
                return response()->json(["code" => 200, "data" => [], "message" => "Added succesfully"]);
            }
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Requested id is not in database"]);
    }

    public function changeStatus(Request $request) {
        if(!auth()->user()->isAdmin()) {
            return response()->json(["code" => 500, "data" => [], "message" => "only admin can change status."]);
        }
        if($request->id != null) {
            $hnotification = \App\Hubstaff\HubstaffActivityNotification::find($request->id);
            if($hnotification != null) {
                $hnotification->status = $request->status;
                $hnotification->save();
                return response()->json(["code" => 200, "data" => [], "message" => "changed succesfully"]);
            }
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Requested id is not in database"]);
    }


    public function getActivityUsers(Request $request)
    {
        $title = "Hubstaff Activities";


        $start_date = $request->start_date ? $request->start_date : date("Y-m-d");
        $end_date = $request->end_date ? $request->end_date : date("Y-m-d");
        $user_id = $request->user_id ? $request->user_id : null;
        $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->where('hubstaff_activities.starts_at', '>=',$start_date)->where('hubstaff_activities.starts_at', '<=',$end_date);

        if($request->user_id) {
            $query = $query->where('hubstaff_members.user_id',$request->user_id);
        }
        $activityUsers  = $query->select(DB::raw("
        hubstaff_activities.user_id,
        SUM(hubstaff_activities.tracked) as total_tracked,DATE(hubstaff_activities.starts_at) as date,hubstaff_members.user_id as system_user_id")
      )->groupBy('date','user_id')->orderBy('date','desc')->get();

        // $userActivities = $activities->filter(function ($value, $key) use ($user) {
        //     return $value->system_user_id === $user->id;
        // });

        foreach($activityUsers as $activity) {
            if($activity->system_user_id) {
                $user = User::find($activity->system_user_id);
                if($user) {
                    $activity->userName = $user->name;
                }
                else {
                    $activity->userName = '';
                }
            }
            else {
                $activity->userName = '';
            }
            $activity->totalApproved = HubstaffActivity::whereDate('starts_at',$activity->date)->where('user_id',$activity->user_id)->where('status',1)->sum('tracked');

        }
        $users = User::all()->pluck('name','id')->toArray();
        return view("hubstaff.activities.activity-users", compact('title','activityUsers','start_date','end_date','users','user_id'));
    }

    public function getActivityDetails(Request $request) {

        if(!$request->user_id || !$request->date || $request->user_id == "" || $request->date == "") {
            return response()->json(['message' => '']); 
        }

        $activityrecords  = HubstaffActivity::whereDate('starts_at',$request->date)->where('user_id',$request->user_id)->get();
        $user_id = $request->user_id;
        $date = $request->date;
        return view("hubstaff.activities.activity-records", compact('activityrecords','user_id','date'));
    }

    public function approveActivity(Request $request) {
        if($request->activities && count($request->activities) > 0) {
            foreach($request->activities as $id) {
                HubstaffActivity::where('id',$id)->update(['status' => 1]);
            }
            $totalApproved = HubstaffActivity::whereDate('starts_at',$request->date)->where('user_id',$request->user_id)->where('status',1)->sum('tracked');

            return response()->json([
                'totalApproved' => $totalApproved
            ],200);
        }
        return response()->json([
            'message' => 'Can not update data'
        ],500);
    }
   
}
