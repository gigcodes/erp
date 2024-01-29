<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\DeveloperTask;
use App\Task;
use App\Models\MonitorServer;
use App\TimeDoctor\TimeDoctorLog;
use App\Models\DatabaseBackupMonitoring;
use App\PermissionRequest;
use App\Meetings\ZoomMeetingParticipant;
use App\TodoList;
use App\TodoStatus;
use App\LiveChatUser;
use App\LivechatincSetting;
use App\Instruction;
use App\CustomerLiveChat;
use App\User;
use App\Sop;
use App\Email;
use App\StoreWebsite;
use App\TodoCategory;
use Illuminate\Http\Request;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        View::composer('layouts.app', function ($view) use ($request){
            if($request->user()) {
                $d_taskList = DeveloperTask::select('id')->orderBy('id', 'desc')->get()->pluck('id');
                $g_taskList = Task::select('id')->orderBy('id', 'desc')->get()->pluck('id');
                $status = MonitorServer::where('status', 'off')->count();
                $logs = TimeDoctorLog::query()->with(['user']);
                $dbBackupList = DatabaseBackupMonitoring::where('is_resolved', 0)->count();
                $permissionCount = PermissionRequest::count();
                $description = ZoomMeetingParticipant::whereNull('description')->count();
                $todoLists = TodoList::where('user_id',$request->user()->id)->where('status','Active')->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->with('category')->limit(10)->get();
                $statuses = TodoStatus::get();
    
                $lukas_pending_devtasks_count =DeveloperTask::where('user_id', 3)->where('status', '!=',
                'Done')->count();
                $lukas_completed_devtasks_count = DeveloperTask::where('user_id', 3)->where('status', 'Done')->count();
                $rishab_pending_devtasks_count = DeveloperTask::where('user_id', 65)->where('status', '!=',
                'Done')->count();
                $rishab_completed_devtasks_count = DeveloperTask::where('user_id', 65)->where('status',
                'Done')->count();
    
                $liveChatUsers = LiveChatUser::where('user_id',$request->user()->id)->first();
                $key_ls = LivechatincSetting::first();
    
                $pending_instructions_count = Instruction::where('assigned_to',
                $request->user()->id)->whereNull('completed_at')->count();
                $completed_instructions_count = Instruction::where('assigned_to',
                $request->user()->id)->whereNotNull('completed_at')->count();
                $sushil_pending_instructions_count = Instruction::where('assigned_from',
                $request->user()->id)->where('assigned_to', 7)->whereNull('completed_at')->count();
                $andy_pending_instructions_count = Instruction::where('assigned_from',
                $request->user()->id)->where('assigned_to', 56)->whereNull('completed_at')->count();
    
                $pending_tasks_count = Task::where('is_statutory', 0)->where('assign_to',
                $request->user()->id)->whereNull('is_completed')->count();
                $completed_tasks_count = Task::where('is_statutory', 0)->where('assign_to',
                $request->user()->id)->whereNotNull('is_completed')->count();
                $sushil_pending_tasks_count = Task::where('is_statutory', 0)->where('assign_to',
                7)->whereNull('is_completed')->count();
                $andy_pending_tasks_count = Task::where('is_statutory', 0)->where('assign_to',
                56)->whereNull('is_completed')->count();
    
                $chatIds = cache()->remember('CustomerLiveChat::with::customer::orderby::seen_asc', 60 * 60 * 24 * 1, function(){
                    return CustomerLiveChat::with('customer')->orderBy('seen','asc')
                    ->orderBy('status','desc')
                    ->get();
                });
                $newMessageCount = CustomerLiveChat::where('seen',0)->count();

                $usersop = Sop::all();
                $users = User::orderBy('name', 'ASC')->get();

                $userEmails = Email::where('seen', '0')
                                    ->orderBy('created_at', 'desc')
                                    ->latest()
                                    ->take(20)
                                    ->get();
                $websites = StoreWebsite::get();
                $todoCategories = TodoCategory::get();
                $userLists = User::where('is_active', 1)->orderBy('name','asc')->get();
                // $statuses = TodoStatus::all()->toArray();
    
                $view->with('d_taskList', $d_taskList)
                      ->with('g_taskList', $g_taskList)
                      ->with('status', $status)
                      ->with('logs', $logs)
                      ->with('dbBackupList', $dbBackupList)
                      ->with('permissionCount', $permissionCount)
                      ->with('description', $description)
                      ->with('todoLists', $todoLists)
                      ->with('statuses', $statuses)
                      ->with('lukas_pending_devtasks_count', $lukas_pending_devtasks_count)
                      ->with('lukas_completed_devtasks_count', $lukas_completed_devtasks_count)
                      ->with('rishab_pending_devtasks_count', $rishab_pending_devtasks_count)
                      ->with('rishab_completed_devtasks_count', $rishab_completed_devtasks_count)
                      ->with('liveChatUsers', $liveChatUsers)
                      ->with('key_ls', $key_ls)
                      ->with('pending_instructions_count', $pending_instructions_count)
                      ->with('completed_instructions_count', $completed_instructions_count)
                      ->with('sushil_pending_instructions_count', $sushil_pending_instructions_count)
                      ->with('andy_pending_instructions_count', $andy_pending_instructions_count)
                      ->with('pending_tasks_count', $pending_tasks_count)
                      ->with('completed_tasks_count', $completed_tasks_count)
                      ->with('sushil_pending_tasks_count', $sushil_pending_tasks_count)
                      ->with('andy_pending_tasks_count', $andy_pending_tasks_count)
                      ->with('chatIds', $chatIds)
                      ->with('newMessageCount', $newMessageCount)
                      ->with('usersop', $usersop)
                      ->with('users', $users)
                      ->with('userEmails', $userEmails)
                      ->with('websites', $websites)
                      ->with('todoCategories', $todoCategories)
                      ->with('userLists', $userLists)
                      ;

            }
            
        });
    }
}
