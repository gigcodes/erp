<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\WhatsAppGroup;
use App\Hubstaff\HubstaffMember;
use App\Task;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Plank\Mediable\Mediable;
use Auth;
use Illuminate\Http\Request;
use App\Models\Tasks\TaskHistoryForStartDate;
use App\Models\Tasks\TaskHistoryForCost;


class Task extends Model {
    /**
     * @var string
     * @SWG\Property(property="category",type="string")
     * @SWG\Property(property="task_details",type="string")
     * @SWG\Property(property="task_subject",type="string")
     * @SWG\Property(property="completion_date",type="datetime")
     * @SWG\Property(property="assign_from",type="datetime")
     * @SWG\Property(property="assign_to",type="datetime")
     * @SWG\Property(property="is_statutory",type="boolean")
     * @SWG\Property(property="sending_time",type="string")
     * @SWG\Property(property="recurring_type",type="string")
     * @SWG\Property(property="statutory_id",type="integer")
     * @SWG\Property(property="model_type",type="string")
     * @SWG\Property(property="model_id",type="integer")
     * @SWG\Property(property="general_category_id",type="integer")
     
     * @SWG\Property(property="cost",type="string")
     * @SWG\Property(property="is_milestone",type="boolean")
     * @SWG\Property(property="no_of_milestone",type="string")
     * @SWG\Property(property="milestone_completed",type="string")
     * @SWG\Property(property="customer_id",type="integer")
     * @SWG\Property(property="hubstaff_task_id",type="integer")
     * @SWG\Property(property="master_user_id",type="integer")
     * @SWG\Property(property="lead_hubstaff_task_id",type="integer")
     * @SWG\Property(property="due_date",type="datetime")
     * @SWG\Property(property="site_developement_id",type="integer")
     * @SWG\Property(property="priority_no",type="integer")
     */

    use SoftDeletes;
    use Mediable;
    protected $fillable = [
        'category',
        'task_details',
        'task_subject',
        'start_date',
        'completion_date',
        'assign_from',
        'assign_to',
        'is_statutory',
        'actual_start_date',
        'is_completed',
        'sending_time',
        'recurring_type',
        'statutory_id',
        'model_type',
        'model_id',
        'general_category_id',
        'cost',
        'is_milestone',
        'no_of_milestone',
        'milestone_completed',
        'customer_id',
        'hubstaff_task_id',
        'master_user_id',
        'lead_hubstaff_task_id',
        'due_date',
        'site_developement_id',
        'priority_no',
        'frequency',
        'message',
        'reminder_from',
        'reminder_last_reply',
        'last_send_reminder',

        'parent_task_id',

        'last_date_time_reminder',
        'is_flow_task'

    ];

    const TASK_TYPES = [
        "Other Task",
        "Statutory Task",
        "Calendar Task",
        "Discussion Task",
        "Developer Task",
        "Developer Issue",
    ];

    protected $dates = ['deleted_at'];

    public static function hasremark($id) {
        $task = Task::find($id);
        if (!empty($task->remark)) {
            return true;
        } else {
            return false;
        }
    }

    // getting remarks
    public static function getremarks($taskid) {
        $results = DB::select('select * from remarks where taskid = :taskid order by created_at DESC', ['taskid' => $taskid]);
        return json_decode(json_encode($results), true);
    }

    public function remarks() {
        return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task')->latest();
    }

    public function notes() {
        return $this->hasMany('App\Remark', 'taskid')->where('module_type', 'task-note')->latest();
    }

    public function users() {
        return $this->belongsToMany('App\User', 'task_users', 'task_id', 'user_id')->where('type', 'App\User');
    }

    public function assignedTo() {
        return $this->belongsTo('App\User', 'assign_to', 'id');
    }

    public function contacts() {
        return $this->belongsToMany('App\Contact', 'task_users', 'task_id', 'user_id')->where('type', 'App\Contact');
    }

    public function whatsappgroup() {
        return $this->hasOne(WhatsAppGroup::class);
    }

    public function whatsappAll($needBroadCast = false) {
        if ($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'task_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany('App\ChatMessage', 'task_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function allMessages() {
        return $this->hasMany(ChatMessage::class, 'task_id', 'id')->orderBy('id', 'desc');
    }
    public function customer() {
        return $this->belongsTo('App\Customer', 'customer_id', 'id');
    }

    public function timeSpent() {
        return $this->hasOne(
            'App\Hubstaff\HubstaffActivity',
            'task_id',
            'hubstaff_task_id'
        )
            ->selectRaw('task_id, SUM(tracked) as tracked')
            ->groupBy('task_id');
    }

    public function taskStatus() {
        return $this->hasOne(
            'App\taskStatus',
            'id',
            'status'
        );
    }

    public function createTaskFromSortcuts($request) {
        $created = 0;
        $message = '';
        $assignedUserId = 0;

        if (isset($request['task_asssigned_from'])) {
            $data["assign_from"] = $request['task_asssigned_from'];
        } else {
            $data['assign_from'] = Auth::id();
        }

        $data['status'] = 3;
        $task = 0;
        $taskType = $request['task_type'];

        if (isset($request['parent_task_id'])) {
            $data["parent_task_id"] = $request['parent_task_id'];
        }

        if ($taskType == "4" || $taskType == "5" || $taskType == "6") {
        } else {
            if (isset($data["is_flow_task"])) {
                $data["is_flow_task"] = $data["is_flow_task"];
            } else {
                $data["is_flow_task"] = 1;
            }
            if ($request['task_asssigned_to']) {
                if (is_array($request['task_asssigned_to'])) {
                    $data["assign_to"] = $request['task_asssigned_to'];
                } else {
                    $data["assign_to"] = $request['task_asssigned_to'];
                }
            } else {
                $data['assign_to'] = $request['assign_to_contacts'];
            }
            //discussion task

            $data['is_statutory'] = $request["task_type"];
            $data['task_details'] = $request["task_detail"];
            $data['task_subject'] = $request["task_subject"];
            $data["customer_id"]    = $request["customer_id"];
            $data["site_developement_id"]   = $request['site_id'];
            $data["cost"]   = $request["cost"];
            if ($request['category_id'] != null) {
                $data['category']     = $request['category_id'];
            }
            $task = Task::create($data);
            $created = 1;
            $assignedUserId = $task->assign_to;
            if ($task->is_statutory != 1) {
                $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
            } else {
                $message = $task->task_subject . ". " . $task->task_details;
            }

            $params = [
                'number'       => null,
                'user_id'      => $data['assign_from'],
                'approved'     => 1,
                'status'       => 2,
                'task_id'      => $task->id,
                'message'      => $message
            ];

            if (count($task->users) > 0) {
                if ($task->assign_from == Auth::id()) {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $user->id;
                        } else {
                            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                } else {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $task->assign_from;
                        } else {
                            if ($user->id != Auth::id()) {
                                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                            }
                        }
                    }
                }
            }

            if (count($task->contacts) > 0) {
                foreach ($task->contacts as $key => $contact) {
                    if ($key == 0) {
                        $params['contact_id'] = $task->assign_to;
                    } else {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, null, $params['message']);
                    }
                }
            }

            $chat_message = ChatMessage::create($params);
            ChatMessagesQuickData::updateOrCreate([
                'model' => \App\Task::class,
                'model_id' => $params['task_id']
            ], [
                'last_communicated_message' => @$params['message'],
                'last_communicated_message_at' => $chat_message->created_at,
                'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
            ]);

            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);
            app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);
        }


        if ($created) {
            // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
            $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

            $assignedUser = HubstaffMember::where('user_id', $assignedUserId)->first();

            $hubstaffUserId = null;
            $hubstaffTaskId = null;
            if ($assignedUser) {
                $hubstaffUserId = $assignedUser->hubstaff_user_id;
            }
            $taskSummery = substr($message, 0, 200);
            if ($hubstaffUserId) {
                $hubstaffTaskId = app('App\Http\Controllers\TaskModuleController')->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            }

            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();
            }
            if ($hubstaffTaskId) {
                $hubtask = new HubstaffTask();
                $hubtask->hubstaff_task_id = $hubstaffTaskId;
                $hubtask->project_id = $hubstaff_project_id;
                $hubtask->hubstaff_project_id = $hubstaff_project_id;
                $hubtask->summary = $message;
                $hubtask->save();
            }
        }
        return $task;
        return response()->json(["code" => 200, "data" => [], "message" => "Your quick task has been created!"]);
    }

    public function site_development() {
        return $this->belongsTo('App\SiteDevelopment', 'site_developement_id', 'id');
    }

    public function updateStartDate($newValue){
        $oldValue = $this->start_date;
        if($oldValue != $newValue){
            $this->start_date = $newValue;
            $this->save();
            TaskHistoryForStartDate::create([
                'task_id' => $this->id,
                'task_type' => 'TASK',
                'updated_by' => Auth::id(),
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);
        }
    }

    // 
}
