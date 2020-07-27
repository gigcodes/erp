<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Hubstaff\HubstaffActivity;
use App\User;
use DB;
use Artisan;
use App\Hubstaff\HubstaffActivitySummary;
use App\Hubstaff\HubstaffMember;
use App\UserRate;
use App\PaymentMethod;
use App\PaymentReceipt;

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
        $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=',$start_date)->whereDate('hubstaff_activities.starts_at', '<=',$end_date);

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
            $activity->totalNotPaid = HubstaffActivity::whereDate('starts_at',$activity->date)->where('user_id',$activity->user_id)->where('status',1)->where('paid',0)->sum('tracked');

        }
        $users = User::all()->pluck('name','id')->toArray();
        return view("hubstaff.activities.activity-users", compact('title','activityUsers','start_date','end_date','users','user_id'));
    }


 

    public function getActivityDetails(Request $request) {

        if(!$request->user_id || !$request->date || $request->user_id == "" || $request->date == "") {
            return response()->json(['message' => '']); 
        }

        $activityrecords  = HubstaffActivity::whereDate('starts_at',$request->date)->where('user_id',$request->user_id)->get();

        $hubstaff_member = HubstaffMember::where('hubstaff_user_id',$request->user_id)->first();
        $hubActivitySummery = null;
        if($hubstaff_member) {
            $system_user_id = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::where('date',$request->date)->where('user_id',$system_user_id)->first();
        }
        $user_id = $request->user_id;
        $date = $request->date;
        return view("hubstaff.activities.activity-records", compact('activityrecords','user_id','date','hubActivitySummery'));
    }

    public function approveActivity(Request $request) {
        if($request->activities && count($request->activities) > 0) {
            $approved = 0;
            foreach($request->activities as $id) {
               $hubActivity = HubstaffActivity::where('id',$id)->first();
               $hubActivity->update(['status' => 1]);
               $approved = $approved + $hubActivity->tracked;
            }
            
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id);
            $totalTracked = $query->sum('tracked');
            $activity = $query->select('hubstaff_members.user_id')->first();
            $user_id = $activity->user_id;

            $rejected = $totalTracked - $approved;

            

            $hubActivitySummery = HubstaffActivitySummary::where('date',$request->date)->where('user_id',$user_id)->first();
            if($hubActivitySummery) {
                $hubActivitySummery->tracked = $totalTracked;
                $hubActivitySummery->accepted = $approved;
                $hubActivitySummery->rejected = $rejected;
                $hubActivitySummery->rejection_note = $request->rejection_note;
                $hubActivitySummery->save();
            }
            else {
                $hubActivitySummery = new HubstaffActivitySummary;
                $hubActivitySummery->user_id = $user_id;
                $hubActivitySummery->date =  $request->date;
                $hubActivitySummery->tracked = $totalTracked;
                $hubActivitySummery->accepted = $approved;
                $hubActivitySummery->rejected = $rejected;
                $hubActivitySummery->rejection_note = $request->rejection_note;
                $hubActivitySummery->save();
            }


            return response()->json([
                'totalApproved' => $approved
            ],200);
        }
        return response()->json([
            'message' => 'Can not update data'
        ],500);
    }

    public function approvedPendingPayments(Request $request) {
        $title = "Approved pending payments";
        $start_date = $request->start_date ? $request->start_date : date("Y-m-d");
        $end_date = $request->end_date ? $request->end_date : date("Y-m-d");
        $user_id = $request->user_id ? $request->user_id : null;




        if($user_id) {
            $activityUsers = DB::select( DB::raw("select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0 and hubstaff_members.user_id = ".$user_id.") as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id"));
        }
        else {
            $activityUsers = DB::select( DB::raw("select system_user_id, sum(tracked) as total_tracked,starts_at from (select a.* from (SELECT hubstaff_activities.id,hubstaff_activities.user_id,cast(hubstaff_activities.starts_at as date) as starts_at,hubstaff_activities.status,hubstaff_activities.paid,hubstaff_members.user_id as system_user_id,hubstaff_activities.tracked FROM `hubstaff_activities` left outer join hubstaff_members on hubstaff_members.hubstaff_user_id = hubstaff_activities.user_id where hubstaff_activities.status = 1 and hubstaff_activities.paid = 0) as a left outer join payment_receipts on a.system_user_id = payment_receipts.user_id where a.starts_at <= payment_receipts.date) as b group by starts_at,system_user_id"));
        }

        

        foreach($activityUsers as $activity) {
            $user = User::find($activity->system_user_id);
            $latestRatesOnDate = UserRate::latestRatesOnDate($activity->starts_at,$user->id);
                if($activity->total_tracked > 0 && $latestRatesOnDate && $latestRatesOnDate->hourly_rate > 0) {
                    $total = ($activity->total_tracked/60)/60 * $latestRatesOnDate->hourly_rate;
                    $activity->amount = number_format($total,2);
                }
                else {
                    $activity->amount = 0;
                }
            $activity->userName = $user->name;
        }
        $users = User::all()->pluck('name','id')->toArray();
        return view("hubstaff.activities.approved-pending-payments", compact('title','activityUsers','start_date','end_date','users','user_id'));
    }



    public function submitPaymentRequest(Request $request) {
        $this->validate($request, [
            'amount' => 'required',
            'user_id' => 'required',
            'starts_at' => 'required'
        ]);
        
        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date( 'Y-m-d' );
        $payment_receipt->rate_estimated = $request->amount;
        $payment_receipt->status = 'Pending';
        $payment_receipt->user_id = $request->user_id;
        $payment_receipt->remarks = $request->note;
        $payment_receipt->save();

        $hubstaff_user_id = HubstaffMember::where('user_id',$request->user_id)->first()->hubstaff_user_id;

       HubstaffActivity::whereDate('starts_at',$request->starts_at)->where('user_id',$hubstaff_user_id)->where('status',1)->where('paid',0)->update(['paid' => 1]);
        return redirect()->back()->with('success','Successfully submitted');
    }
   
}
