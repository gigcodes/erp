<?php
/**
 * Class ZoomMeetingController | app/Http/Controllers/Meeting/ZoomMeetingController.php
 * Zoom Meetings integration for video call purpose using LaravelZoom's REST API
 *
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 *
 * @see https://github.com/saineshmamgain/laravel-zoom
 * @see ZoomMeetings
 *
 * @author   sololux <sololux@gmail.com>
 */

namespace App\Http\Controllers\Meeting;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Meetings\ZoomMeetings;
use App\Http\Controllers\Controller;
use App\Meetings\ZoomApiLog;
use App\Meetings\ZoomMeetingDetails;
use App\Meetings\ZoomMeetingParticipant;
use App\ZoomOAuthHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use seo2websites\LaravelZoom\LaravelZoom;

/**
 * Class ZoomMeetingController - active record
 *
 * A zoom class used to create meetings
 * This class is used to interact with zoom interface.
 */
class ZoomMeetingController extends Controller
{
    /**
     * Constructor of class
     * Calling env variables and adding in scope
     */
    public function __construct()
    {
        $this->zoomkey = env('ZOOM_API_KEY');
        $this->zoomsecret = env('ZOOM_API_SECRET');
        $this->zoomuser = env('ZOOM_USER');
    }

   /**
    * Create a meeting with zoom based on the params send through form
    *
    * @param  Request  $request Request
    * @return \Illuminate\Http\Response
    *
    * @Rest\Post("twilio/token")
    *
    * @uses Auth
    * @uses ClientToken
    */
   public function createMeeting(Request $request)
   {
       $this->validate($request, [
           'meeting_topic' => 'required|min:3|max:255',
           //  'start_date_time' => 'required',
           //  'meeting_duration' => 'required',
           //'timezone' => 'required'
       ]);

       $input = $request->all();

       $startDate = strtotime(new Carbon($request->get('start_date_time', date('Y-m-d H:i', strtotime('+5 minutes')))));
       $currentDate = strtotime(Carbon::now());

       if ($startDate < $currentDate) {
           $data = ['msg' => 'Start date time should not be less than current date time.'];

           return Response::json([
               'success' => false,
               'data' => $data,
           ]);
       }

       $userId = $this->zoomuser;
       // Default settings for zoommeeting
       $settings = [
           'join_before_host' => true,
           'host_video' => true,
           'participant_video' => true,
           'mute_upon_entry' => false,
           'enforce_login' => false,
           'auto_recording' => 'cloud',
       ];

       // add default setting in meeting
       $input['start_date_time'] = date('Y-m-d H:i', $startDate);
       $input['meeting_duration'] = $request->get('meeting_duration', 5);
       $input['timezone'] = $request->get('timezone', 'Asia/Dubai');
       $input['meeting_agenda'] = $request->get('agenda', '');
       $input['timezone'] = ($input['timezone'] != '') ? $input['timezone'] : 'Asia/Dubai';
       // gethering all data to pass to model function
       $input['timezone'] = ($input['timezone'] != '') ? $input['timezone'] : 'Asia/Dubai';
       $data = [
           'user_id' => $userId,
           'topic' => $input['meeting_topic'],
           'agenda' => $input['meeting_agenda'],
           'settings' => $settings,
           'startTime' => new Carbon($input['start_date_time']),
           'duration' => $input['meeting_duration'],
           'timezone' => $input['timezone'],
       ];
       // Calling model calss
       $meetings = new ZoomMeetings();
       $zoomKey = $this->zoomkey;
       $zoomSecret = $this->zoomsecret;

       $createMeeting = $meetings->createMeeting($zoomKey, $zoomSecret, $data);
       // dd($createMeeting);
       if ($createMeeting) {
           $input['meeting_id'] = empty($createMeeting['body']['id']) ? '' : $createMeeting['body']['id'];
           //$input['host_zoom_id'] = $this->zoomuser;
           $input['host_zoom_id'] = $userId;
           $input['meeting_type'] = 'scheduled';
           $input['join_meeting_url'] = empty($createMeeting['body']['join_url']) ? '' : $createMeeting['body']['join_url'];
           $input['start_meeting_url'] = empty($createMeeting['body']['start_url']) ? '' : $createMeeting['body']['start_url'];
           // saving data in db
           $createMeeting = ZoomMeetings::create($input);
           if ($createMeeting) {
               $getUserDetails = $meetings->getUserDetails($input['user_id'], $input['user_type']);
               if (! empty($getUserDetails)) {
                   $phonenumber = isset($getUserDetails->number) ? $getUserDetails->number : $getUserDetails->phone;
                   $msg = 'New meeting has been scheduled for you. Kindly find below the link to join the meeting. ' . $input['join_meeting_url'];
                   $html = "New meeting has been scheduled for you. Kindly find below the link to join the meeting. <br><br> <a href='" . $input['join_meeting_url'] . "' target='_blank'>" . $input['join_meeting_url'] . '</a>';
                   if (! empty($phonenumber)) {
                       $message = app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($phonenumber, $getUserDetails->whatsapp_number, $msg);
                   }
                   $email = $getUserDetails->email;
                   if (! empty($email)) {
                       if ('supplier' == $input['user_type']) {
                           $name = $getUserDetails->supplier;
                       } else {
                           $name = $getUserDetails->name;
                       }
                       $data = ['name' => 'SoloLuxury'];
                   }
               }
               $data = ['msg' => 'New Meeting added successfully.', 'meeting_link' => $input['join_meeting_url'], 'start_meeting' => $input['start_meeting_url']];

               return Response::json([
                   'success' => true,
                   'data' => $data,
               ]);
           } else {
               $data = ['msg' => 'Token is expired. Please try to add the meeting again.'];

               return Response::json([
                   'success' => false,
                   'data' => $data,
               ]);
           }
       } else {
           $data = ['msg' => 'Meeting not added.'];

           return Response::json([
               'success' => false,
               'data' => $data,
           ]);
       }
   }

    public function getMeetings()
    {
        $zoomKey = $this->zoomkey;
        $zoomSecret = $this->zoomsecret;
        $zoom = new LaravelZoom($zoomKey, $zoomSecret);
        $meeting1 = $zoom->getJWTToken(time() + 7200);
        $meeting = $zoom->getUsers('active', 10);
        $user_id = '-ISK-roPRUyC3-3N5-AT_g';
        $topic = 'Test meeting using erp';
        $agenda = 'Communication with team';
        $startTime = Carbon::tomorrow();
        $duration = 40;
        $timezone = 'Asia/Kolkata';
        $settings = [
            'join_before_host' => true,
            'host_video' => true,
            'participant_video' => true,
            'mute_upon_entry' => false,
            'enforce_login' => false,
            'auto_recording' => 'local',
        ];

        $data = ['user_id' => $user_id, 'topic' => $topic, 'agenda' => $agenda, 'settings' => $settings, 'startTime' => $startTime, 'duration' => $duration, 'timezone' => $timezone, 'type' => 'all'];
        $meetings = new ZoomMeetings();
        //$createMeet = $meetings->getMeetings($zoomKey,$zoomSecret, $data);
        $createMeet = $meetings->createMeeting($zoomKey, $zoomSecret, $data);
        echo 'hello';
        echo '<pre>';
        print_r($createMeet);
        exit;
        exit;
    }

    public function showData(Request $request)
    {
        $type = $request->get('type');
        $meetings = new ZoomMeetings();
        $curDate = Carbon::now();
        $upcomingMeetings = $meetings->upcomingMeetings($type, $curDate);
        $pastMeetings = $meetings->pastMeetings($type, $curDate);

        return view('zoom-meetings.showdata', [
            'upcomingMeetings' => $upcomingMeetings,
            'pastMeetings' => $pastMeetings,
            'type' => $type,
        ]);
    }

    public function allMeetings()
    {
        $meetings = ZoomMeetings::orderBy('zoom_meetings.start_date_time', 'DESC')->paginate(10);

        return view('zoom-meetings.index', [
            'meetingData' => $meetings,
        ]);
    }

    public function fetchRecordings(Request $request)
    {
        $tokenResponse = ZoomOAuthHelper::getAccessToken();
        
        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];
            $recordingURL = 'https://api.zoom.us/v2/meetings/' . $request->meetingId . '/recordings';

            try {
                // Fetch recordings for this meeting
                $recordingsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get($recordingURL);
    
                // Log the API request and response to the database
                ZoomApiLog::create([
                    'type' => 'recording',
                    'request_url' => $recordingURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer ' . $accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => $recordingsResponse->status(),
                    'response_data' => json_encode($recordingsResponse->json()),
                ]);
    
                if ($recordingsResponse->successful()) {
                    $meetingRecording = $recordingsResponse->json();

                    \Log::info('meetingRecording -->' . json_encode($meetingRecording));
        
                    // Code copied from app/Meetings/ZoomMeetings.php:saveRecordings()
                    if ($meetingRecording && isset($meetingRecording['recording_files'])) {
                        $folderPath = public_path() . '/zoom/0/' . $request->meetingId;
                        $databsePath = '/zoom/0/' . $request->meetingId;
                        \Log::info('folderPath -->' . $folderPath);
                        foreach ($meetingRecording['recording_files'] as $recordings) {
                            $checkfile = ZoomMeetingDetails::where('download_url_id', $recordings['id'])->first();
                            if (! $checkfile) {
                                if ('shared_screen_with_speaker_view' == $recordings['recording_type']) {
                                    \Log::info('shared_screen_with_speaker_view');
                                    $fileName = $request->meetingId . '_' . time() . '.mp4';
                                    $urlOfFile = $recordings['download_url'];
                                    $filePath = $folderPath . '/' . $fileName;
                                    if (! file_exists($filePath) && ! is_dir($folderPath)) {
                                        mkdir($folderPath, 0777, true);
                                    }
                                    $ch = curl_init($urlOfFile);
                                    curl_exec($ch);
                                    if (! curl_errno($ch)) {
                                        $info = curl_getinfo($ch);
                                        $downloadLink = $info['redirect_url'];
                                    }
                                    curl_close($ch);
        
                                    if ($downloadLink) {
                                        copy($downloadLink, $filePath);     
                                    }
        
                                    $zoom_meeting_details = new ZoomMeetingDetails();
                                    $zoom_meeting_details->local_file_path = $databsePath . '/' . $fileName;
                                    $zoom_meeting_details->file_name = $fileName;
                                    $zoom_meeting_details->download_url_id = $recordings['id'];
                                    $zoom_meeting_details->meeting_id = $recordings['meeting_id'];
                                    $zoom_meeting_details->file_type = $recordings['file_type'];
                                    $zoom_meeting_details->download_url = $recordings['download_url'];
                                    // $zoom_meeting_details->file_path = $recordings['file_path']; // this field for Zoom On-Premise accounts.
                                    $zoom_meeting_details->file_size = $recordings['file_size'];
                                    $zoom_meeting_details->file_extension = $recordings['file_extension'];
                                    $zoom_meeting_details->save();
                                }
                            }
                        }
                    }

                    return response()->json(['message' => 'Recordings fetched successfully', 'code' => 200]);
                } else {
                    // $errorMessage = $recordingsResponse->body();
                    return response()->json(['message' => 'An error occurred while fetch the zoom recordings, Please check the logs', 'code' => 500], 500);
                }
            } catch (\Exception $e) {
                // Log the exception to the database
                ZoomApiLog::create([
                    'type' => 'recording',
                    'request_url' => $recordingURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer ' . $accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => 500, // Set an appropriate status code for errors
                    'response_data' => json_encode(['error' => $e->getMessage()]),
                ]);

                return response()->json(['message' => 'An error occurred while fetch the zoom recordings, Please check the logs', 'code' => 500], 500);
            }
        }
    }

    public function fetchParticipants(Request $request)
    {
        $tokenResponse = ZoomOAuthHelper::getAccessToken();
        
        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];
            $participantURL = 'https://api.zoom.us/v2/past_meetings/' . $request->meetingId . '/participants';

            try {
                // Fetch participants for this meeting
                $participantsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get($participantURL);

    
                // Log the API request and response to the database
                ZoomApiLog::create([
                    'type' => 'participant',
                    'request_url' => $participantURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer ' . $accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => $participantsResponse->status(),
                    'response_data' => json_encode($participantsResponse->json()),
                ]);
    
                if ($participantsResponse->successful()) {
                    $participants = $participantsResponse->json();

                    if ($participants['total_records'] > 0) {
                        // Store participants in the participants table
                        foreach ($participants['participants'] as $participant) {
                            ZoomMeetingParticipant::updateOrCreate(
                                ['meeting_id' => $request->meetingId, 'email' => $participant['user_email']],
                                [
                                    'name' => $participant['name'],
                                    'join_time' => $participant['join_time'],
                                    'leave_time' => $participant['leave_time'],
                                    'duration' => $participant['duration']
                                ]
                            );
                        }
                    }

                    return response()->json(['message' => 'Participants fetched successfully', 'code' => 200]);
                } else {
                    // $errorMessage = $recordingsResponse->body();
                    return response()->json(['message' => 'An error occurred while fetch the zoom participants, Please check the logs', 'code' => 500], 500);
                }
            } catch (\Exception $e) {
                // Log the exception to the database
                ZoomApiLog::create([
                    'type' => 'participant',
                    'request_url' => $participantURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer ' . $accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => 500, // Set an appropriate status code for errors
                    'response_data' => json_encode(['error' => $e->getMessage()]),
                ]);

                return response()->json(['message' => 'An error occurred while fetch the zoom participants, Please check the logs', 'code' => 500], 500);
            }
        }
        
    }

    public function show()
    {
        $type = '';
        $upcomingMeetings = [];
        $pastMeetings = [];

        return view('zoom-meetings.showdata', [
            'upcomingMeetings' => $upcomingMeetings,
            'pastMeetings' => $pastMeetings,
            'type' => $type,
        ]);
    }

    public function listParticipants(Request $request)
    {
        $perPage = 5;

        $participants = ZoomMeetingParticipant::where('meeting_id', $request->meetingId)
        ->latest()
        ->paginate($perPage);

        $html = view('zoom-meetings.participations-listing-modal-html')->with('participants', $participants)->render();

        return response()->json(['code' => 200, 'data' => $participants, 'html' => $html, 'message' => 'Content render']);
    }

    public function listErrorLogs(Request $request)
    {
        $zoomApiLogs = new ZoomApiLog();
        
        $zoomApiLogs = $zoomApiLogs->latest()->paginate(\App\Setting::get('pagination', 10));

        return view('zoom-meetings.zoom-error-logs', compact('zoomApiLogs'));
    }

    public function listRecordings($meetingId)
    {

        $zoomRecordings = new ZoomMeetingDetails();
        
        $zoomRecordings = $zoomRecordings->where('meeting_id', $meetingId)->latest()->paginate(\App\Setting::get('pagination', 10));
        
        return view('zoom-meetings.zoom-recodring-list', compact('zoomRecordings'));
    }

    public function updateMeetingDescription(Request $request)
    {
        $meetingdata = ZoomMeetingDetails::find($request->id);
        $meetingdata->description = $request->description;
        $meetingdata->save();

        return response()->json(['code' => 200, 'message' => 'Meeting Added SuccessFully'], 200);
    }

    public function downloadRecords($id)
    {
        $fileName = ZoomMeetingDetails::find($id);
        $file_name = basename($fileName->local_file_path);
        $meetingId = $fileName->meeting_id;

        $filePath = public_path("zoom/zoom/0/$meetingId/$file_name");

        if (file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            abort(404, 'The file you are trying to download does not exist.');
        }
    }

    public function webhook(Request $request)
    {
        $zoomData = json_decode($request->getContent(), true);
        if ($zoomData['event'] == 'endpoint.url_validation') {
            $message = 'v0:'.$request->header('x-zm-request-timestamp').':'.$request->getContent();
            $hash = hash_hmac('sha256', $message, config('services.zoom.secret_token'));
            $signature = "v0={$hash}";
            $verified = hash_equals($request->header('x-zm-signature'), $signature);
            if($verified)
            {
                $zoomSecret = config('services.zoom.secret_token');  
                $zoomPlainToken = $zoomData['payload']['plainToken'];
                $hash = hash_hmac('sha256', $zoomPlainToken, $zoomSecret);
                $reponseData['plainToken'] = $zoomPlainToken;
                $reponseData['encryptedToken'] = $hash;
                return response()->json($reponseData);
            }
        } elseif ($zoomData['event'] == 'meeting.created') {
            return $this->createMeetingWebhook($zoomData);
        } elseif ($zoomData['event'] == 'meeting.participant_joined') {
            return $this->participantJoinedWebhook($zoomData);
        } elseif ($zoomData['event'] == 'meeting.participant_left') {
            return $this->participantLeftWebhook($zoomData);
        }
    }

    protected function createMeetingWebhook($zoomData) {
        ZoomMeetings::updateOrCreate(
            ['meeting_id' => $zoomData['payload']['object']['id']],
            [
                'meeting_topic' => $zoomData['payload']['object']['topic'],
                'meeting_type' => $zoomData['payload']['object']['type'],
                'meeting_agenda' => $zoomData['payload']['object']['agenda'] ?? "",
                'join_meeting_url' => $zoomData['payload']['object']['join_url'],
                'start_date_time' => $zoomData['payload']['object']['start_time'],
                'meeting_duration' => $zoomData['payload']['object']['duration'],
                'timezone' => $zoomData['payload']['object']['timezone'],
                'host_zoom_id' => $zoomData['payload']['object']['host_id'],
            ]
        );

        return response()->json(['message' => 'Meeting created successfully', 'code' => 200], 200);
    }

    protected function participantJoinedWebhook($zoomData) {
        ZoomMeetingParticipant::create([   
                'meeting_id' => $zoomData['payload']['object']['id'], 
                'zoom_user_id' => $zoomData['payload']['object']['participant']['user_id'],
                'participant_uuid' => $zoomData['payload']['object']['participant']['participant_uuid'],
                'name' => $zoomData['payload']['object']['participant']['user_name'],
                'email' => $zoomData['payload']['object']['participant']['email'],
                'join_time' => $zoomData['payload']['object']['participant']['join_time'],
            ],
        );
        
        return response()->json(['message' => 'Participant joined successfully', 'code' => 200], 200);
    }

    protected function participantLeftWebhook($zoomData) {
        ZoomMeetingParticipant::updateOrCreate([
                'meeting_id' => $zoomData['payload']['object']['id'], 
                'zoom_user_id' => $zoomData['payload']['object']['participant']['user_id'],
                'participant_uuid' => $zoomData['payload']['object']['participant']['participant_uuid'],
            ],
            [
                'name' => $zoomData['payload']['object']['participant']['user_name'],
                'email' => $zoomData['payload']['object']['participant']['email'],
                'leave_time' => $zoomData['payload']['object']['participant']['leave_time'] ?? "",
                'leave_reason' => $zoomData['payload']['object']['participant']['leave_reason'] ?? "",
            ]
        );
        
        return response()->json(['message' => 'Participant left successfully', 'code' => 200], 200);
    }
}
