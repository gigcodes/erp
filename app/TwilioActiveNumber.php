<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioActiveNumber extends Model
{
    protected $table = 'twilio_active_numbers';

    protected $fillable = ['sid','account_sid','friendly_name','phone_number','voice_url','date_created','date_updated',
                            'sms_url','voice_receive_mode','api_version','voice_application_sid','sms_application_sid',
                            'trunk_sid','emergency_status','emergency_address_sid','address_sid','identity_sid','bundle_sid',
                            'uri','status'
                            ];

    public function assigned_stores()
    {
        return $this->hasOne('\App\StoreWebsiteTwilioNumber','twilio_active_number_id');
    }

    public function forwarded()
    {
        return $this->hasOne('App\TwilioCallForwarding','twilio_number_sid','sid');
    }

}
