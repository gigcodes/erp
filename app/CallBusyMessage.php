<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBusyMessage extends Model {

    protected $fillable = ['lead_id', 'twilio_call_sid', 'caller_sid', 'message', 'recording_url', 'status'];
    protected $table = "call_busy_messages";
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Function to insert large amount of data
     * @param type $data
     * @return $result
     */
    public static function bulkInsert($data) {
        CallBusyMessage::insert($data);
    }
    
    /**
     * Function to check Twilio Sid 
     * @param string $sId twilio sid
     */
     public static function checkSidAlreadyExist($sId){
         return CallBusyMessage::where('caller_sid', '=', $sId)->first();
     }

}
