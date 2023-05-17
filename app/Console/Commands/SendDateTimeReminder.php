<?php

namespace App\Console\Commands;

use App\Task;
use Carbon\Carbon;
use App\ChatMessage;
use App\TaskMessage;
use App\CronJobReport;
use App\DeveloperTask;
use Illuminate\Console\Command;

class SendDateTimeReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:date_time_reminder';

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
        $report = CronJobReport::create([
            'signature' => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $now = Carbon::now()->toDateTimeString();
        $taskMessage = TaskMessage::where('message_type', 'date_time_reminder_message')->orderBy('id', 'DESC')->first();
        if ($taskMessage != null) {
            if ($taskMessage->frequency) {
                // task page logic starting from here
                /*$tasks = \App\Task::where('is_flagged', 1)->whereNotNull('is_completed')->select(["*", \DB::raw('TIMESTAMPDIFF(MINUTE, `last_date_time_reminder`, "' . $now . '") as diff_min')])->get();
            if (!$tasks->isEmpty()) {
                foreach ($tasks as $task) {
                    $templateMessage = $taskMessage['message'];
                    if ($task->diff_min >= $taskMessage['frequency']) {
                        $this->sendMessage($task, $templateMessage);

                        $task->last_date_time_reminder = date("Y-m-d H:i:s");
                        $task->save();
                    }
                }
            }*/

                $tasks = \App\DeveloperTask::query()
                    ->whereNotIn('status', [
                        DeveloperTask::DEV_TASK_STATUS_DONE,
                        DeveloperTask::DEV_TASK_STATUS_IN_REVIEW,
                    ])
                    ->whereRaw('assigned_to IN (SELECT id FROM users WHERE is_task_planned = 1)')
                    // ->where('is_flagged', 1)
                    ->where('status', '<>', 'Done')
                    ->whereNull('estimate_time')
                    ->whereNull('estimate_minutes')
                    ->select(['*', \DB::raw('TIMESTAMPDIFF(MINUTE, `last_date_time_reminder`, "' . $now . '") as diff_min')])
                    ->get();

                if (! $tasks->isEmpty()) {
                    foreach ($tasks as $task) {
                        $templateMessage = $taskMessage['message'];
                        if ($task->last_date_time_reminder == null || $task->diff_min >= $taskMessage['frequency']) {
                            $this->sendDevMessage($task->id, $templateMessage, $task);

                            $task->last_date_time_reminder = date('Y-m-d H:i:s');
                            $task->save();
                        }
                    }
                }
            }
        }
        $report->update(['end_time' => Carbon::now()]);
    }

    private function sendDevMessage($taskId, $message, $task = null)
    {
        $params = [
            'number' => null,
            'user_id' => ($task) ? $task->assigned_to : 6,
            'erp_user' => ($task) ? $task->assigned_to : null,
            'approved' => 0,
            'status' => 1,
            'developer_task_id' => $taskId,
            'message' => $message,
        ];

        $chat_message = ChatMessage::create($params);
        \App\ChatbotReply::create([
            'question' => $message,
            'replied_chat_id' => $chat_message->id,
            'chat_id' => $chat_message->id,
            'reply_from' => 'reminder',
        ]);

        if ($task->responsible_user_id > 0) {
            $params['erp_user'] = $task->responsible_user_id;
            $chat_message = ChatMessage::create($params);
            \App\ChatbotReply::create([
                'question' => $message,
                'replied_chat_id' => $chat_message->id,
                'chat_id' => $chat_message->id,
                'reply_from' => 'reminder',
            ]);
        }

        if ($task->master_user_id > 0) {
            $params['erp_user'] = $task->master_user_id;
            $chat_message = ChatMessage::create($params);
            \App\ChatbotReply::create([
                'question' => $message,
                'replied_chat_id' => $chat_message->id,
                'chat_id' => $chat_message->id,
                'reply_from' => 'reminder',
            ]);
        }

        if ($task->team_lead_id > 0) {
            $params['erp_user'] = $task->team_lead_id;
            $chat_message = ChatMessage::create($params);
            \App\ChatbotReply::create([
                'question' => $message,
                'replied_chat_id' => $chat_message->id,
                'chat_id' => $chat_message->id,
                'reply_from' => 'reminder',
            ]);
        }

        if ($task->tester_id > 0) {
            $params['erp_user'] = $task->tester_id;
            $chat_message = ChatMessage::create($params);
            \App\ChatbotReply::create([
                'question' => $message,
                'replied_chat_id' => $chat_message->id,
                'chat_id' => $chat_message->id,
                'reply_from' => 'reminder',
            ]);
        }
    }
}
