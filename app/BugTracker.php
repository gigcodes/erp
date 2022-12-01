<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BugTracker extends Model
{
    protected $guarded = ['id'];

    public function type()
    {
        $this->belongsTo('App\BugType', 'bug_type_id', 'id');
    }

    public function environment()
    {
        $this->belongsTo(BugEnvironment::class, 'bug_environment_id', 'id');
    }

    public function severity()
    {
        $this->belongsTo(BugSeverity::class, 'bug_severity_id', 'id');
    }

    public function module()
    {
        $this->belongsTo(SiteDevelopmentCategory::class, 'module_id', 'id');
    }
}
