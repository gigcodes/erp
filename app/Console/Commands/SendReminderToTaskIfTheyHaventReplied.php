<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\CronJobReport;
use App\Http\Controllers\WhatsAppController;
use App\Vendor;
use App\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $now = Carbon::now()->toDateTimeString();

            // task page logic starting from here
            $tasks = \App\Task::where('frequency',">",0)->where('reminder_message',"!=","")->get();

            if(!$tasks->isEmpty()) {
                foreach($tasks as $task) {
                    $templateMessage = $task->reminder_message;
                    if($task->reminder_last_reply == 0) {
                        $this->sendMessage($task->id, $templateMessage);
                        $task->last_send_reminder = date("Y-m-d H:i:s");
                        $task->save();
                    }else{
                        $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "' . $now . '") >= ' . $task->frequency)
                            ->where('task_id', $task->id)
                            ->latest()
                            ->first();

                        if($message) {
                           if($message->approved == 1) {
                              continue;
                           }
                        }

                        $this->sendMessage($task->id, $templateMessage);
                        $task->last_send_reminder = date("Y-m-d H:i:s");
                        $task->save();
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }

    }

    /**
     * @param $taskId
     * @param $message
     * create chat message entry and then approve the message and send the message...
     */
    private function sendMessage($taskId, $message)
    {

        $params = [
            'number'    => null,
            'user_id'   => 6,
            'approved'  => 1,
            'status'    => 1,
            'task_id' => $taskId,
            'message'   => $message,
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(\App\Http\Controllers\WhatsAppController::class)->approveMessage('task', $myRequest);

    }
}
