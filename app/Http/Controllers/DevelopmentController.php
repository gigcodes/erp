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
use App\Github\GithubRepository;
use App\PushNotification;
use App\User;
use App\Helpers;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffProject;
use App\Hubstaff\HubstaffTask;
use App\Issue;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Response;
use Storage;

define('SEED_REFRESH_TOKEN', getenv('HUBSTAFF_SEED_PERSONAL_TOKEN'));
define('HUBSTAFF_TOKEN_FILE_NAME', 'hubstaff_tokens.json');

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $githubClient;

    public function __construct()
    {
        //  $this->middleware( 'permission:developer-tasks', [ 'except' => [ 'issueCreate', 'issueStore', 'moduleStore' ] ] );
        $this->githubClient = new Client([
            'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
        ]);
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
        $user_id = $request->get('user_id', 0);
        $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
            ->leftJoin('erp_priorities', function ($query) {
                $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                $query->where('erp_priorities.user_id', $user_id);
            })
            ->where('status', '!=', 'Done');
        // if admin the can assign new task    
        if (auth()->user()->isAdmin()) {
            $issues = $issues->whereIn('developer_tasks.id', $request->get('selected_issue', []));
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
        $user_id = $request->get('user_id', 0);
        //get all user task
        //$developerTask = DeveloperTask::where('user_id', $request->get('user_id', 0))->pluck('id')->toArray();

        //delete old priority
        \App\ErpPriority::where("user_id",$user_id)->where('model_type', '=', DeveloperTask::class)->delete();

        if (!empty($priority)) {
            foreach ((array) $priority as $model_id) {
                \App\ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class,
                    'user_id' => $user_id
                ]);
            }
            $developerTask = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
                ->join('erp_priorities', function ($query) use($user_id) {
                    $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                    $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                    $query->where('erp_priorities.user_id', '=', $user_id);
                })
                ->where('is_resolved', '0')
                ->orderBy('erp_priorities.id')
                ->get();
            $message = "";
            $i = 1;
            foreach ($developerTask as $value) {
                $message .= $i . " : #Task-" . $value->id . "-" . $value->subject . "\n";
                $i++;
            }
            if (!empty($message)) {
                $requestData = new Request();
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = "";
                if (!empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes') . "\n";
                }
                $string .= "Task Priority is : \n" . $message;

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
        if (Auth::user()->hasRole('Admin') && (int) $request->user > 0) {
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
            $moduleNames[$module->id] = $module->name;
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
        //$request->request->add(["order" => $request->get("order","communication_desc")]);
        // Load issues
        $issues = DeveloperTask::where('developer_tasks.task_type_id', $type == 'issue' ? '3' : '1');

        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('developer_tasks.created_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('developer_tasks.responsible_user_id', $request->get('responsible_user'));
        }

        if ((int) $request->get('corrected_by') > 0) {
            $issues = $issues->where('developer_tasks.user_id', $request->get('corrected_by'));
        }

        if ((int) $request->get('assigned_to') > 0) {
            $issues = $issues->where('developer_tasks.assigned_to', $request->get('assigned_to'));
        }
        if ($request->get('module')) {
            $issues = $issues->where('developer_tasks.module_id', $request->get('module'));
        }
        if (!empty($request->get('task_status', []))) {
            $issues = $issues->whereIn('developer_tasks.status', $request->get('task_status'));
        }

        $whereCondition = "";
        if ($request->get('subject') != '') {
            $whereCondition = ' and message like  "%' . $request->get('subject') . '%"';
            $issues = $issues->where(function ($query) use ($request) {
                $subject = $request->get('subject');
                $query->where('developer_tasks.id', 'LIKE', "%$subject%")->orWhere('subject', 'LIKE', "%$subject%")->orWhere("task", "LIKE", "%$subject%")
                    ->orwhere("chat_messages.message", 'LIKE', "%$subject%");
            });
        }
        if ($request->get('language') != '') {
            $issues = $issues->where('language', 'LIKE', "%" . $request->get('language') . "%");
        }
        $issues = $issues->leftJoin(DB::raw('(SELECT MAX(id) as  max_id, issue_id  FROM `chat_messages` where issue_id > 0 ' . $whereCondition . ' GROUP BY issue_id ) m_max'), 'm_max.issue_id', '=', 'developer_tasks.id');
        $issues = $issues->leftJoin('chat_messages', 'chat_messages.id', '=', 'm_max.max_id');
        $issues = $issues->select("developer_tasks.*");

        // Set variables with modules and users
        $modules = DeveloperModule::all();
        $users = Helpers::getUserArray(User::all());
        $statusList = \DB::table("developer_tasks")->where("status", "!=", "")->groupBy("status")->select("status")->pluck("status", "status")->toArray();
        $statusList = array_merge([
            "" => "Select Status",
            "Planned" => "Planned",
            "In Progress" => "In Progress",
            "Done" => "Done"
        ], $statusList);

        // Hide resolved
        /*if ((int)$request->show_resolved !== 1) {
            $issues = $issues->where('is_resolved', 0);
        }*/

        if (!auth()->user()->isAdmin()) {
            $issues = $issues->where(function($q){
                $q->where("developer_tasks.assigned_to",auth()->user()->id)->where('is_resolved', 0);
            });
        }
        // category filter start count
        $issuesGroups = clone ($issues);
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'Planned')->groupBy("developer_tasks.assigned_to")->select([\DB::raw("count(developer_tasks.id) as total_product"), "developer_tasks.assigned_to"])->pluck("total_product", "assigned_to")->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));
        $userModel = \App\User::whereIn("id", $userIds)->pluck("name", "id")->toArray();

        $countPlanned = [];
        if (!empty($issuesGroups) && !empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countPlanned[] = [
                    "id" => $key,
                    "name" => !empty($userModel[$key]) ? $userModel[$key] : "N/A",
                    "count" => $count,
                ];
            }
        }
        // category filter start count
        $issuesGroups = clone ($issues);
        $issuesGroups = $issuesGroups->where('developer_tasks.status', 'In Progress')->groupBy("developer_tasks.assigned_to")->select([\DB::raw("count(developer_tasks.id) as total_product"), "developer_tasks.assigned_to"])->pluck("total_product", "assigned_to")->toArray();
        $userIds = array_values(array_filter(array_keys($issuesGroups)));

        $userModel = \App\User::whereIn("id", $userIds)->pluck("name", "id")->toArray();
        $countInProgress = [];
        if (!empty($issuesGroups) && !empty($userModel)) {
            foreach ($issuesGroups as $key => $count) {
                $countInProgress[] = [
                    "id" => $key,
                    "name" => !empty($userModel[$key]) ? $userModel[$key] : "N/A",
                    "count" => $count,
                ];
            }
        }
        // Sort
        if ($request->order == 'priority') {
            $issues = $issues->orderBy('priority', 'ASC')->orderBy('created_at', 'DESC')->with('communications');
        }

        if ($request->order == 'create_asc') {
            $issues = $issues->orderBy('developer_tasks.created_at', 'ASC');
        } else if ($request->order == 'communication_desc') {
            $issues = $issues->orderBy('chat_messages.id', 'DESC');
        } else {
            $issues = $issues->orderBy('developer_tasks.created_at', 'DESC');
        }

        $issues =  $issues->with('communications');

        $issues = $issues->paginate(Setting::get('pagination'));
        $priority = \App\ErpPriority::where('model_type', '=', DeveloperTask::class)->pluck('model_id')->toArray();

        $languages = \App\DeveloperLanguage::get()->pluck("name","id")->toArray();

        return view('development.issue', [
            'issues' => $issues,
            'users' => $users,
            'modules' => $modules,
            'request' => $request,
            'title' => $type,
            'priority' => $priority,
            'countPlanned' => $countPlanned,
            'countInProgress' => $countInProgress,
            'statusList' => $statusList,
            'languages' => $languages
        ]);
    }
    public function issueIndex(Request $request)
    {
        $issues = new Issue;

        if ((int) $request->get('submitted_by') > 0) {
            $issues = $issues->where('submitted_by', $request->get('submitted_by'));
        }
        if ((int) $request->get('responsible_user') > 0) {
            $issues = $issues->where('responsible_user_id', $request->get('responsible_user'));
        }
        if ((int) $request->get('assigned_to') > 0) {
            $issues = $issues->where('assigned_to', $request->get('assigned_to'));
        }
        if ((int) $request->get('corrected_by') > 0) {
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
        if ((int) $request->show_resolved !== 1) {
            $issues = $issues->where('is_resolved', 0);
        }
        // Sort
        if ($request->order == 'create') {
            $issues = $issues->orderBy('created_at', 'DESC')->with('communications')->get();
        } else {
            $issues = $issues->orderBy('priority', 'ASC')->orderBy('created_at', 'DESC')->with('communications')->get();
        }
        $priority = \App\ErpPriority::where('model_type', '=', Issue::class)->pluck('model_id')->toArray();
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
        $user_id = $request->get('user_id', 0);
        $selected_issue = $request->get('selected_issue', []); 

        $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by')
            ->leftJoin('erp_priorities', function ($query) use ($user_id) {
                $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
            })->where('is_resolved', '0');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->where(function($q) use ($selected_issue , $user_id) {
                $user_id = is_null($user_id) ? 0 : $user_id;
                $q->whereIn('developer_tasks.id', $selected_issue)->orWhere("erp_priorities.user_id", $user_id);
            });
            //$issues = $issues->whereIn('developer_tasks.id', $request->get('selected_issue', []));
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->groupBy("developer_tasks.id")->orderBy('erp_priorities.id')->get();

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
        $user_id = $request->get('user_id', 0);
        //get all user task
        $issues = DeveloperTask::where('assigned_to', $request->get('user_id', 0))->pluck('id')->toArray();
        //delete old priority
        \App\ErpPriority::where("user_id",$user_id)->where('model_type', '=', DeveloperTask::class)->delete();

        if (!empty($priority)) {
            foreach ((array) $priority as $model_id) {
                \App\ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => DeveloperTask::class,
                    'user_id' => $user_id
                ]);
            }

            $issues = DeveloperTask::select('developer_tasks.id', 'developer_tasks.module_id', 'developer_tasks.subject', 'developer_tasks.task', 'developer_tasks.created_by','developer_tasks.task_type_id')
                ->join('erp_priorities', function ($query) use($user_id) {
                    $query->on('erp_priorities.model_id', '=', 'developer_tasks.id');
                    $query->where('erp_priorities.model_type', '=', DeveloperTask::class);
                    $query->where('erp_priorities.user_id', '=', $user_id);
                })
                ->where('is_resolved', '0')
                ->orderBy('erp_priorities.id')
                ->get();
            $message = '';
            $i = 1;
            foreach ($issues as $value) {
                $mode  = ($value->task_type_id == 3) ? "#ISSUE-" : "#TASK-";
                $message .= $i . " : ".$mode . $value->id . "-" . $value->subject . "\n";
                $i++;
            }
            if (!empty($message)) {
                $requestData = new Request();
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $request->get('user_id', 0);

                $string = "";
                if (!empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes') . "\n";
                }
                $string .= "Issue Priority is : \n" . $message;

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

    private function getTokens()
    {
        if (!Storage::disk('local')->exists(HUBSTAFF_TOKEN_FILE_NAME)) {
            $this->generateAccessToken(SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get(HUBSTAFF_TOKEN_FILE_NAME));
        return $tokens;
    }

    private function generateAccessToken(string $refreshToken)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->post(
                'https://account.hubstaff.com/access_tokens',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken
                    ]
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $tokens = [
                'access_token' => $responseJson->access_token,
                'refresh_token' => $responseJson->refresh_token
            ];

            return Storage::disk('local')->put(HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
            return false;
        }
    }

    private function refreshTokens()
    {
        $tokens = $this->getTokens();
        $this->generateAccessToken($tokens->refresh_token);
    }

    private function createHubstaffTask(string $taskSummary, ?int $hubstaffUserId, int $projectId, bool $shouldRetry = true)
    {
        $tokens = $this->getTokens();

        $url = 'https://api.hubstaff.com/v2/projects/' . $projectId . '/tasks';
        $httpClient = new Client();
        try {

            $body = array(
                'summary' => $taskSummary
            );

            if ($hubstaffUserId) {
                $body['assignee_id'] = $hubstaffUserId;
            } else {
                $body['assignee_id'] = getenv('HUBSTAFF_DEFAULT_ASSIGNEE_ID');
            }

            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json'
                    ],

                    RequestOptions::BODY => json_encode($body)
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            return $parsedResponse->task->id;
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->createHubstaffTask(
                        $taskSummary,
                        $hubstaffUserId,
                        $projectId,
                        false
                    );
                }
            }
        }
        return false;
    }

    /**
     * return branch name or false
     */
    private function createBranchOnGithub($repositoryId, $taskId, $taskTitle,  $branchName = 'master')
    {
        $newBranchName = 'DEVTASK-' . $taskId . '/' . preg_replace('/\s/', '', $taskTitle);

        // get the master branch SHA
        // https://api.github.com/repositories/:repoId/branches/master
        $url = 'https://api.github.com/repositories/' . $repositoryId . '/branches/' . $branchName;
        try {
            $response = $this->githubClient->get($url);
            $masterSha = json_decode($response->getBody()->getContents())->commit->sha;
        } catch (Exception $e) {
            return false;
        }

        // create a branch
        // https://api.github.com/repositories/:repo/git/refs
        $url = 'https://api.github.com/repositories/' . $repositoryId . '/git/refs';
        try {
            $this->githubClient->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        "ref" => "refs/heads/" . $newBranchName,
                        "sha" => $masterSha
                    ])
                ]
            );
            return $newBranchName;
        } catch (Exception $e) {

            if ($e instanceof ClientException && $e->getResponse()->getStatusCode() == 422) {
                // branch already exists
                return $newBranchName;
            }
            return false;
        }
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
            'status' => 'required',
            'repository_id' => 'required',
            'hubstaff_project' => 'required'
        ]);
        $data = $request->except('_token');
        $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();
        //$data[ 'responsible_user_id' ] = $request->user_id ? $request->user_id : Auth::id();
        $data['created_by'] = Auth::id();
        //$data[ 'submitted_by' ] = Auth::id();
        $module = $request->get('module_id');
        if (!empty($module)) {
            $module = DeveloperModule::find($module);
            if (!$module) {
                $module = new DeveloperModule();
                $module->name = $request->get('module_id');
                $module->save();
                $data['module_id'] = $module->id;
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

        // CREATE GITHUB REPOSITORY BRANCH
        $newBranchName = $this->createBranchOnGithub(
            $request->get('repository_id'),
            $task->id,
            $task->subject
        );

        if (is_string($newBranchName)) {
            $message = $request->input('task') . PHP_EOL . "A new branch " . $newBranchName . " has been created. Please pull the current code and run 'git checkout " . $newBranchName . "' to work in that branch.";
        } else {
            $message = $request->input('task');
        }

        $requestData = new Request();
        $requestData->setMethod('POST');
        $requestData->request->add(['issue_id' => $task->id, 'message' => $message, 'status' => 1]);

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

        $assignedUser = HubstaffMember::where('user_id', $request->input('user_id'))->first();
        $hubstaffProject = HubstaffProject::find($request->input('hubstaff_project'));

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }

        $hubstaffTaskId = $this->createHubstaffTask(
            $request->input('task'),
            $hubstaffUserId,
            $hubstaffProject->hubstaff_project_id
        );

        if ($hubstaffUserId) {
            $task = new HubstaffTask();
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaffProject->id;
            $task->hubstaff_project_id = $hubstaffProject->hubstaff_project_id;
            $task->summary = $request->input('task');
            $task->save();
        }

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
        if ($request->response == 1) {

            $reference = md5(strtolower($request->reference));
            //Check if reference exist
            $existReference = DeveloperTask::where('reference', $reference)->first();
            if ($existReference != null || $existReference != '') {
                return redirect()->back()->withErrors(['Issue Already Created!']);
            }
        }

        if (!isset($reference)) {
            $reference = null;
        }
        $module = DeveloperModule::find($module);
        if (!$module) {
            $module = new DeveloperModule();
            $module->name = $request->get('module');
            $module->save();
            $data['module'] = $module->id;
        }
        //$issue = Issue::create($data);
        /*$responsibleUser = $request->get('responsible_user_id', 0);
        if (empty($responsibleUser)) {
            $responsibleUser = Auth::id();
        }*/
        $task = new DeveloperTask;
        $task->priority = $request->input('priority');
        $task->subject = $request->input('subject');
        $task->task = $request->input('issue');
        $task->responsible_user_id = 0;
        $task->assigned_to = $request->get('assigned_to', 0);
        $task->module_id = $module->id;
        $task->user_id = 0;
        $task->assigned_by = Auth::id();
        $task->created_by = Auth::id();
        $task->reference = $reference;
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
        $data['user_id'] = Auth::id();

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
        $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

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

    private function getHubstaffLockVersion($hubstaffTaskId, $shouldRetry = true)
    {

        $tokens = $this->getTokens();

        $url = 'https://api.hubstaff.com/v2/tasks/' . $hubstaffTaskId;

        $httpClient = new Client();

        try {
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token
                    ],
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());

            return $parsedResponse->task->lock_version;
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->getHubstaffLockVersion(
                        $hubstaffTaskId,
                        false
                    );
                }
            }
        }
        return false;
    }

    private function updateHubstaffAssignee($hubstaffTaskId, $assigneeId, $shouldRetry = true)
    {
        $lockVersion = $this->getHubstaffLockVersion($hubstaffTaskId);



        if ($lockVersion === false) {
            return false;
        }

        $tokens = $this->getTokens();

        $url = 'https://api.hubstaff.com/v2/tasks/' . $hubstaffTaskId;

        $httpClient = new Client();


        try {

            $body = array(
                'lock_version' => $lockVersion,
                'assignee_id' => $assigneeId
            );

            $response = $httpClient->put(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json'
                    ],

                    RequestOptions::BODY => json_encode($body)
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());
            return $parsedResponse->task->id;
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->updateHubstaffAssignee(
                        $hubstaffTaskId,
                        $assigneeId,
                        false
                    );
                }
            }
        }
        return false;
    }

    public function assignUser(Request $request)
    {
        $masterUserId = $request->get("master_user_id", 0);
        // $issue = Issue::find($request->get('issue_id'));
        $issue = DeveloperTask::find($request->get('issue_id'));

        $hubstaffUser = HubstaffMember::where('user_id', $request->get('assigned_to'))->first();

        if ($hubstaffUser) {
            $this->updateHubstaffAssignee(
                $issue->hubstaff_task_id,
                $hubstaffUser->hubstaff_user_id
            );
        }

        if ($masterUserId > 0) {
            $issue->master_user_id = $masterUserId;
        } else {
            $issue->assigned_to = $request->get('assigned_to');
        }
        $issue->save();
        return response()->json([
            'status' => 'success'
        ]);
    }
    public function assignResponsibleUser(Request $request)
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        //$issue = Issue::find($request->get('issue_id'));
        //$issue->responsible_user_id = $request->get('responsible_user_id');
        $issue->assigned_by = \Auth::id();
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
        if (strtolower($request->get('is_resolved')) == "done") {
            $issue->responsible_user_id = $issue->assigned_to;
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

    public function saveEstimateMinutes(Request $request)
    {
        $issue = DeveloperTask::find($request->get('issue_id'));
        //$issue = Issue::find($request->get('issue_id'));
        $issue->estimate_minutes = $request->get('estimate_minutes');
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
        $data['user_id'] = Auth::id();

        $created = DeveloperTaskComment::create($data);
        if ($created) {
            $response['status'] = 'ok';
            $response['msg'] = 'Comment stored successfully';
            echo json_encode($response);
        } else {
            $response['status'] = 'error';
            $response['msg'] = 'Error';
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
        $users = Helpers::getUserArray(User::role('Developer')->get());
        //$users = Helpers::getUsersByRoleName('Developer');
        // Get all task types
        $tasksTypes = TaskTypes::all();
        $moduleNames = [];
        // Get all modules
        $modules = DeveloperModule::all();
        // Loop over all modules and store them
        foreach ($modules as $module) {
            $moduleNames[$module->id] = $module->name;
        }

        // this is the ID for erp
        $defaultRepositoryId = 231925646;
        $respositories = GithubRepository::all();


        $html = view('development.ajax.add_new_task', compact("users", "tasksTypes", "modules", "moduleNames", "respositories", "defaultRepositoryId"))->render();
        //Get hubstaff projects
        $projects = HubstaffProject::all();

        $html = view('development.ajax.add_new_task', compact("users", "tasksTypes", "modules", "moduleNames", "projects"))->render();
        return json_encode(compact("html", "status"));
    }
    public function saveLanguage(Request $request)
    {
        $language = $request->get('language');

        if(!empty(trim($language))) {
            if(!is_numeric($language)) {
                $languageModal = \App\DeveloperLanguage::updateOrCreate(
                    ['name' => $language],
                    ['name' => $language]
                );
            }

            $issue = DeveloperTask::find($request->get('issue_id'));
            $issue->language = isset($languageModal->id) ? $languageModal->id : $language;
            $issue->save();
        }
        

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function uploadDocument(Request $request)
    {

        $id = $request->get("developer_task_id",0);
        $subject = $request->get("subject",null);

        if($id > 0 && !empty($subject)) {

            $devTask = DeveloperTask::find($id);
            
            if(!empty($devTask)) {

                $devDocuments = new \App\DeveloperTaskDocument;
                $devDocuments->fill(request()->all());
                $devDocuments->created_by = \Auth::id();
                $devDocuments->save();

                if ($request->hasfile('files')) {
                    foreach ($request->file('files') as $files) {
                        $media = MediaUploader::fromSource($files)
                            ->toDirectory('developertask/' . floor($devTask->id / config('constants.image_per_folder')))
                            ->upload();
                        $devDocuments->attachMedia($media, config('constants.media_tags'));
                    }
                }

                return response()->json(["code" => 200 , "success" => "Done!"]);
            }

            return response()->json(["code" => 500 , "error" => "Oops, There is no record in database"]);


        }else{
            return response()->json(["code" => 500 , "error" => "Oops, Please fillup required fields"]);
        }

    }

    public function getDocument(Request $request)
    {
        $id = $request->get("id",0);

        if($id > 0) {

            $devDocuments = \App\DeveloperTaskDocument::where("developer_task_id",$id)->get();

            $html = view('development.ajax.document-list', compact("devDocuments"))->render();
            
            return response()->json(["code" => 200 , "data" => $html]);

        }else{
            return response()->json(["code" => 500 , "error" => "Oops, id is required field"]);
        }

    }
}
