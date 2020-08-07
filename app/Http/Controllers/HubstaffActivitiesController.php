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
use Auth;
use App\Team;
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
        if(Auth::user()->isAdmin()) {
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=',$start_date)->whereDate('hubstaff_activities.starts_at', '<=',$end_date);
            $users = User::all()->pluck('name','id')->toArray();
        }
        else {
            $members = Team::join('team_user','team_user.team_id','teams.id')->where('teams.user_id',Auth::user()->id)->distinct()->pluck('team_user.user_id');
            // if(count($members) > 0) {
            //     $imp = $members->implode(',');
            //      $onlyTheseUsers = '('. $imp . ')';
            // }
            // else {
            //     $authId = Auth::user()->id;
            //     $onlyTheseUsers = '('. $authId . ')'; 
            // }
            if(!count($members)) {
                $members = [Auth::user()->id];
            }
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at', '>=',$start_date)->whereDate('hubstaff_activities.starts_at', '<=',$end_date)->whereIn('hubstaff_members.user_id',$members);
            $users = User::whereIn('id',$members)->pluck('name','id')->toArray();
        }

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



            // $activity->userName = $user->name;
            $hubActivitySummery = HubstaffActivitySummary::where('date',$activity->date)->where('user_id',$activity->system_user_id)->orderBy('created_at','desc')->first();
            $status = 'New';
            $final_approval = 0;
            
            if($hubActivitySummery) {
                if($hubActivitySummery->forworded_person == 'admin') {
                    $activity->status = 'Pending for admin approval';
                }
                if($hubActivitySummery->forworded_person == 'team_lead') {
                    $activity->status = 'Pending for team lead approval';
                }
                if($hubActivitySummery->forworded_person == 'user') {
                    $activity->status = 'Pending for approval';
                }

                $activity->totalApproved = $hubActivitySummery->accepted;
                $activity->totalNotPaid = HubstaffActivity::whereDate('starts_at',$activity->date)->where('user_id',$activity->user_id)->where('status',1)->where('paid',0)->sum('tracked');
                $activity->forworded_to = $hubActivitySummery->receiver;
                if($hubActivitySummery->final_approval)  {
                    $final_approval = 1;
                }
            }
            else {
                $activity->forworded_to = Auth::user()->id;
                $activity->status = 'New';
                $activity->totalApproved = 0;
                $activity->totalNotPaid = 0;
            }
            $activity->final_approval = $final_approval;
        }
        
        return view("hubstaff.activities.activity-users", compact('title','activityUsers','start_date','end_date','users','user_id'));
    }


 

    public function getActivityDetails(Request $request) {

        if(!$request->user_id || !$request->date || $request->user_id == "" || $request->date == "") {
            return response()->json(['message' => '']); 
        }

        $activityrecords  = HubstaffActivity::whereDate('starts_at',$request->date)->where('user_id',$request->user_id)->get();



        $admins = User::join('role_user','role_user.user_id','users.id')->join('roles','roles.id','role_user.role_id')
        ->where('roles.name','Admin')->select('users.name','users.id')->get();

        $teamLeaders =  [];

        $users = User::select('name','id')->get();

        $hubstaff_member = HubstaffMember::where('hubstaff_user_id',$request->user_id)->first();
        $hubActivitySummery = null;
        if($hubstaff_member) {
            $system_user_id = $hubstaff_member->user_id;
            $hubActivitySummery = HubstaffActivitySummary::where('date',$request->date)->where('user_id',$system_user_id)->orderBy('created_at','DESC')->first();

            $teamLeaders = User::join('teams','teams.user_id','users.id')->join('team_user','team_user.team_id','teams.id')->where('team_user.user_id',$system_user_id)->distinct()->select('users.name','users.id')->get();
        }
        $approved_ids = [0]; 
        if($hubActivitySummery) {
            if($hubActivitySummery->approved_ids) {
                $approved_ids = json_decode($hubActivitySummery->approved_ids);   
            }

            if($hubActivitySummery->final_approval)  {
                return response()->json([
                    'message' => 'Already approved'
                ],500);
            }
        }
        foreach($activityrecords as $record) {
            if(in_array($record->id, $approved_ids)) {
                $record->status = 1;
            }
            else {
                $record->status = 0;
            }
        }
        $user_id = $request->user_id;
        $isAdmin = false;
        if(Auth::user()->isAdmin()) {
            $isAdmin = true;
        }
        $isTeamLeader = false;
        $isLeader = Team::where('user_id',Auth::user()->id)->first();
        if($isLeader) {
            $isTeamLeader = true;
        }
        $taskOwner = false;
        if(!$isAdmin && !$isTeamLeader) {
            $taskOwner = true;
        }
        $date = $request->date;
        return view("hubstaff.activities.activity-records", compact('activityrecords','user_id','date','hubActivitySummery','teamLeaders','admins','users','isAdmin','isTeamLeader','taskOwner'));
    }

    public function approveActivity(Request $request) {
        
        if(!$request->forworded_person) {
            return response()->json([
                'message' => 'Please forword someone'
            ],500);
        }
        if($request->forworded_person == 'admin') {
            $forword_to = $request->forword_to_admin;
        }
        if($request->forworded_person == 'team_lead') {
            $forword_to = $request->forword_to_team_leader;
        }
        if($request->forworded_person == 'user') {
            $forword_to = $request->forword_to_user;
        }

        $approvedArr = [];
        $rejectedArr = [];
        if($request->activities && count($request->activities) > 0) {
            $approved = 0;
            foreach($request->activities as $id) {
               $hubActivity = HubstaffActivity::where('id',$id)->first();
            //    $hubActivity->update(['status' => 1]);
               $approved = $approved + $hubActivity->tracked;
               $approvedArr[] = $id;
            }
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity = $query->select('hubstaff_members.user_id')->first();
            $user_id = $activity->user_id;
            $rejected = $totalTracked - $approved;
            $rejectedArr = $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id)->whereNotIn('hubstaff_activities.id',$approvedArr)->pluck('hubstaff_activities.id')->toArray();

            $approvedJson = json_encode($approvedArr);
            if(count($rejectedArr) > 0) {
                $rejectedJson = json_encode($rejectedArr);
            }
            else {
                $rejectedJson = null;
            }

            $hubActivitySummery = new HubstaffActivitySummary;
            $hubActivitySummery->user_id = $user_id;
            $hubActivitySummery->date =  $request->date;
            $hubActivitySummery->tracked = $totalTracked;
            $hubActivitySummery->accepted = $approved;
            $hubActivitySummery->rejected = $rejected;
            $hubActivitySummery->approved_ids = $approvedJson;
            $hubActivitySummery->rejected_ids = $rejectedJson;
            $hubActivitySummery->sender = Auth::user()->id;
            $hubActivitySummery->receiver = $forword_to;
            $hubActivitySummery->forworded_person = $request->forworded_person;
            $hubActivitySummery->rejection_note = $request->rejection_note;
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
                'totalApproved' => $approved
            ],200);
        }
        return response()->json([
            'message' => 'Can not update data'
        ],500);
    }


    public function finalSubmit(Request $request) {
        $approvedArr = [];
        $rejectedArr = [];
        if($request->activities && count($request->activities) > 0) {
            $approved = 0;
            foreach($request->activities as $id) {
               $hubActivity = HubstaffActivity::where('id',$id)->first();
               $hubActivity->update(['status' => 1]);
               $approved = $approved + $hubActivity->tracked;
               $approvedArr[] = $id;
            }
            $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id);

            $totalTracked = $query->sum('tracked');
            $activity = $query->select('hubstaff_members.user_id')->first();
            $user_id = $activity->user_id;
            $rejected = $totalTracked - $approved;
            $rejectedArr = $query = HubstaffActivity::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at',$request->date)->where('hubstaff_activities.user_id',$request->user_id)->whereNotIn('hubstaff_activities.id',$approvedArr)->pluck('hubstaff_activities.id')->toArray();

            $approvedJson = json_encode($approvedArr);
            if(count($rejectedArr) > 0) {
                $rejectedJson = json_encode($rejectedArr);
            }
            else {
                $rejectedJson = null;
            }

            $hubActivitySummery = new HubstaffActivitySummary;
            $hubActivitySummery->user_id = $user_id;
            $hubActivitySummery->date =  $request->date;
            $hubActivitySummery->tracked = $totalTracked;
            $hubActivitySummery->accepted = $approved;
            $hubActivitySummery->rejected = $rejected;
            $hubActivitySummery->approved_ids = $approvedJson;
            $hubActivitySummery->rejected_ids = $rejectedJson;
            $hubActivitySummery->sender = Auth::user()->id;
            $hubActivitySummery->receiver = Auth::user()->id;
            $hubActivitySummery->forworded_person = 'admin';
            $hubActivitySummery->final_approval = 1;
            $hubActivitySummery->rejection_note = $request->rejection_note;
            $hubActivitySummery->save();
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
