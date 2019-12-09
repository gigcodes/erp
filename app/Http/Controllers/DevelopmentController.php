<?php

namespace App\Http\Controllers;

use App\Helpers\DevelopmentHelper;
use App\Setting;
use App\TaskAttachment;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

use App\TasksHistory;
use App\TaskTypes;
use App\DeveloperTask;
use App\DeveloperModule;
use App\DeveloperComment;
use App\DeveloperTaskComment;
use App\DeveloperCost;
use App\PushNotification;
use App\User;
use App\Helpers;
use App\Issue;
use Response;

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        //  $this->middleware( 'permission:developer-tasks', [ 'except' => [ 'issueCreate', 'issueStore', 'moduleStore' ] ] );
    }

    /*public function index_bkup(Request $request)
    {
        // Set required data
        $user = $request->user ?? Auth::id();
        $start = $request->range_start ? "$request->range_start 00:00" : '2018-01-01 00:00';
        $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->endOfWeek();
        $id = null;

        // Set initial variables
        $progressTasks = new DeveloperTask();
        $plannedTasks = new DeveloperTask();
        $completedTasks = new DeveloperTask();

        // For non-admins get tasks assigned to the user
        if (!Auth::user()->hasRole('Admin')) {
            $progressTasks = DeveloperTask::where('user_id', Auth::id());
            $plannedTasks = DeveloperTask::where('user_id', Auth::id());
            $completedTasks = DeveloperTask::where('user_id', Auth::id());
        }

        // Get tasks for specific user if you are admin
        if (Auth::user()->hasRole('Admin') && (int)$request->user > 0) {
            $progressTasks = DeveloperTask::where('user_id', $user);
            $plannedTasks = DeveloperTask::where('user_id', $user);
            $completedTasks = DeveloperTask::where('user_id', $user);
        }

        // Filter by date
        if ($request->get('range_start') != '') {
            $progressTasks = $progressTasks->whereBetween('created_at', [$start, $end]);
            $plannedTasks = $plannedTasks->whereBetween('created_at', [$start, $end]);
            $completedTasks = $completedTasks->whereBetween('created_at', [$start, $end]);
        }

        // Filter by ID
        if ($request->get('id')) {
            $progressTasks = $progressTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $plannedTasks = $plannedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $completedTasks = $completedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
        }

        // Get all data with user and messages
        $plannedTasks = $plannedTasks->where('status', 'Planned')->orderBy('created_at')->with(['user', 'messages'])->get();
        $completedTasks = $completedTasks->where('status', 'Done')->orderBy('created_at')->with(['user', 'messages'])->get();
        $progressTasks = $progressTasks->where('status', 'In Progress')->orderBy('created_at')->with(['user', 'messages'])->get();

        // Get all modules
        $modules = DeveloperModule::all();

        // Get all developers
        $users = Helpers::getUserArray(User::role('Developer')->get());

        // Get all task types
        $tasksTypes = TaskTypes::all();

        // Create empty array for module names
        $moduleNames = [];

        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[ $module->id ] = $module->name;
        }

        $times = [];

        $priority  = \App\ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        return view('development.index', [
            'times' => $times,
            'users' => $users,
            'modules' => $modules,
            'user' => $user,
            'start' => $start,
            'end' => $end,
            'moduleNames' => $moduleNames,
            'completedTasks' => $completedTasks,
            'plannedTasks' => $plannedTasks,
            'progressTasks' => $progressTasks,
            'tasksTypes' => $tasksTypes,
            'priority' => $priority,
        ]);
    }*/

    public function taskListByUserId(Request $request)
    {
        $user_id = $request->get('user_id' , 0);

        $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                        ->leftJoin('erp_priorities', function($query){
                            $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                            $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                        })
                        ->where('user_id', $user_id)
                        ->where('status', '!=', 'Done');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->whereIn('developer_tasks.id', $request->get('selected_issue' , []));
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->orderBy('erp_priorities.id')->get();

        foreach ($issues as &$value) {
            $value->module = $value->developerModule->name;
            $value->created_by = User::where('id', $value->created_by)->value('name');
        }
        unset($value);

        return response()->json($issues);
    }

    public function setTaskPriority(Request $request)
    {
        $priority = $request->get('priority', null);
        //get all user task
        $developerTask = DeveloperTask::where('user_id', $request->get('user_id', 0))->pluck('id')->toArray();

        //delete old priority
        \App\ErpPriority::whereIn('model_id', $developerTask)->where('model_type', '=', DeveloperTask::class)->delete();

        if (!empty($priority)) {
            foreach ((array)$priority as $model_id) {
                \App\ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class
                ]);
            }

            $developerTask = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                        ->join('erp_priorities', function($query){
                            $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                            $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                        })
                        ->where('user_id',  $request->get('user_id', 0))
                        ->where('status', '!=', 'Done')
                        ->orderBy('erp_priorities.id')
                        ->get();

            $message = "";
            $i = 1;
            foreach ($developerTask as $value) {
                $message .= $i ." : #Task-" . $value->id . "-" . $value->subject."\n";
                $i++;
            }
            if (!empty($message)) {
                $requestData = new Request();
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = "";

                if(!empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes')."\n";
                }

                $string .= "Task Priority is : \n".$message;

                $params['message'] = $string;
                $params['status'] = 2;
                $requestData->request->add($params);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'priority');
            }
        }
        return response()->json([
            'status' => 'success'
        ]);
    }

    public function index(Request $request)
    {
//        //$this->issueTaskIndex( $request,'task');
//        return Redirect::to('/development/list/task');

        // Set required data
        $user = $request->user ?? Auth::id();
        $start = $request->range_start ? "$request->range_start 00:00" : '2018-01-01 00:00';
        $end = $request->range_end ? "$request->range_end 23:59" : Carbon::now()->endOfWeek();
        $id = null;

        // Set initial variables
        $progressTasks = new DeveloperTask();
        $plannedTasks = new DeveloperTask();
        $completedTasks = new DeveloperTask();

        // For non-admins get tasks assigned to the user
        if (!Auth::user()->hasRole('Admin')) {
            $progressTasks = DeveloperTask::where('user_id', Auth::id());
            $plannedTasks = DeveloperTask::where('user_id', Auth::id());
            $completedTasks = DeveloperTask::where('user_id', Auth::id());
        }

        // Get tasks for specific user if you are admin
        if (Auth::user()->hasRole('Admin') && (int)$request->user > 0) {
            $progressTasks = DeveloperTask::where('user_id', $user);
            $plannedTasks = DeveloperTask::where('user_id', $user);
            $completedTasks = DeveloperTask::where('user_id', $user);
        }

        // Filter by date
        if ($request->get('range_start') != '') {
            $progressTasks = $progressTasks->whereBetween('created_at', [$start, $end]);
            $plannedTasks = $plannedTasks->whereBetween('created_at', [$start, $end]);
            $completedTasks = $completedTasks->whereBetween('created_at', [$start, $end]);
        }

        // Filter by ID
        if ($request->get('id')) {
            $progressTasks = $progressTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $plannedTasks = $plannedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
            $completedTasks = $completedTasks->where(function ($query) use ($request) {
                $id = $request->get('id');
                $query->where('id', $id)->orWhere('subject', 'LIKE', "%$id%");
            });
        }

        // Get all data with user and messages
        $plannedTasks = $plannedTasks->where('status', 'Planned')->orderBy('created_at')->with(['user', 'messages'])->get();
        $completedTasks = $completedTasks->where('status', 'Done')->orderBy('created_at')->with(['user', 'messages'])->get();
        $progressTasks = $progressTasks->where('status', 'In Progress')->orderBy('created_at')->with(['user', 'messages'])->get();

        // Get all modules
        $modules = DeveloperModule::all();

        // Get all developers
        $users = Helpers::getUserArray(User::role('Developer')->get());

        // Get all task types
        $tasksTypes = TaskTypes::all();

        // Create empty array for module names
        $moduleNames = [];

        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[ $module->id ] = $module->name;
        }

        $times = [];
        return view('development.index', [
            'times' => $times,
            'users' => $users,
            'modules' => $modules,
            'user' => $user,
            'start' => $start,
            'end' => $end,
            'moduleNames' => $moduleNames,
            'completedTasks' => $completedTasks,
            'plannedTasks' => $plannedTasks,
            'progressTasks' => $progressTasks,
            'tasksTypes' => $tasksTypes,
            'title' => 'Dev'
        ]);
    }

    public function moveTaskToProgress(Request $request)
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $date = $request->get('date');
        $task->status = 'In Progress';
        $hour = $request->get('hour') ?? '00';
        $minutes = $request->get('mimutes') ?? '00';
        $task->estimate_time = $date . ' ' . "$hour:$minutes:00 ";
        $task->start_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function completeTask(Request $request)
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Done';
        $task->end_time = Carbon::now()->toDateTimeString();
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function relistTask(Request $request)
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $task->status = 'Planned';
        $task->end_time = null;
        $task->start_time = null;
        $task->estimate_time = null;
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function updateAssignee(Request $request)
    {
        $task = DeveloperTask::find($request->get('task_id'));
        $old_assignee = $task->user_id;
        $task->user_id = $request->get('user_id');
        $task->save();

        $task_history = new TasksHistory;
        $task_history->date_time = date('Y-m-d H:i:s');
        $task_history->task_id = $request->get('task_id');
        $task_history->user_id = Auth::id();
        $task_history->old_assignee = $old_assignee;
        $task_history->new_assignee = $request->get('user_id');
        $task_history->save();
        return response()->json([
            'success'
        ]);
    }

    public function issueTaskIndex(Request $request, $type)
    {
        if ($type == 'issue') {
            $issues = DeveloperTask::with(["developerModule","assignedUser","responsibleUser","submitter"])->where('task_type_id', '3');
        } else {
            $issues = DeveloperTask::with(["developerModule","assignedUser","responsibleUser","submitter"])->where('task_type_id', '1');
        }

        if ((int)$request->get('submitted_by') > 0) {
            $issues = $issues->where('created_by', $request->get('submitted_by'));
        }
        if ((int)$request->get('responsible_user') > 0) {
            $issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }

        if ((int)$request->get('corrected_by') > 0) {
            $issues = $issues->where('user_id', $request->get('corrected_by'));
        }

        if ($request->get('module')) {
            $issues = $issues->where('module_id', $request->get('module'));
        }

        if ($request->get('task_status')) {
            $issues = $issues->where('status', $request->get('task_status'));
        }



        if ($request->get('subject') != '') {
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%");
            });
        }

        $modules = DeveloperModule::all();
        $users = Helpers::getUserArray(User::all());

        // Hide resolved
        if ((int)$request->show_resolved !== 1) {
            $issues = $issues->where('is_resolved', 0);
        }

        // Sort
        if ($request->order == 'create') {
            $issues = $issues->orderBy('created_at', 'DESC');
        } else {
            $issues = $issues->orderBy('priority', 'ASC')->orderBy('created_at', 'DESC');
        }

        $issues = $issues->paginate(Setting::get('pagination'));

        $priority  = \App\ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        return view('development.issue', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $type,
            'priority' => $priority
        ]);


    }

    public function issueIndex(Request $request)
    {
        $issues = new Issue;

        if ((int)$request->get('submitted_by') > 0) {
            $issues = $issues->where('submitted_by', $request->get('submitted_by'));
        }
        if ((int)$request->get('responsible_user') > 0) {
            $issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }

        if ((int)$request->get('corrected_by') > 0) {
            $issues = $issues->where('user_id', $request->get('corrected_by'));
        }

        if ($request->get('module')) {
            $issues = $issues->where('module', $request->get('module'));
        }

        if ($request->get('subject') != '') {
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%");
            });
        }

        $modules = DeveloperModule::all();
        $users = Helpers::getUserArray(User::all());

        // Hide resolved
        if ((int)$request->show_resolved !== 1) {
            $issues = $issues->where('is_resolved', 0);
        }

        // Sort
        if ($request->order == 'create') {
            $issues = $issues->orderBy('created_at', 'DESC')->with('communications')->get();
        } else {
            $issues = $issues->orderBy('priority', 'ASC')->orderBy('created_at', 'DESC')->with('communications')->get();
        }

        $priority  = \App\ErpPriority::where('model_type', '=', Issue::class)->pluck('model_id')->toArray();

        return view('development.issue', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => 'Issue',
            'priority' => $priority,
        ]);
    }

    public function listByUserId(Request $request)
    {
        $user_id = $request->get('user_id' , 0);

        $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module','developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                        ->leftJoin('erp_priorities', function($query){
                            $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                            $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                        })
                        ->where('responsible_user_id', $user_id)
                        ->where('is_resolved', '0');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->whereIn('developer_tasks.id', $request->get('selected_issue' , []));
        }  else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->orderBy('erp_priorities.id')->get();

        foreach ($issues as &$value) {
            $value->module = $value->developerModule ? $value->developerModule->name : 'Not Specified';
            $value->submitted_by = ($value->submitter) ? $value->submitter->name : "";
        }
        unset($value);

        return response()->json($issues);
    }

    public function setPriority(Request $request)
    {
        $priority = $request->get('priority', null);
        //get all user task
        $issues = DeveloperTask::where('responsible_user_id', $request->get('user_id', 0))->pluck('id')->toArray();

        //delete old priority
        \App\ErpPriority::whereIn('model_id', $issues)->where('model_type', '=', DeveloperTask::class)->delete();

        if (!empty($priority)) {
            foreach ((array)$priority as $model_id) {
                \App\ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class
                ]);
            }

            $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                            ->join('erp_priorities', function($query){
                                $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                                $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                            })
                            ->where('responsible_user_id', $request->get('user_id', 0))
                            ->where('is_resolved', '0')
                            ->orderBy('erp_priorities.id')
                            ->get();

            $message = '';

            $i = 1;
            foreach ($issues as $value) {
                $message .= $i ." : #ISSUE-" . $value->id . "-" . $value->subject."\n";
                $i++;
            }

            if (!empty($message)) {
                $requestData = new Request();
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = "";

                if(!empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes')."\n";
                }

                $string .= "Issue Priority is : \n".$message;

                $params['message'] = $string;
                $params['status'] = 2;
                $requestData->request->add($params);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'priority');
            }
        }
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function issueCreate()
    {
        return view('development.issue-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'priority' => 'required|integer',
            'subject' => 'sometimes|nullable|string',
            'task' => 'required|string|min:3',
            'cost' => 'sometimes|nullable|integer',
            'status' => 'required'
        ]);

        $data = $request->except('_token');
        $data[ 'user_id' ]      = $request->user_id ? $request->user_id : Auth::id();
        $data[ 'created_by' ]   = Auth::id();
        //$data[ 'submitted_by' ] = Auth::id();


        $module = $request->get('module_id');
        if (!empty($module)) {
            $module = DeveloperModule::find($module);
            if (!$module) {
                $module = new DeveloperModule();
                $module->name = $request->get('module_id');
                $module->save();
                $data[ 'module_id' ] = $module->id;
            }
        }

        $task = DeveloperTask::create($data);

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('developertask/' . floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }

        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add(['issue_id' => $task->id, 'message' => $request->input('task'), 'status' => 1]);

        app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
        // if ($task->status == 'Done') {
        //   NotificationQueueController::createNewNotification([
        //     'message' => 'New Task to Verify',
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => DeveloperTask::class,
        //     'model_id' =>  $task->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => 6,
        //     'role' => '',
        //   ]);
        //
        //   NotificationQueueController::createNewNotification([
        //     'message' => 'New Task to Verify',
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => DeveloperTask::class,
        //     'model_id' =>  $task->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => 56,
        //     'role' => '',
        //   ]);
        // }

        if ($request->ajax()) {
            return response()->json(['task' => $task]);
        }

        return redirect(url('development/list/devtask'))->with('success', 'You have successfully added task!');
    }

    public function issueStore(Request $request)
    {

        $this->validate($request, [
            'priority' => 'required|integer',
            'issue' => 'required|min:3'
        ]);

        $data = $request->except('_token');

        $module = $request->get('module');

        $module = DeveloperModule::find($module);
        if (!$module) {
            $module = new DeveloperModule();
            $module->name = $request->get('module');
            $module->save();
            $data[ 'module' ] = $module->id;
        }

        //$issue = Issue::create($data);
        $task = new DeveloperTask;
        $task->priority = $request->input('priority');
        $task->subject = $request->input('subject');
        $task->task = $request->input('issue');
        $task->responsible_user_id = $request->get('responsible_user_id',0);
        $task->module_id = $module->id;
        $task->user_id = Auth::id();
        $task->status = 'Issue';
        $task->task_type_id = 3;

        $task->save();


        //$issue->submitted_by = Auth::user()->id;
        //$issue->save();

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('issue/' . floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }

        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add(['issue_id' => $task->id, 'message' => $request->input('issue'), 'status' => 1]);

        app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');

        return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function moduleStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3'
        ]);

        $data = $request->except('_token');

        DeveloperModule::create($data);

        return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function commentStore(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string|min:3'
        ]);

        $data = $request->except('_token');
        $data[ 'user_id' ] = Auth::id();

        DeveloperComment::create($data);

        return redirect()->back()->with('success', 'You have successfully wrote a comment!');
    }

    public function costStore(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'paid_date' => 'required'
        ]);

        $data = $request->except('_token');

        DeveloperCost::create($data);

        return redirect()->back()->with('success', 'You have successfully added payment!');
    }

    public function awaitingResponse(Request $request, $id)
    {
        $comment = DeveloperComment::find($id);
        $comment->status = 1;
        $comment->save();

        return response('success');
    }

    public function issueAssign(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required|integer'
        ]);

        $issue = Issue::find($id);
        $task = new DeveloperTask;

        $task->priority = $issue->priority;
        $task->task = $issue->issue;
        $task->user_id = $request->user_id;
        $task->status = 'Planned';

        $task->save();

        foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
            $task->attachMedia($image, config('constants.media_tags'));
        }

        $issue->user_id = $request->user_id;
        $issue->save();
        $issue->delete();

        return redirect()->back()->with('success', 'You have successfully assigned the issue!');
    }

    public function moduleAssign(Request $request, $id)
    {
        $this->validate($request, [
            'user_id' => 'required|integer'
        ]);

        $module = DeveloperTask::find($id);

        $module->user_id = $request->user_id;
        $module->module = 0;

        $module->save();

        return redirect()->route('development.index')->with('success', 'You have successfully assigned the module!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'priority' => 'required|integer',
            'task' => 'required|string|min:3',
            'cost' => 'sometimes||nullable|integer',
            'status' => 'required'
        ]);

        $data = $request->except('_token');
        $data[ 'user_id' ] = $request->user_id ? $request->user_id : Auth::id();

        $task = DeveloperTask::find($id);
        $task->update($data);

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $media = MediaUploader::fromSource($image)
                    ->toDirectory('developertask/' . floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }

        return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
    }

    public function updateCost(Request $request, $id)
    {
        $task = DeveloperTask::find($id);

        if ($task->user_id == Auth::id()) {
            $task->cost = $request->cost;
            $task->save();
        }

        return response('success');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->status = $request->status;

        if ($request->status == 'In Progress') {
            $task->start_time = Carbon::now();
        }

        if ($request->status == 'Done') {
            $task->end_time = Carbon::now();
        }

        $task->save();

        // if ($task->status == 'Done' && $task->completed == 0) {
        //   NotificationQueueController::createNewNotification([
        //     'message' => 'New Task to Verify',
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => DeveloperTask::class,
        //     'model_id' =>  $task->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => 6,
        //     'role' => '',
        //   ]);
        //
        //   NotificationQueueController::createNewNotification([
        //     'message' => 'New Task to Verify',
        //     'timestamps' => ['+0 minutes'],
        //     'model_type' => DeveloperTask::class,
        //     'model_id' =>  $task->id,
        //     'user_id' => Auth::id(),
        //     'sent_to' => 56,
        //     'role' => '',
        //   ]);
        // }

        return response('success');
    }

    public function updateTask(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->task = $request->task;
        $task->save();

        return response('success');
    }

    public function updatePriority(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->priority = $request->priority;
        $task->save();

        return response()->json([
            'priority' => $task->priority
        ]);
    }

    public function verify(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->completed = 1;
        $task->save();

        $notifications = PushNotification::where('model_type', 'App\DeveloperTask')->where('model_id', $task->id)->where('isread', 0)->get();

        foreach ($notifications as $notification) {
            $notification->isread = 1;
            $notification->save();
        }

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('development.index')->with('success', 'You have successfully verified the task!');
    }

    public function verifyView(Request $request)
    {
        $task = DeveloperTask::find($request->id);

        PushNotification::where('model_type', 'App\DeveloperTask')->where('model_id', $request->id)->delete();

        if ($request->tab) {
            $message = 'New Task to Verify';

            // NotificationQueueController::createNewNotification([
            //   'message' => $message,
            //   'timestamps' => ['+10 minutes'],
            //   'model_type' => DeveloperTask::class,
            //   'model_id' =>  $task->id,
            //   'user_id' => Auth::id(),
            //   'sent_to' => 6,
            //   'role' => '',
            // ]);
            //
            // NotificationQueueController::createNewNotification([
            //   'message' => $message,
            //   'timestamps' => ['+10 minutes'],
            //   'model_type' => DeveloperTask::class,
            //   'model_id' =>  $task->id,
            //   'user_id' => Auth::id(),
            //   'sent_to' => 56,
            //   'role' => '',
            // ]);

            return redirect(url("/development#task_$request->id"));
        } else {
            $message = 'New Task Remark';

            if ($request->user == Auth::id()) {
                // NotificationQueueController::createNewNotification([
                //   'message' => $message,
                //   'timestamps' => ['+10 minutes'],
                //   'model_type' => DeveloperTask::class,
                //   'model_id' =>  $task->id,
                //   'user_id' => Auth::id(),
                //   'sent_to' => 6,
                //   'role' => '',
                // ]);
                //
                // NotificationQueueController::createNewNotification([
                //   'message' => $message,
                //   'timestamps' => ['+10 minutes'],
                //   'model_type' => DeveloperTask::class,
                //   'model_id' =>  $task->id,
                //   'user_id' => Auth::id(),
                //   'sent_to' => 56,
                //   'role' => '',
                // ]);
            } else {
                // NotificationQueueController::createNewNotification([
                //   'message' => $message,
                //   'timestamps' => ['+10 minutes'],
                //   'model_type' => DeveloperTask::class,
                //   'model_id' =>  $task->id,
                //   'user_id' => Auth::id(),
                //   'sent_to' => $request->user,
                //   'role' => '',
                // ]);
            }

            return redirect(url("/development?user=$request->user#task_$task->id"));

            // if ($task->status == 'Done' && $task->completed == 1) {
            // } elseif ($task->status == 'Done' && $task->completed == 0) {
            //   return redirect(url("/development#task_$request->id"));
            // } else {
            //   return redirect(url("/development#task_$task->id"));
            // }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $task = DeveloperTask::find($id);
        $task->development_details()->delete();
        $task->delete();

        if ($request->ajax()) {
            return response('success');
        }

        return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id)
    {
        DeveloperTask::find($id)->delete();

        return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }

    public function moduleDestroy($id)
    {
        $module = DeveloperModule::find($id);

        foreach ($module->tasks as $task) {
            $task->module_id = '';
            $task->save();
        }

        $module->delete();

        return redirect()->route('development.index')->with('success', 'You have successfully archived the module!');
    }

    public function assignUser(Request $request)
    {
        // $issue = Issue::find($request->get('issue_id'));
        $issue = DeveloperTask::find($request->get('issue_id'));
        $issue->user_id = $request->get('user_id');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function assignResponsibleUser(Request $request)
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        //$issue = Issue::find($request->get('issue_id'));
        $issue->responsible_user_id = $request->get('responsible_user_id');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function saveAmount(Request $request)
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        $issue->cost = $request->get('cost');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function resolveIssue(Request $request)
    {

        $issue = DeveloperTask::find($request->get('issue_id'));
        //$issue = Issue::find($request->get('issue_id'));
        //$issue->is_resolved = $request->get('is_resolved');
        $issue->status = $request->get('is_resolved');
        if(strtolower($request->get('is_resolved')) == "done") {
            $issue->is_resolved = 1;
        }

        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function saveEstimateTime(Request $request)
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        //$issue = Issue::find($request->get('issue_id'));
        $issue->estimate_time = $request->get('estimate_time');
        $issue->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function updateValues(Request $request)
    {
        $task = DeveloperTask::find($request->get('id'));
        $type = $request->get('type');
        $value = $request->get('value');
        if ($type == 'start_date') {
            $task->start_date = $request->get('value');
        } else {
            if ($type == 'end_date') {
                $task->end_date = $request->get('value');
            } else {
                if ($type == 'estimate_date') {
                    $task->estimate_date = $request->get('value');
                } else {
                    if ($type == 'cost') {
                        $task->cost = $request->get('value');
                    } else {
                        if ($type == 'module') {
                            $task->module_id = $request->get('value');
                        }
                    }
                }
            }
        }
        $task->save();

        return response()->json([
            'status' => 'success'
        ]);

    }

    public function overview(Request $request)
    {
        // Get status
        $status = $request->get('status');
        if (empty($status)) {
            $status = 'In Progress';
        }
        $task_type = 1;
        $taskTypes = TaskTypes::all();
        $users = Helpers::getUsersByRoleName('Developer');

        if (!empty($request->get('task_type'))) {
            $task_type = $request->get('task_type');
            //$issues = $issues->where('submitted_by', $request->get('submitted_by'));
        }
        if (!empty($request->get('task_status'))) {
            $status = $request->get('task_status');
            //$issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }
        if (!empty($request->get('task_type')) && !empty($request->get('task_status'))) {
            $status = $request->get('task_status');
            $task_type = $request->get('task_type');
        }

        return view('development.overview', [
            'taskTypes' => $taskTypes,
            'users' => $users,
            'status' => $status,
            'task_type' => $task_type,
        ]);
    }

    public function taskDetail($taskId)
    {
        // Get tasks
        $task = DeveloperTask::where('developer_tasks.id', $taskId)
            ->select('developer_tasks.*', 'task_types.name as task_type', 'users.name as username', 'u.name as reporter')
            ->leftjoin('task_types', 'task_types.id', '=', 'developer_tasks.task_type_id')
            ->leftjoin('users', 'users.id', '=', 'developer_tasks.user_id')
            ->leftjoin('users AS u', 'u.id', '=', 'developer_tasks.created_by')
            ->first();

        // Get subtasks
        $subtasks = DeveloperTask::where('developer_tasks.parent_id', $taskId)->get();

        // Get comments
        $comments = DeveloperTaskComment::where('task_id', $taskId)
            ->join('users', 'users.id', '=', 'developer_task_comments.user_id')
            ->get();

        //Get Attachments
        $attachments = TaskAttachment::where('task_id', $taskId)->get();
        $developers = Helpers::getUserArray(User::role('Developer')->get());

        // Return view
        return view('development.task_detail', [
            'task' => $task,
            'subtasks' => $subtasks,
            'comments' => $comments,
            'developers' => $developers,
            'attachments' => $attachments,
        ]);
    }

    public function taskComment(Request $request)
    {
        $response = array();
        $this->validate($request, [
            'comment' => 'required|string|min:1'
        ]);

        $data = $request->except('_token');
        $data[ 'user_id' ] = Auth::id();

        $created = DeveloperTaskComment::create($data);
        if ($created) {
            $response[ 'status' ] = 'ok';
            $response[ 'msg' ] = 'Comment stored successfully';
            echo json_encode($response);
        } else {
            $response[ 'status' ] = 'error';
            $response[ 'msg' ] = 'Error';
        }
    }

    public function changeTaskStatus(Request $request)
    {

        if (!empty($request->input('task_id'))) {

            $task = DeveloperTask::find($request->input('task_id'));
            $task->status = $request->input('status');
            $task->save();

            return response()->json(['success']);
        }
    }

    public function makeDirectory($path, $mode = 0777, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        } else {
            return mkdir($path, $mode, $recursive);
        }
    }

    public function uploadAttachDocuments(Request $request)
    {
        $task_id = $request->input('task_id');
        $task = DeveloperTask::find($task_id);
        if ($request->hasfile('attached_document')) {
            foreach ($request->file('attached_document') as $image) {
                $name = time() . '_' . $image->getClientOriginalName();
                $new_id = floor($task_id / 1000);
//                $path = public_path().'/developer-task' . $task_id;
//                if (!file_exists($path)) {
//                    $this->makeDirectory($path);
//                }

                $dirname = public_path() . '/uploads/developer-task/' . $new_id;
                if (file_exists($dirname)) {
                    $dirname2 = public_path() . '/uploads/developer-task/' . $new_id . '/' . $task_id;
                    if (file_exists($dirname2) == false) {
                        mkdir($dirname2, 0777);
                    }
                } else {
                    mkdir($dirname, 0777);
                }

                $media = MediaUploader::fromSource($image)->toDirectory("developer-task/$new_id/$task_id")->upload();
                $task->attachMedia($media, config('constants.media_tags'));
            }
        }
        if (!empty($request->file('attached_document'))) {

            foreach ($request->file('attached_document') as $file) {

                $name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/task_files/'), $name);
                $filepath[] = 'images/task_files/' . $name;

                $task_attachment = new TaskAttachment;
                $task_attachment->task_id = $task_id;
                $task_attachment->name = $name;
                $task_attachment->save();
            }
            return redirect(url("/development/task-detail/$task_id"));
        } else {
            return redirect(url("/development/task-detail/$task_id"));
        }
    }

    public function downloadFile(Request $request)
    {
        $file_name = $request->input('file_name');
        //PDF file is stored under project/public/download/info.pdf
        $file = public_path() . "/images/task_files/" . $file_name;

        $ext = substr($file_name, strrpos($file_name, '.') + 1);

        $headers = array();
        if ($ext == 'pdf') {
            $headers = array(
                'Content-Type: application/pdf',
            );

            //$download_file = $file_name.'.pdf';
        }

        return Response::download($file, $file_name, $headers);
    }

//    public function downloadFile($path) {
//        if (file_exists($path) && is_file($path)) {
//            // file exist
//            header('Content-Description: File Transfer');
//            header('Content-Type: application/octet-stream');
//            header('Content-Disposition: attachment; filename=' . basename($path));
//            header('Content-Transfer-Encoding: binary');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($path));
//            set_time_limit(0);
//            @readfile($path);//"@" is an error control operator to suppress errors
//        } else {
//            // file doesn't exist
//            die('Error: The file ' . basename($path) . ' does not exist!');
//        }
//    }

    public function openNewTaskPopup(Request $request)
    {
        $status = "ok";
        // Get all developers
        //$users = Helpers::getUserArray(User::role('Developer')->get());
        $users = Helpers::getUsersByRoleName('Developer');
        // Get all task types
        $tasksTypes = TaskTypes::all();
        $moduleNames = [];
        // Get all modules
        $modules = DeveloperModule::all();
        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[ $module->id ] = $module->name;
        }

        $html = view('development.ajax.add_new_task', compact("users", "tasksTypes", "modules", "moduleNames"))->render();
        return json_encode(compact("html", "status"));
    }
}
