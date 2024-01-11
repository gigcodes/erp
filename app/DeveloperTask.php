<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Exception;
use Carbon\Carbon;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeveloperTask extends Model
{
    /**
     * @var string
     *
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="module_id",type="integer")

     * @SWG\Property(property="priority",type="string")
     * @SWG\Property(property="subject",type="string")
     * @SWG\Property(property="task",type="string")
     * @SWG\Property(property="cost",type="float")
     * @SWG\Property(property="status",type="string")
     * @SWG\Property(property="module",type="string")
     * @SWG\Property(property="completed",type="string")
     * @SWG\Property(property="estimate_time",type="datetime")
     * @SWG\Property(property="start_time",type="datetime")
     * @SWG\Property(property="end_time",type="datetime")
     * @SWG\Property(property="task_type_id",type="integer")
     * @SWG\Property(property="parent_id",type="integer")
     * @SWG\Property(property="created_by",type="integer")
     * @SWG\Property(property="submitted_by",type="integer")
     * @SWG\Property(property="responsible_user_id",type="integer")
     * @SWG\Property(property="assigned_to",type="integer")
     * @SWG\Property(property="assigned_by",type="integer")
     * @SWG\Property(property="language",type="sting")
     * @SWG\Property(property="master_user_id",type="integer")
     * @SWG\Property(property="hubstaff_task_id",type="integer")
     * @SWG\Property(property="is_milestone",type="boolean")
     * @SWG\Property(property="no_of_milestone",type="sting")
     * @SWG\Property(property="milestone_completed",type="sting")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="lead_hubstaff_task_id",type="integer")
     * @SWG\Property(property="team_lead_id",type="integer")
     * @SWG\Property(property="tester_id",type="integer")
     * @SWG\Property(property="team_lead_hubstaff_task_id",type="integer")
     * @SWG\Property(property="tester_hubstaff_task_id",type="integer")
     * @SWG\Property(property="site_developement_id",type="integer")
     * @SWG\Property(property="priority_no",type="integer")
     */
    use Mediable;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'module_id',
        'log_keyword_id',
        'priority',
        'subject',
        'task',
        'cost',
        'status',
        'module',
        'completed',
        'estimate_time',
        'start_date',
        'start_time',
        'end_time',
        'task_type_id',
        'parent_id',
        'created_by',
        'submitted_by',
        'responsible_user_id',
        'assigned_to',
        'assigned_by',
        'language',
        'master_user_id',
        'hubstaff_task_id',
        'is_milestone',
        'no_of_milestone',
        'milestone_completed',
        'customer_id',
        'lead_hubstaff_task_id',
        'team_lead_id',
        'tester_id',
        'team_lead_hubstaff_task_id',
        'tester_hubstaff_task_id',
        'site_developement_id',
        'priority_no',
        'scraper_id',
        'frequency',
        'message',
        'reminder_from',
        'reminder_last_reply',
        'last_send_reminder',
        'repository_id',
        'last_date_time_reminder',
        'parent_review_task_id',
        'user_feedback_cat_id',
        'time_doctor_task_id',
        'lead_time_doctor_task_id',
        'team_lead_time_doctor_task_id',
        'tester_time_doctor_task_id',
        'manually_assign',
        'slotTaskRemarks',
        'estimate_date',
        'task_start',
        'm_start_date',
        'm_end_date',
    ];

    const DEV_TASK_STATUS_DONE = 'Done';

    const DEV_TASK_STATUS_DISCUSSING = 'Discussing';

    const DEV_TASK_STATUS_IN_PROGRESS = 'In Progress';

    const DEV_TASK_STATUS_ISSUE = 'Issue';

    const DEV_TASK_STATUS_PLANNED = 'Planned';

    const DEV_TASK_STATUS_DISCUSS_WITH_LEAD = 'Discuss with Lead';

    const DEV_TASK_STATUS_NOTE = 'Note';

    const DEV_TASK_STATUS_LEAD_RESPONSE_NEEDED = 'Lead Response Needed';

    const DEV_TASK_STATUS_ERRORS_IN_TASK = 'Errors in Task';

    const DEV_TASK_STATUS_IN_REVIEW = 'In Review';

    const DEV_TASK_STATUS_PRIORITY = 'Priority';

    const DEV_TASK_STATUS_HIGH_PRIORITY = 'High Priority';

    const DEV_TASK_STATUS_REVIEW_ESTIMATED_TIME = 'Review Estimated Time';

    const DEV_TASK_STATUS_USER_COMPLETE = 'User Complete';

    const DEV_TASK_STATUS_USER_ESTIMATED = 'User Estimated';

    const DEV_TASK_STATUS_DECLINE = 'Decline';

    const DEV_TASK_STATUS_REOPEN = 'Reopen';

    const DEV_TASK_STATUS_APPROVED = 'Approved';

    const DEV_TASK_STATUS_FILTER = [
        'DONE' => 'Done',
        'DISCUSSING' => 'Discussing',
        'IN_PROGRESS' => 'In Progress',
        'ISSUE' => 'Issue',
        'PLANNED' => 'Planned',
        'DISCUSS_WITH_LEAD' => 'Discuss with Lead',
        'NOTE' => 'Note',
        'LEAD_RESPONSE_NEEDED' => 'Lead Response Needed',
        'ERRORS_IN_TASK' => 'Errors in Task',
        'IN_REVIEW' => 'In Review',
        'PRIORITY' => 'Priority',
        'HIGH_PRIORITY' => 'High Priority',
        'REVIEW_ESTIMATED_TIME' => 'Review Estimated Time',
        'USER_COMPLETE' => 'User Complete',
        'USER_ESTIMATED' => 'User Estimated',
        'DECLINE' => 'Decline',
        'REOPEN' => 'Reopen',
        'APPROVED' => 'Approved'      
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function development_details()
    {
        return $this->hasMany(\App\Remark::class, 'taskid')->where('module_type', 'task-detail')->latest();
    }

    public function development_discussion()
    {
        return $this->hasMany(\App\Remark::class, 'taskid')->where('module_type', 'task-discussion')->latest();
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
        if ($needBroadCast) {
            return $this->hasMany(\App\ChatMessage::class, 'developer_task_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(\App\ChatMessage::class, 'developer_task_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function countUserTaskFromReference($id)
    {
        return $this->whereNotNull('reference')->where('responsible_user_id', $id)->count();
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

    public function timeSpent()
    {
        return $this->hasOne(
            \App\Hubstaff\HubstaffActivity::class,
            'task_id',
            'hubstaff_task_id'
        )
            ->selectRaw('task_id, SUM(tracked) as tracked')
            ->groupBy('task_id');
    }

    public function leadtimeSpent()
    {
        return $this->hasOne(
            \App\Hubstaff\HubstaffActivity::class,
            'task_id',
            'lead_hubstaff_task_id'
        )
            ->selectRaw('task_id, SUM(tracked) as tracked')
            ->groupBy('task_id');
    }

    public function testertimeSpent()
    {
        return $this->hasOne(
            \App\Hubstaff\HubstaffActivity::class,
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
        return $this->hasMany(ChatMessage::class, 'developer_task_id', 'id')->orderBy('id', 'desc');
    }

    public function scopeNotEstimated($query)
    {
        return $query->whereNull('estimate_minutes')
            ->where('estimate_date', '0000-00-00');
    }

    public function scopeEstimated($query)
    {
        return $query->whereNotNull('estimate_minutes');
    }

    public function scopeAdminNotApproved($query)
    {
        return $query->join('developer_tasks_history', 'developer_tasks_history.developer_task_id', 'developer_tasks.id')
            ->estimated()
            ->where('attribute', 'estimation_minute')
            ->where('model', \App\DeveloperTask::class)
            ->where('is_approved', '0');
    }

    public function developerTaskHistory()
    {
        return  $this->hasOne(DeveloperTaskHistory::class, 'developer_task_id', 'id');
    }

    public function ApprovedDeveloperTaskHistory()
    {
        return  $this->hasOne(DeveloperTaskHistory::class, 'developer_task_id', 'id')->where('is_approved', 1);
    }

    public function updateHistory($type, $oldValue, $newValue)
    {
        if ($oldValue != $newValue) {
            DeveloperTaskHistory::create([
                'developer_task_id' => $this->id,
                'model' => \App\DeveloperTask::class,
                'attribute' => $type,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'user_id' => \Auth::id(),
            ]);
        }
    }

    public function updateStartDate($new)
    {
        $type = 'start_date';
        $old = $this->start_date;

        if (isset($this->estimate_date) && $this->estimate_date != '0000-00-00 00:00:00' && isset($new)) {
            $newStartDate = Carbon::parse($new);
            $estimateDate = Carbon::parse($this->estimate_date);
            if ($newStartDate->gte($estimateDate)) {
                throw new Exception('Estimate start date time must be less then Estimate end date time.');
            }
        }
        $count = DeveloperTaskHistory::query()
            ->where('model', \App\DeveloperTask::class)
            ->where('attribute', $type)
            ->where('developer_task_id', $this->id)
            ->count();
        if ($count) {
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 0);
        } else {
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 1);
        }

        unset($this->attributes['estimate_date']); // Unset Assigned value
        $this->start_date = $new;
        $this->save();
    }

    public function updateEstimateDate($new)
    {
        $type = 'estimate_date';
        $old = $this->estimate_date;

        if (isset($this->start_date) && $this->start_date != '0000-00-00 00:00:00' && isset($new)) {
            $startDate = Carbon::parse($this->start_date);
            $newEstimateDate = Carbon::parse($new);
            if ($newEstimateDate->lte($startDate)) {
                throw new Exception('Estimate end date time must be greater then Estimate start date time.');
            }
        }

        $count = DeveloperTaskHistory::query()
            ->where('model', \App\DeveloperTask::class)
            ->where('attribute', $type)
            ->where('developer_task_id', $this->id)
            ->count();
        if ($count) {
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 0);
        } else {
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 1);
        }

        $this->estimate_date = $new;
        $this->save();
    }

    public function updateEstimateDueDate($new)
    {
        $type = 'due_date';
        $old = $this->due_date;

        $count = DeveloperTaskHistory::query()
            ->where('model', \App\DeveloperTask::class)
            ->where('attribute', $type)
            ->where('developer_task_id', $this->id)
            ->count();
        if ($count) {
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 0);
        } else {
            $this->due_date = $new;
            $this->save();
            DeveloperTaskHistory::historySave($this->id, $type, $old, $new, 1);
        }
    }

    public static function getMessagePrefix($obj)
    {
        return '#DEVTASK-' . $obj->id . '-' . $obj->subject . ' => ';
    }

    public function taskStatus()
    {
        return $this->hasOne(TaskStatus::class, 'name', 'status');
    }
}
