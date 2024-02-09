<?php

namespace App\Providers;

use App\Sop;
use App\Task;
use App\User;
use App\Email;
use App\TodoList;
use App\TodoStatus;
use App\Instruction;
use App\LiveChatUser;
use App\StoreWebsite;
use App\TodoCategory;
use App\DeveloperTask;
use App\CustomerLiveChat;
use App\PermissionRequest;
use App\LivechatincSetting;
use Illuminate\Http\Request;
use App\Models\MonitorServer;
use App\TimeDoctor\TimeDoctorLog;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Meetings\ZoomMeetingParticipant;
use App\Models\DatabaseBackupMonitoring;

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
        View::composer('layouts.app', function ($view) use ($request) {
            $auth_user = $request->user();
            $route_name = request()->route()->getName();
            if ($auth_user) {
                $d_taskList = DeveloperTask::select('id')->orderBy('id', 'desc')->pluck('id');
                $g_taskList = Task::select('id')->orderBy('id', 'desc')->pluck('id');
                $status = MonitorServer::where('status', 'off')->count();
                $logs = TimeDoctorLog::query()->with(['user']);
                $dbBackupList = DatabaseBackupMonitoring::where('is_resolved', 0)->count();
                $permissionCount = PermissionRequest::count();
                $description = ZoomMeetingParticipant::whereNull('description')->count();
                $todoLists = TodoList::where('user_id', $auth_user->id)->where('status', 'Active')
                    ->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->with('category')->limit(10)->get();
                $statuses = TodoStatus::all();

                $liveChatUsers = LiveChatUser::where('user_id', $auth_user->id)->first();
                $key_ls = LivechatincSetting::first();

                // Instruction counts
                $pending_instructions_count = Instruction::where('assigned_to', $auth_user->id)
                    ->whereNull('completed_at')
                    ->count();
                $completed_instructions_count = Instruction::where('assigned_to', $auth_user->id)
                    ->whereNotNull('completed_at')
                    ->count();

                // Task counts
                $pending_tasks_count = Task::where('is_statutory', 0)
                    ->where('assign_to', $auth_user->id)
                    ->whereNull('is_completed')
                    ->count();
                $completed_tasks_count = Task::where('is_statutory', 0)
                    ->where('assign_to', $auth_user->id)
                    ->whereNotNull('is_completed')
                    ->count();

                $chatIds = cache()->remember('CustomerLiveChat::with::customer::orderby::seen_asc', 60 * 60 * 24 * 1, function () {
                    return CustomerLiveChat::with('customer')->orderBy('seen', 'asc')
                        ->orderBy('status', 'desc')
                        ->get();
                });

                $newMessageCount = CustomerLiveChat::where('seen', 0)->count();

                $usersop = Sop::all();
                $users = User::orderBy('name', 'ASC')->get();

                $userEmails = Email::where('seen', '0')
                    ->orderBy('created_at', 'desc')
                    ->latest()
                    ->take(20)
                    ->get();
                $websites = StoreWebsite::get();
                $todoCategories = TodoCategory::get();
                $userLists = User::where('is_active', 1)->orderBy('name', 'asc')->get();

                $storeWebsiteConnections = StoreWebsite::DB_CONNECTION;

                $isAdmin = $auth_user->isAdmin();

                $database_table_name = \DB::table('information_schema.TABLES')
                    ->where('table_schema', config('database.connections.mysql.database'))
                    ->get();
                $shell_list = shell_exec('bash ' . config('env.DEPLOYMENT_SCRIPTS_PATH') . '/webaccess-firewall.sh -f list');

                $view->with(
                    compact('d_taskList',
                        'g_taskList',
                        'status',
                        'logs',
                        'dbBackupList',
                        'permissionCount',
                        'description',
                        'todoLists',
                        'statuses',
                        'liveChatUsers',
                        'key_ls',
                        'pending_instructions_count',
                        'completed_instructions_count',
                        'chatIds',
                        'newMessageCount',
                        'usersop',
                        'users',
                        'userEmails',
                        'websites',
                        'todoCategories',
                        'userLists',
                        'storeWebsiteConnections',
                        'isAdmin',
                        'database_table_name',
                        'route_name',
                        'shell_list'
                    ));
            } else {
                $view->with('route_name', $route_name)
                    ->with('isAdmin', false);
            }
        });
    }
}
