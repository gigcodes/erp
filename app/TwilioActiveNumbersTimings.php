<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioActiveNumbersTimings extends Model
{

    protected $table = 'twilio_active_numbers_timings';

    protected $fillable = ['twilio_active_number_id','day','morning_start','morning_end','evening_start','evening_end','status'];

}
