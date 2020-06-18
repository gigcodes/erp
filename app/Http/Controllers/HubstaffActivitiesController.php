<?php

namespace App\Http\Controllers;

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
   
}
