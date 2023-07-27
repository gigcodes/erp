<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwilioConditionStatus;

class TwilioCondition extends Model
{
    protected $table = 'twilio_conditions';

    protected $fillable = [
        'condition',
        'description',
        'status',
    ];

    public function twilioStatusColour()
    {
        return $this->belongsTo(TwilioConditionStatus::class, 'status');
    }
}
