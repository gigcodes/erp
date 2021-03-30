<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivitySummary extends Model
{
    protected $fillable = [
        'user_id','date','tracked','user_requested','accepted','rejected','rejection_note','approved_ids','rejected_ids','sender','receiver','forworded_person','final_approval'
    ];
}
