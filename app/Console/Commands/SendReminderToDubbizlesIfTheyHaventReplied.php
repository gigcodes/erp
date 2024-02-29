<?php

namespace App\Console\Commands;

use App\Dubbizle;
use Carbon\Carbon;
use App\ChatMessage;
use App\CronJobReport;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WhatsAppController;

class SendReminderToDubbizlesIfTheyHaventReplied extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-to-dubbizle';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was updated.']);

            $now = Carbon::now()->toDateTimeString();

            $messagesIds = DB::table('chat_messages')
                ->selectRaw('MAX(id) as id, dubbizle_id')
                ->groupBy('dubbizle_id')
                ->whereNotNull('message')
                ->where('dubbizle_id', '>', '0')
                ->where(function ($query) {
                    $query->whereNotIn('status', [7, 8, 9]);
                })
                ->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query was finished.']);

            foreach ($messagesIds as $messagesId) {
                $dubbizle = Dubbizle::find($messagesId->dubbizle_id);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'dubbizle message query was finished.']);
                if (! $dubbizle) {
                    continue;
                }

                $frequency = $dubbizle->frequency;
                if (! ($frequency >= 5)) {
                    continue;
                }

                $message = ChatMessage::whereRaw('TIMESTAMPDIFF(MINUTE, `updated_at`, "' . $now . '") >= ' . $frequency)
                    ->where('id', $messagesId->id)
                    ->where('user_id', '>', '0')
                    ->where('approved', '1')
                    ->first();
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message query was finished.']);

                if (! $message) {
                    continue;
                }

                dump('saving...');

                $templateMessage = $dubbizle->reminder_message;

                $this->sendMessage($dubbizle->id, $templateMessage);
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Message sent.']);
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was finished.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function sendMessage($dubbizle, $message): void
    {
        $params = [
            'number'      => null,
            'user_id'     => 6,
            'approved'    => 1,
            'status'      => 1,
            'dubbizle_id' => $dubbizle,
            'message'     => $message,
        ];

        $chat_message = ChatMessage::create($params);

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app(WhatsAppController::class)->approveMessage('dubbizle', $myRequest);
    }
}
