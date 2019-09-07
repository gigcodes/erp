<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallBusyMessage extends Model
{
    protected $fillable = ['lead_id', 'twilio_call_sid' , 'caller_sid' ,'message', 'recording_url', 'status'];
	protected $table ="call_busy_messages";
	protected $dates = ['created_at', 'updated_at'];
/**
 * Function to insert large amount of data
 * @param type $data
 * @return $result
 */        
public function bulkInsert($data){
    
}
}

