<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadHubstaffDetail extends Model
{
    public $table = 'lead_hubstaff_detail';

    protected $fillable = ['id','hubstaff_task_id','task_id','team_lead_id','current','created_at', 'updated_at'];
}
