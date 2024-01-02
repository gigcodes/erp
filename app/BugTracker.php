<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BugTracker extends Model
{
    protected $guarded = ['id'];

    public function type()
    {
        $this->belongsTo(\App\BugType::class, 'bug_type_id', 'id');
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

    public function userassign()
    {
        return $this->belongsTo(User::class, 'assign_to', 'id');
    }

    public function whatsappAll($needBroadcast = false)
    {
        if ($needBroadcast) {
            return $this->hasMany(\App\ChatMessage::class, 'bug_id')->where(function ($q) {
                $q->whereIn('status', ['7', '8', '9', '10'])->orWhere('group_id', '>', 0);
            })->latest();
        } else {
            return $this->hasMany(\App\ChatMessage::class, 'bug_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
        }
    }

    public function chatlatest()
    {
        //return $this->hasMany(\App\ChatMessage::class, 'bug_id')->latest()->orderBy('id', 'desc')->limit(1);
        return $this->hasMany(\App\ChatMessage::class, 'bug_id');
    }
}
