<?php
namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\DeveloperTask;

class WebNotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('home');
    }

    public function storeToken(Request $request)
    {
        auth()->user()->update(['device_token'=>$request->token]);
        return response()->json(['Token successfully stored.']);
    }

    public function sendWebNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $sendTo = $request->get('sendTo', 'to_developer');
        $issue = DeveloperTask::find($request->get('issue_id'));
        $userId = $issue->assigned_to;
        if ($sendTo == 'to_master') {
            if ($issue->master_user_id) {
                $userId = $issue->master_user_id;
            }
        }

        if ($sendTo == 'to_team_lead') {
            if ($issue->team_lead_id) {
                $userId = $issue->team_lead_id;
            }
        }

        if ($sendTo == 'to_tester') {
            if ($issue->tester_id) {
                $userId = $issue->tester_id;
            }
        }
        if (isset($userId) && $userId){
            $FcmToken = User::whereNotNull('device_token')->whereId($userId)->pluck('device_token')->all();
        }
        else {
            $FcmToken = User::whereNotNull('device_token')->pluck('device_token')->all();
        }
        $serverKey = env('FCM_SECRET_KEY');
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response

    }
}