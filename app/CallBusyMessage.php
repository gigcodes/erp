<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallBusyMessage extends Model {

    /**
     * @var string
     * @SWG\Property(enum={"lead_id", "twilio_call_sid", "caller_sid", "message", "recording_url", "status"})
     */
    protected $fillable = ['lead_id', 'twilio_call_sid', 'caller_sid', 'message', 'recording_url', 'status'];
    /**
     * @var string
     * @SWG\Property(enum={"call_busy_messages"})
     */
    protected $table = "call_busy_messages";
    /**
     * @var string
     * @SWG\Property(enum={"created_at", "updated_at"})
     */
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
