<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivitySummary extends Model
{
    protected $fillable = [
        'user_id','date','tracked','accepted','rejected','rejection_note'
    ];
}
