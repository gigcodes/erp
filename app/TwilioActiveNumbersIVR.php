<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioActiveNumbersIVR extends Model
{

    protected $table = 'twilio_active_numbers_ivr';

    protected $fillable = ['twilio_active_number_id','category_id','response','active'];

    public function linked_timings() {
		return $this->hasOne('App\TwilioActiveNumbersTimings', 'twilio_active_number_id', 'twilio_active_number_id');
    }
}
