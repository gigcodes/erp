<?php

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

class ZoomMeetingController extends Controller
{
    public function __construct()
    {
        $this->zoomkey = env('ZOOM_API_KEY');
        $this->zoomsecret = env('ZOOM_API_SECRET');
    }
   public function createMeeting( Request $request )
    { echo "hii"; die;
        $this->validate( $request, [
            'meeting_topic' => 'required|min:3|max:255',
            'start_date_time' => 'required',
            'meeting_duration' => 'required',
            'meeting_timezone' => 'required'
        ] );
        $input = $request->all();
        $input[ 'parent_id' ] = empty( $input[ 'parent_id' ] ) ? 0 : $input[ 'parent_id' ];

        ZoomMeetings::create( $input );
        return back()->with( 'success', 'New Meeting added successfully.' );
    }
    
    public function getMeetings()
    {
        $zoomKey =  $this->zoomkey;
        $zoomSecret = $this->zoomsecret;
        $zoom = new LaravelZoom($zoomKey,$zoomSecret);
        $meeting1 = $zoom->getJWTToken(time() + 7200); 
        $meeting = $zoom->getUsers('active',10);
        echo "hello"; echo "<pre>"; print_r($meeting); die; die;
       
    }
}
