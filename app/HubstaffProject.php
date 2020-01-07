<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubstaffProject extends Model
{
    protected $fillable = [
        'hubstaff_project_id',
        'organisation_id',
        'hubstaff_project_name',
        'hubstaff_project_description',
        'hubstaff_project_status'
    ];
}
