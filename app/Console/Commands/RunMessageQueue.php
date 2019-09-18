<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\MessageQueue;
use App\Customer;
use App\CronJobReport;
use Carbon\Carbon;
use App\Jobs\SendMessageToAll;
use App\Jobs\SendMessageToSelected;

class RunMessageQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:message-queues';

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
            'start_time' => Carbon::now()
        ]);

        $time = Carbon::now();
        $morning = Carbon::create($time->year, $time->month, $time->day, 8, 0, 0);
        $evening = Carbon::create($time->year, $time->month, $time->day, 17, 00, 0);

        if ($time->between($morning, $evening, true)) {
            // Get groups
            $groups = DB::table('message_queues')->distinct()->get(['group_id']);

            foreach ($groups as $group) {
                // Get messages
                $message_queues = MessageQueue::where('group_id', $group->group_id)->where('sending_time', '<=', Carbon::now())->where('sent', 0)->where('status', '!=', 1)->orderBy('sending_time', 'ASC')->limit(20);

                // Do we have results?
                if (count($message_queues->get()) > 0) {
                    foreach ($message_queues->get() as $message) {
                        if ($message->type == 'message_all') {

                            $customer = Customer::find($message->customer_id);

                            if ($customer && $customer->do_not_disturb == 0) {
                                SendMessageToAll::dispatchNow($message->user_id, $customer, json_decode($message->data, true), $message->id);

                                dump('sent to all');
                            } else {
                                $message->delete();

                                dump('deleting queue');
                            }
                        } else {
                            SendMessageToSelected::dispatchNow($message->phone, json_decode($message->data, true), $message->id, $message->whatsapp_number);

                            dump('sent to selected');
                        }
                    }
                }
            }
        } else {
            dump('Not the right time for sending');
        }

        $report->update(['end_time' => Carbon:: now()]);
    }
}
