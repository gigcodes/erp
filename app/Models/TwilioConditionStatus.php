<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioConditionStatus extends Model
{
    use HasFactory;

    protected $table = 'twilio_conditions_status';

    protected $fillable = [
        'status_name',
        'status_id',
        'color',
    ];
}
