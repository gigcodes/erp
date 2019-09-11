<?php
/**
 * Class ZoomMeetings | app/Meetings/Meeting/ZoomMeetings.php
 * Zoom Meetings integration for video call purpose using LaravelZoom's REST API
 *
 * @package  Zoom
 * @subpackage Jwt Token
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 * @see https://github.com/saineshmamgain/laravel-zoom
 * @see ZoomMeetings
 * @author   sololux <sololux@gmail.com>
 */
namespace App\Meetings;
use CodeZilla\LaravelZoom\LaravelZoom;
use Illuminate\Database\Eloquent\Model;
/**
 * Class ZoomMeetings - active record
 * 
 * A zoom class used to create meetings
 * This class is used to interact with zoom interface.
 *
 * @package  LaravelZoom
 * @subpackage Jwt Token
 */
class ZoomMeetings extends Model
{
   protected $fillable = ['meeting_id','meeting_topic','meeting_type', 'meeting_agenda', 'join_meeting_url', 'start_meeting_url', 'start_date_time', 'meeting_duration', 'host_zoom_id', 'zoom_recording', 'customer_id'];
   public function createMeeting($zoomKey,$zoomSecret,$data)
    {
        $zoom = new LaravelZoom($zoomKey,$zoomSecret);
        $token = $zoom->getJWTToken(time() + 7200); 
        //$meeting = $zoom->createInstantMeeting($data['user_id'],$data['topic'], '', $data['agenda'], '',$data['settings']);
        $meeting = $zoom->createScheduledMeeting($data['user_id'], $data['topic'], $data['startTime'], $data['duration'], $data['timezone'], '', '', $data['agenda'], [],$data['settings']);
        return $meeting;    
    }
    
    public function getMeetings($zoomKey,$zoomSecret,$data)
    {
        $zoom = new LaravelZoom($zoomKey,$zoomSecret);
        $meeting1 = $zoom->getJWTToken(time() + 7200);
        $meetingAll = $zoom->getMeetings($data['user_id'],$data['type'],10);
        echo "reach"; echo "<pre>"; print_r($meetingAll); die;
       
    }
}
