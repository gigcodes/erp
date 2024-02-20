<?php

namespace App\Console\Commands;

use App\Task;
use App\TaskMessage;
use App\DeveloperTask;
use App\LogChatMessage;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

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
    protected $description = 'Tasks Time Reminder';

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
        try {
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Cron was started to run']);

            $messageApplicationId = 3;
            $currenttime = date('Y-m-d H:m:s');

            $q = TaskMessage::where('message_type', 'est_time_message')->orderBy('id', 'DESC')->first();
            $est_time_msg = $q ? ($q->frequency != 0 ? $q->message : '') : '';
            $q = TaskMessage::where('message_type', 'est_date_message')->orderBy('id', 'DESC')->first();
            $est_date_msg = $q ? ($q->frequency != 0 ? $q->message : '') : '';
            $q = TaskMessage::where('message_type', 'overdue_time_date_message')->orderBy('id', 'DESC')->first();
            $overdue_message = $q ? ($q->frequency != 0 ? $q->message : '') : '';

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'TaskMessage model query was finished']);

            $q = DeveloperTask::query();
            $q->whereNotNull('user_id');
            $q->where('user_id', '<>', 0);
            $q->whereNotIn('status', [
                DeveloperTask::DEV_TASK_STATUS_DONE,
                DeveloperTask::DEV_TASK_STATUS_IN_REVIEW,
            ]);
            $q->whereRaw('assigned_to > 0');

            $developertasks = $q->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'DeveloperTask model query was finished']);

            foreach ($developertasks as $developertask) {
                $messagePrefix = DeveloperTask::getMessagePrefix($developertask);

                if (! $developertask->estimate_time && $est_time_msg) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'developer_task_id' => $developertask->id,
                        'user_id' => $developertask->user_id,
                        'message' => $messagePrefix . $est_time_msg,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#1', $developertask->id, $messagePrefix . $est_time_msg, 'Created Estimate Time Message for developer task');
                } elseif (! $developertask->estimate_date && $est_date_msg) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'developer_task_id' => $developertask->id,
                        'user_id' => $developertask->user_id,
                        'message' => $messagePrefix . $est_date_msg,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#2', $developertask->id, $messagePrefix . $est_date_msg, 'Created Estimate Date Message for developer task');
                } elseif ($developertask->estimate_date && strtotime($currenttime) > strtotime($developertask->estimate_date) && $overdue_message) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'developer_task_id' => $developertask->id,
                        'user_id' => $developertask->user_id,
                        'message' => $messagePrefix . $overdue_message,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#3', $developertask->id, $messagePrefix . $overdue_message, 'Created Overdue Message for developer task');
                }
            }

            $q = Task::query();
            $q->whereNotNull('assign_to');
            $q->where('assign_to', '<>', 0);
            $q->whereNotIn('status', [
                Task::TASK_STATUS_DONE,
                Task::TASK_STATUS_IN_REVIEW,
            ]);
            $q->whereRaw('assign_to > 0');

            $tasks = $q->get();

            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Task model query was finished']);

            foreach ($tasks as $task) {
                $messagePrefix = Task::getMessagePrefix($task);

                if (! $task->approximate && $est_time_msg) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'task_id' => $task->id,
                        'user_id' => $task->assign_to,
                        'message' => $messagePrefix . $est_time_msg,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#4', $task->id, $messagePrefix . $est_time_msg, 'Created Estimate Time message for task');
                } elseif (! $task->start_date && $est_date_msg) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'task_id' => $task->id,
                        'user_id' => $task->assign_to,
                        'message' => $messagePrefix . $est_date_msg,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#5', $task->id, $messagePrefix . $est_date_msg, 'Created Estimate date message for task');
                } elseif ($task->approximate && strtotime($currenttime) > strtotime($task->approximate) && $overdue_message) {
                    $chatMessage = \App\ChatMessage::firstOrCreate([
                        'task_id' => $task->id,
                        'user_id' => $task->assign_to,
                        'message' => $messagePrefix . $overdue_message,
                        'status' => 1,
                        'is_queue' => 1,
                        'approved' => 1,
                        'message_application_id' => $messageApplicationId,
                        'task_time_reminder' => 1,
                    ]);

                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'save chat mesage record with ID:' . $chatMessage->id]);

                    $this->logs('#6', $task->id, $messagePrefix . $overdue_message, 'Created Overdue Message for task');
                }
            }
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    public function logs($log_case_id, $task_id, $message, $log_msg)
    {
        $log = new LogChatMessage();
        $log->log_case_id = $log_case_id;
        $log->task_id = $task_id;
        $log->message = $message;
        $log->log_msg = $log_msg;
        $log->task_time_reminder = 1;
        $log->save();
    }
}
