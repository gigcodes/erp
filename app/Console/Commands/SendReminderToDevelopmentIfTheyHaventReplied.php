<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJobReport;
use App\DeveloperTask;
use App\Http\Controllers\WhatsAppController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendReminderToDevelopmentIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-development';

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
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $now = Carbon::now()->toDateTimeString();

        // task page logic starting from here
        $tasks = \App\DeveloperTask::where('frequency', ">", 0)->where('reminder_message', "!=", "")->where('reminder_from', "<=", date("Y-m-d H:i:s"))->get();

        if (!$tasks->isEmpty()) {
            foreach ($tasks as $task) {
                $templateMessage = $task->reminder_message;
                if ($task->reminder_last_reply == 0) {
                    $this->sendMessage($task->id, $templateMessage, $task);
                    $task->last_send_reminder = date("Y-m-d H:i:s");
                    $task->save();
                } else {
                    $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "' . $now . '") >= ' . $task->frequency)
                        ->where('developer_task_id', $task->id)
                        ->latest()
                        ->first();

                    if ($message) {
                        if ($message->approved == 1) {
                            continue;
                        }
                    }

                    $this->sendMessage($task->id, $templateMessage, $task);
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
    private function sendMessage($taskId, $message, $task = null)
    {

        $params = [
            'number'            => null,
            'user_id'           => 6,
            'erp_user'          => ($task) ? $task->assigned_to : null,
            'approved'          => 1,
            'status'            => 1,
            'developer_task_id' => $taskId,
            'message'           => $message,
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('user', $myRequest);
    }
}
