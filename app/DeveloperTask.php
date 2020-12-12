<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;

class DeveloperTask extends Model
{
    use Mediable;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'module_id', 'priority', 'subject', 'task', 'cost', 'status', 'module', 'completed', 'estimate_time', 'start_time', 'end_time', 'task_type_id', 'parent_id', 'created_by', 'submitted_by',
        'responsible_user_id','assigned_to','assigned_by','language','master_user_id', 'hubstaff_task_id','is_milestone','no_of_milestone','milestone_completed','customer_id','lead_hubstaff_task_id','team_lead_id','tester_id','team_lead_hubstaff_task_id','tester_hubstaff_task_id','site_developement_id','priority_no'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function development_details()
    {
        return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-detail')->latest();
    }

    public function development_discussion()
    {
        return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-discussion')->latest();
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'developer_task_id', 'id');
    }

    public function developerModule()
    {
        return $this->belongsTo(DeveloperModule::class, 'module_id', 'id');
    }

    public function communications()
    {
        return $this->hasMany(ChatMessage::class, 'issue_id', 'id');
    }
    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id', 'id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function whatsappAll($needBroadCast = false)
    {
        if($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'developer_task_id')->whereIn('status', ['7', '8', '9', '10'])->latest();    
        }
        
        return $this->hasMany('App\ChatMessage', 'developer_task_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function countUserTaskFromReference($id){
        return $this->whereNotNull('reference')->where('responsible_user_id',$id)->count();
    }

    public function masterUser()
    {
        return $this->belongsTo(User::class, 'master_user_id', 'id');
    }

    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id', 'id');
    }

    public function tester()
    {
        return $this->belongsTo(User::class, 'tester_id', 'id');
    }

    public function timeSpent(){
        return $this->hasOne(
            'App\Hubstaff\HubstaffActivity',
            'task_id',
            'hubstaff_task_id'
        )
        ->selectRaw('task_id, SUM(tracked) as tracked')
        ->groupBy('task_id');
    }

    public function leadtimeSpent(){
        return $this->hasOne(
            'App\Hubstaff\HubstaffActivity',
            'task_id',
            'lead_hubstaff_task_id'
        )
        ->selectRaw('task_id, SUM(tracked) as tracked')
        ->groupBy('task_id');
    }

    public function testertimeSpent(){
        return $this->hasOne(
            'App\Hubstaff\HubstaffActivity',
            'task_id',
            'tester_hubstaff_task_id'
        )
        ->selectRaw('task_id, SUM(tracked) as tracked')
        ->groupBy('task_id');
    }

    public function taskType()
    {
        return $this->belongsTo(TaskTypes::class, 'task_type_id', 'id');
    }

    public function allMessages()
    {
        return $this->hasMany(ChatMessage::class, 'developer_task_id', 'id')->orderBy('id','desc');
    }
}
