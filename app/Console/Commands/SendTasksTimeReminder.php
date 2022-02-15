<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\DeveloperTask;
use App\Task;
use App\TaskMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendTasksTimeReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:tasks-time-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $messageApplicationId = 3;
        $currenttime = date("Y-m-d H:m:s");
        $developertasks = DeveloperTask::where('is_flagged', '1')->get();
        $est_time_date_message = TaskMessage::where('message_type','est_time_date_message')->first();
        $est_message = (!empty($est_time_date_message)) ? $est_time_date_message->message : "";
        $overdue_time_date_message = TaskMessage::where('message_type','overdue_time_date_message')->first();
        $overdue_message = (!empty($overdue_time_date_message)) ? $overdue_time_date_message->message : "";
        foreach ($developertasks as $developertask) 
        {
            if($developertask->estimate_time == NULL)
            {
                    $insertParams = [
                        "developer_task_id"      => $developertask->id,
                        "message"                => $est_message,
                        "status"                 => 1,
                        "is_queue"               => 1,
                        "approved"               => 1,
                        "message_application_id" => $messageApplicationId,
                    ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            } 
            if($developertask->estimate_date == NULL)
            {
                $insertParams = [
                    "developer_task_id"      => $developertask->id,
                    "message"                => $est_message,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            } 
            if(strtotime($currenttime) > strtotime($developertask->estimate_date))
            {
                $insertParams = [
                    "developer_task_id"      => $developertask->id,
                    "message"                => $overdue_message,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            }  
        }
        $tasks = Task::where('is_flagged', '1')->get();
        foreach ($tasks as $task) 
        {
            if($task->timeSpent == NULL)
            {
                    $insertParams = [
                        "task_id"                => $task->id,
                        "message"                => $est_message,
                        "status"                 => 1,
                        "is_queue"               => 1,
                        "approved"               => 1,
                        "message_application_id" => $messageApplicationId,
                    ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            } 
            if($task->approximate == NULL)
            {
                $insertParams = [
                    "task_id"                => $task->id,
                    "message"                => $est_message,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            } 
            if(strtotime($currenttime) > strtotime($task->approximate))
            {
                $insertParams = [
                    "task_id"                => $task->id,
                    "message"                => $overdue_message,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
            }  
        }
    }
}
