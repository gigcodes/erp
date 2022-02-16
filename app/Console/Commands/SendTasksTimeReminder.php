<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\LogChatMessage;
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
        $est_time_message = TaskMessage::where('message_type','est_time_message')->first();
        $est_time_msg = (!empty($est_time_message)) ? $est_time_message->message : "";
        $est_date_message = TaskMessage::where('message_type','est_date_message')->first();
        $est_date_msg = (!empty($est_date_message)) ? $est_date_message->message : "";
        
        $overdue_time_date_message = TaskMessage::where('message_type','overdue_time_date_message')->first();
        $overdue_message = (!empty($overdue_time_date_message)) ? $overdue_time_date_message->message : "";
        foreach ($developertasks as $developertask) 
        {
            if($developertask->estimate_time == NULL)
            {
                    $insertParams = [
                        "developer_task_id"      => $developertask->id,
                        "message"                => $est_time_msg,
                        "status"                 => 1,
                        "is_queue"               => 1,
                        "approved"               => 1,
                        "message_application_id" => $messageApplicationId,
                        "task_time_reminder" => 1,
                    ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);

                $this->logs("#1",$developertask->id,$est_time_msg,"Created Estimate Time Message for developer task");
            } 
            if($developertask->estimate_date == NULL)
            {
                $insertParams = [
                    "developer_task_id"      => $developertask->id,
                    "message"                => $est_date_msg,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                    "task_time_reminder" => 1,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);

                $this->logs("#2",$developertask->id,$est_date_msg,"Created Estimate Date Message for developer task");
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
                    "task_time_reminder" => 1,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
                $this->logs("#3",$developertask->id,$overdue_message,"Created Overdue Message for developer task");
            }  
        }
        $tasks = Task::where('is_flagged', '1')->get();
        foreach ($tasks as $task) 
        {
            if($task->timeSpent == NULL)
            {
                    $insertParams = [
                        "task_id"                => $task->id,
                        "message"                => $est_time_msg,
                        "status"                 => 1,
                        "is_queue"               => 1,
                        "approved"               => 1,
                        "message_application_id" => $messageApplicationId,
                        "task_time_reminder" => 1,
                    ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
                $this->logs("#4",$task->id,$est_time_msg,"Created Estimate Time message for task");
            } 
            if($task->approximate == NULL)
            {
                $insertParams = [
                    "task_id"                => $task->id,
                    "message"                => $est_date_msg,
                    "status"                 => 1,
                    "is_queue"               => 1,
                    "approved"               => 1,
                    "message_application_id" => $messageApplicationId,
                    "task_time_reminder" => 1,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
                $this->logs("#5",$task->id,$est_date_msg,"Created Estimate date message for task");
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
                    "task_time_reminder" => 1,
                ];

                $chatMessage = \App\ChatMessage::firstOrCreate($insertParams);
                $this->logs("#6",$task->id,$overdue_message,"Created Overdue Message for task");
            }  
        }
    }
    public function logs($log_case_id,$task_id,$message,$log_msg)
    {
        $log = New LogChatMessage(); 
        $log->log_case_id= $log_case_id;
        $log->task_id= $task_id;
        $log->message= $message;
        $log->log_msg= $log_msg;
        $log->task_time_reminder= 1;
        $log->save();

    }
}
