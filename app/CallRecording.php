<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
class CallRecording extends Model
{
    //
    /**
     * @var string
     * @SWG\Property(enum={"lead_id", "order_id", "customer_id", "recording_url", "twilio_call_sid" , "customer_number","callsid", "message"})
     */
    protected $fillable = ['lead_id', 'order_id', 'customer_id', 'recording_url', 'twilio_call_sid' , 'customer_number','callsid', 'message'];
    /**
     * @var string
     * @SWG\Property(enum={"model_id", "model_type", "name", "phone", "whatsapp_number", "address", "email"})
     */
	protected $table ="call_recordings";
	/**
     * @var string
     * @SWG\Property(enum={"model_id", "model_type", "name", "phone", "whatsapp_number", "address", "email"})
     */
	protected $dates = ['created_at', 'updated_at'];
}
