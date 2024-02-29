<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\DailyActivity;
use App\Helpers\LogHelper;
use App\UserEvent\UserEvent;
use Illuminate\Console\Command;
use App\DailyActivitiesHistories;

class SendDailyPlannerNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-daily-planner-notification';

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
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report added.']);

            // get the events which has 30  OR 05 Min left
            $events = UserEvent::havingRaw('TIMESTAMPDIFF(MINUTE,now() , start) = 30 OR TIMESTAMPDIFF(MINUTE, now(), start) = 05 ')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Event query finished.']);

            $userWise           = [];
            $vendorParticipants = [];

            if (! $events->isEmpty()) {
                foreach ($events as $event) {
                    $userWise[$event->user_id][] = $event;
                    $participants                = $event->attendees;
                    if (! $participants->isEmpty()) {
                        foreach ($participants as $participant) {
                            if ($participant->object == \App\Vendor::class) {
                                $vendorParticipants[$participant->object_id] = $event;
                            }
                        }
                    }
                }
            }

            if (! empty($userWise)) {
                foreach ($userWise as $id => $events) {
                    // find user into database
                    $user = \App\User::find($id);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'user query finished.']);
                    // if user exist
                    if (! empty($user)) {
                        $notification   = [];
                        $notification[] = 'Following Event Schedule on within the next 30 min';
                        $no             = 1;

                        foreach ($events as $event) {
                            $dailyActivities = DailyActivity::where('id', $event->daily_activity_id)->first();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activity query finished.']);
                            $notification[] = $no . ') [' . changeTimeZone($dailyActivities->for_datetime, null, $dailyActivities->timezone) . '] => ' . $event->subject;
                            $no++;

                            $history = [
                                'daily_activities_id' => $event->daily_activity_id,
                                'title'               => 'Sent notification',
                                'description'         => 'To ' . $user->name,
                            ];
                            DailyActivitiesHistories::insert($history);
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activity histroy added.']);
                        }

                        $params['user_id'] = $user->id;
                        $params['message'] = implode("\n", $notification);
                        // send chat message
                        $chat_message = \App\ChatMessage::create($params);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'chat message created.']);
                        // send
                        app(\App\Http\Controllers\WhatsAppController::class)
                            ->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            if (! empty($vendorParticipants)) {
                foreach ($vendorParticipants as $id => $vendorParticipant) {
                    $vendor = \App\Vendor::find($id);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'vendor created.']);
                    if (! empty($vendor)) {
                        $notification   = [];
                        $notification[] = 'Following Event Schedule on within the next 30 min';
                        $no             = 1;
                        foreach ($events as $event) {
                            $dailyActivities = DailyActivity::where('id', $event->daily_activity_id)->first();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activities created.']);

                            $notification[] = $no . ') [' . changeTimeZone($dailyActivities->for_datetime, null, $dailyActivities->timezone) . '] => ' . $event->subject;
                            $no++;
                            $history = [
                                'daily_activities_id' => $event->daily_activity_id,
                                'title'               => 'Sent notification',
                                'description'         => 'To ' . $vendor->name,
                            ];
                            DailyActivitiesHistories::insert($history);
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Daily activities histroy added.']);
                        }

                        $params['vendor_id'] = $vendor->id;
                        $params['message']   = implode("\n", $notification);
                        // send chat message
                        $chat_message = \App\ChatMessage::create($params);
                        LogHelper::createCustomLogForCron($this->signature, ['message' => 'Chat message added.']);
                        // send
                        app(\App\Http\Controllers\WhatsAppController::class)
                            ->sendWithThirdApi($vendor->phone, $vendor->whatsapp_number, $params['message'], false, $chat_message->id);
                    }
                }
            }

            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Report time updated.']);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'corn job ended.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
