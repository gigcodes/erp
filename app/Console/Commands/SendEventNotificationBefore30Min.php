<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\UserEvent\UserEvent;

class SendEventNotificationBefore30Min extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:event-notification30min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Event notification before 30 min';

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

            // get the events which has 24 hr left
            $events = UserEvent::where('start', '>', \DB::raw('NOW() - INTERVAL 30 MINUTE'))->where('start', '<', \DB::raw('NOW() - INTERVAL 30 MINUTE'))->get();

                    $userWise = [];
                    if (!$events->isEmpty()) {
                        foreach ($events as $event) {
                            $userWise[$event->user_id][] = $event;
                        }
                    }

                    if (!empty($userWise)) {
                        foreach ($userWise as $id => $events) {
                            // find user into database
                            $user = \App\User::find($id);
                            // if user exist
                            if (!empty($user)) {
                                $notification   = [];
                                $notification[] = "Following Event Schedule on within the next 30 minute";
                                $no             = 1;
                                foreach ($events as $event) {
                                    $notification[] = $no . ") [" . $event->start . "] => " . $event->subject;
                                    $no++;
                                }

                                $params['user_id'] = $user->id;
                                $params['message'] = implode("\n", $notification);
                                // send chat message
                                $chat_message = ChatMessage::create($params);
                                // send
                                app('App\Http\Controllers\WhatsAppController')
                                    ->sendWithWhatsApp($user->phone, null, $params['message'], false, $chat_message->id);
                            }
                        }
                    }

                    //

                    $report->update(['end_time' => Carbon::now()]);
                } catch (\Exception $e) {
                    \App\CronJob::insertLastError($this->signature, $e->getMessage());
                }
            }
        }
