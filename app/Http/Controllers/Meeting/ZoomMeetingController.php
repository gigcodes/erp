<?php
/**
 * Class ZoomMeetingController | app/Http/Controllers/Meeting/ZoomMeetingController.php
 * Zoom Meetings integration for video call purpose using LaravelZoom's REST API
 *
 * @package  Zoom
 * @subpackage Jwt Token
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 * @see https://github.com/saineshmamgain/laravel-zoom
 * @see ZoomMeetings
 * @author   sololux <sololux@gmail.com>
 */
namespace App\Http\Controllers\Meeting;

use App\Meetings\ZoomMeetings;
use Auth;
use Cache;
use Validator;
use Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use CodeZilla\LaravelZoom\LaravelZoom;
use App\Http\Controllers\Controller;
/**
 * Class ZoomMeetingController - active record
 * 
 * A zoom class used to create meetings
 * This class is used to interact with zoom interface.
 *
 * @package  LaravelZoom
 * @subpackage Jwt Token
 */
class ZoomMeetingController extends Controller
{
    /**
     * Constructor of class
     * Calling env variables and adding in scope
     * 
     */
    public function __construct()
    {
        $this->zoomkey = env('ZOOM_API_KEY');
        $this->zoomsecret = env('ZOOM_API_SECRET');
        $this->zoomuser = env('ZOOM_USER');
    }

    /**
     * Create a meeting with zoom based on the params send through form
     * @param Request $request Request
     * @return \Illuminate\Http\Response
     * @Rest\Post("twilio/token")
     * 
     * @uses Auth
     * @uses ClientToken
     */
   public function createMeeting( Request $request )
    { 
        $this->validate( $request, [
            'meeting_topic' => 'required|min:3|max:255',
            'start_date_time' => 'required',
            'meeting_duration' => 'required',
            'meeting_timezone' => 'required'
        ] );
        $input = $request->all(); 
        $userId = $this->zoomuser;
        // Default settings for zoommeeting
         $settings = [
            'join_before_host' => true,
            'host_video' => true,
            'participant_video' => true,
            'mute_upon_entry' => false,
            'enforce_login' => false,
            'auto_recording' => 'cloud'
        ]; 
        // gethering all data to pass to model function
        $data = [
            'user_id' => $userId,
            'topic' => $input['meeting_topic'], 
            'agenda' => $input['meeting_agenda'],
            'settings' => $settings, 
            'startTime' => new Carbon($input['start_date_time']), 
            'duration' => $input['meeting_duration'], 
            'timezone' => $input['meeting_timezone'], 
            ];
        // Calling model calss
        $meetings = new ZoomMeetings();
        $zoomKey =  $this->zoomkey;
        $zoomSecret = $this->zoomsecret;
        $createMeeting = $meetings->createMeeting($zoomKey,$zoomSecret, $data);
        if($createMeeting){
         $input[ 'meeting_id' ] = empty( $createMeeting[ 'body' ]['id'] ) ? 0 : $createMeeting[ 'body' ]['id']; 
         $input[ 'host_zoom_id' ] = $this->zoomuser;
         $input[ 'meeting_type' ] = 'scheduled';
         $input[ 'join_meeting_url' ] = empty( $createMeeting[ 'body' ]['join_url'] ) ? 0 : $createMeeting[ 'body' ]['join_url']; 
         $input[ 'start_meeting_url' ] = empty( $createMeeting[ 'body' ]['start_url'] ) ? 0 : $createMeeting[ 'body' ]['start_url']; 
         // saving data in db
         ZoomMeetings::create( $input );
         return back()->with( 'success', 'New Meeting added successfully.' );
        }else{
            return back()->with( 'error', 'Meeting not added.' );
        }
    }
    
    public function getMeetings()
    {
        $zoomKey =  $this->zoomkey;
        $zoomSecret = $this->zoomsecret;
        $zoom = new LaravelZoom($zoomKey,$zoomSecret);
        $meeting1 = $zoom->getJWTToken(time() + 7200); 
        $meeting = $zoom->getUsers('active',10);
        $user_id = '-ISK-roPRUyC3-3N5-AT_g';
        $topic = 'Test meeting using erp';
        $agenda = "Communication with team";
        $startTime = Carbon::tomorrow();
        $duration = 40;
        $timezone = 'Asia/Kolkata';
        $settings = [
            'join_before_host' => true,
            'host_video' => true,
            'participant_video' => true,
            'mute_upon_entry' => false,
            'enforce_login' => false,
            'auto_recording' => 'local'
        ];
        
        $data = ['user_id' => $user_id,'topic' => $topic, 'agenda' => $agenda, 'settings' => $settings, 'startTime' => $startTime, 'duration' => $duration, 'timezone' => $timezone, 'type' => 'all'];
        $meetings = new ZoomMeetings();
        //$createMeet = $meetings->getMeetings($zoomKey,$zoomSecret, $data);
        $createMeet = $meetings->createMeeting($zoomKey,$zoomSecret, $data);
        echo "hello"; echo "<pre>"; print_r($createMeet); die; die;
       
    }

    public function showData($type){ 
    $meetings = new ZoomMeetings();
    $curDate = Carbon::now();
    $upcomingMeetings = $meetings->upcomingMeetings($type, $curDate); 
    $pastMeetings = $meetings->pastMeetings($type, $curDate);
    return view('zoom-meetings.showdata', [
            'upcomingMeetings' => $upcomingMeetings,
            'pastMeetings' => $pastMeetings,
            'type' => $type
        ]);   
    }
}
