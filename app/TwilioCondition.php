<?php

namespace App;

use App\Models\TwilioConditionStatus;
use Illuminate\Database\Eloquent\Model;

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
