<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJobReport;
use App\Http\Controllers\WhatsAppController;
use App\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendReminderToTaskIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder send for task';

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
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $now = Carbon::now()->toDateTimeString();

        // task page logic starting from here
        $tasks = \App\Task::where('frequency', ">", 0)->where('reminder_message', "!=", "")->select(["*",\DB::raw('TIMESTAMPDIFF(MINUTE, `last_send_reminder`, "' . $now . '") as diff_min')])->get();

        if (!$tasks->isEmpty()) {
            foreach ($tasks as $task) {
                $templateMessage = $task->reminder_message;
                $this->info("started for task #".$task->id);
                if ($task->diff_min >= $task->frequency && ($task->reminder_from == "0000-00-00 00:00" || strtotime($task->reminder_from) >= strtotime("now"))) {
                    $this->info("condition matched for task #".$task->id);
                    $this->sendMessage($task->id, $templateMessage);
                    $task->last_send_reminder = date("Y-m-d H:i:s");
                    $task->save();
                }
            }
        }

        $report->update(['end_time' => Carbon::now()]);

    }

    /**
     * @param $taskId
     * @param $message
     * create chat message entry and then approve the message and send the message...
     */
    private function sendMessage($taskId, $message)
    {

        $params = [
            'number'   => null,
            'user_id'  => 6,
            'approved' => 1,
            'status'   => 1,
            'task_id'  => $taskId,
            'message'  => $message,
        ];

        $chat_message = ChatMessage::create($params);

        \App\ChatbotReply::create([
            'question'=> $message,
            'replied_chat_id' => $chat_message->id,
            'reply_from' => 'database'
        ]);

    }
}
