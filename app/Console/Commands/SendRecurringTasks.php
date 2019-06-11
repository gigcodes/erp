<?php

namespace App\Console\Commands;

use App\Task;
use App\ChatMessage;
use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:recurring-tasks';

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
        'start_time'  => Carbon::now()
      ]);

      $today_date = Carbon::now()->format('Y-m-d');
      $today_weekday = strtoupper(Carbon::now()->format('l'));
      $today_day = Carbon::now()->format('d');
      $today_month = Carbon::now()->format('m');

      $tasks = Task::where('is_statutory', 1)->whereNull('is_completed')->whereNotNull('recurring_type')->get();

      $params = [
        'user_id'   => 6,
        'number'    => NULL,
        'approved'  => 0,
        'status'    => 1
      ];

      foreach ($tasks as $task) {
        $sending_date = Carbon::parse($task->created_at)->format('Y-m-d');
        $sending_weekday = strtoupper(Carbon::parse($task->created_at)->format('l'));
        $sending_day = Carbon::parse($task->created_at)->format('d');
        $sending_month = Carbon::parse($task->created_at)->format('m');

        $params['message'] = $task->task_subject . ". " . $task->task_details;
        $params['task_id'] = $task->id;
        $params['user_id'] = $task->assign_from;

        // if (count($task->users) > 0) {
        //   $params['erp_user'] = $task->assign_to;
        // }
        //
        // if (count($task->contacts) > 0) {
        //   $params['contact_id'] = $task->assign_to;
        // }

        $can_send_message = false;

        switch ($task->recurring_type) {
          case "EveryDay":
            if ($today_date >= $sending_date) {
              dump('Send Recurring Task Daily');

              $can_send_message = true;
            }

            break;
          case "EveryWeek":
            if ($today_date >= $sending_date && $today_weekday == $sending_weekday) {
              dump('Send Recurring Task Weekly');

              $can_send_message = true;
            }

            break;
          case "EveryMonth":
            if ($today_day == $sending_day) {
              dump('Send Recurring Task Monthly');

              $can_send_message = true;
            }

            break;
          case "EveryYear":
            if ($today_day == $sending_day && $today_month == $sending_month) {
              dump('Send Recurring Task Yearly');

              $can_send_message = true;
            }

            break;
          default:

            break;
        }

        if ($can_send_message) {
          if (count($task->users) > 0) {
   				 foreach ($task->users as $key => $user) {
   					 if ($key == 0) {
   						 $params['erp_user'] = $user->id;
   					 } else {
   						 app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
   					 }
   				 }
     		 }

     		 if (count($task->contacts) > 0) {
     			 foreach ($task->contacts as $key => $contact) {
     				 if ($key == 0) {
     					 $params['contact_id'] = $task->assign_to;
     				 } else {
     					 app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, NULL, $params['message']);
     				 }
     			 }
     		 }

         $chat_message = ChatMessage::create($params);

         $myRequest = new Request();
         $myRequest->setMethod('POST');
         $myRequest->request->add(['messageId' => $chat_message->id]);

         app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);
        }
      }

      $report->update(['end_time' => Carbon:: now()]);
    }
}
