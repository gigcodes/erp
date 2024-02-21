<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\AppointmentRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckAppointment
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        for ($i=0; $i < 10; $i++) { 
            $this->getAppointments();
            sleep(8);
        }
    }

    public function getAppointments(){
        $currentDateTimeFormatted = Carbon::now()->format('Y-m-d H:i:s');

        $newAppointments = AppointmentRequest::with([
                'user' => function ($query) {
                    $query->select('id', 'name');
                }])
            ->where('requested_time', '<=', $currentDateTimeFormatted)
            ->where('requested_time_end', '>=', $currentDateTimeFormatted)
            ->where('request_status', 0)
            ->orderBy('id', 'DESC')
            ->groupBy(['requested_user_id'])
            ->distinct('requested_user_id')
            ->get();

        $reactedUnseenAppointments = AppointmentRequest::with([
                'userrequest' => function ($query) {
                    $query->select('id', 'name');
                }])
            ->where('request_status', '!=', 0)
            ->where('is_view', 0)
            ->orderBy('id', 'DESC')
            ->groupBy(['user_id'])
            ->distinct('user_id')
            ->get();

        $userAppointments = [];

        $newAppointments->each(function ($appointment) use (&$userAppointments) {
            $userId = $appointment->requested_user_id;
        
            if (!isset($userAppointments[$userId]['newAppointments'])) {
                $userAppointments[$userId]['newAppointments'] = [];
            }
        
            $userAppointments[$userId]['newAppointments'][] = $appointment;
            $userAppointments[$userId]['userId'] = $userId;
        });

        $reactedUnseenAppointments->each(function ($appointment) use (&$userAppointments) {
            $userId = $appointment->user_id;
        
            if (!isset($userAppointments[$userId]['reactedUnseenAppointments'])) {
                $userAppointments[$userId]['reactedUnseenAppointments'] = [];
            }
        
            $userAppointments[$userId]['reactedUnseenAppointments'][] = $appointment;
            $userAppointments[$userId]['userId'] = $userId;
        });

        foreach ($userAppointments as $appointment) {
            event(new \App\Events\AppointmentFound($appointment));
        }
    }
}
