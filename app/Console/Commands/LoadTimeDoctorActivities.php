<?php

namespace App\Console\Commands;

use Carbon\Carbon;

use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorActivity;
use App\TimeDoctor\TimeDoctorProject;
use App\Library\TimeDoctor\Src\Timedoctor;
use Illuminate\Console\Command;

class LoadTimeDoctorActivities extends Command
{
    public $TIME_DOCTOR_USER_ID;

    public $TIME_DOCTOR_AUTH_TOKEN;

    public $TIME_DOCTOR_COMPANY_ID;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timedoctor:load_time_doctor_activity {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load activities for users per task from TimeDoctor';

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
     * @return int
     */
    public function handle()
    {
        $time_doctor_account = TimeDoctorAccount::find($this->argument('id'));
        $this->TIME_DOCTOR_USER_ID = $time_doctor_account->id;
        $this->TIME_DOCTOR_AUTH_TOKEN = $time_doctor_account->auth_token;
        $this->TIME_DOCTOR_COMPANY_ID = $time_doctor_account->company_id;        

        $timedoctor = Timedoctor::getInstance();        
        try {
            $report = \App\CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            /*$time = strtotime(date('c'));
            $time = $time - ((60 * 60)); //one hour
            $startTime = date('c', strtotime(gmdate('Y-m-d H:i:s', $time)));
            $time = strtotime($startTime);
            $time = $time + (10 * 60); //10 mins
            $stopTime = date('c', $time);*/

            /*$activities = $this->getActivitiesBetween($startTime, $stopTime);*/
            $this->refreshActivityList();            

            /*if ($activities === false) {
                echo 'Error in activities'.PHP_EOL;

                return;
            }
            echo 'Got activities(count): '.count($activities).PHP_EOL;
            foreach ($activities as $id => $data) {
                HubstaffActivity::updateOrCreate(
                    [
                        'id' => $id,
                    ],
                    [
                        'user_id' => $data['user_id'],
                        'task_id' => is_null($data['task_id']) ? 0 : $data['task_id'],
                        'starts_at' => $data['starts_at'],
                        'tracked' => $data['tracked'],
                        'keyboard' => $data['keyboard'],
                        'mouse' => $data['mouse'],
                        'overall' => $data['overall'],
                    ]
                );
            }*/
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function refreshActivityList()
    {
        $timedoctor = Timedoctor::getInstance();        
        try {
            $this->timedoctor = $timedoctor->authenticate(false, $this->TIME_DOCTOR_AUTH_TOKEN);            
            $this->getActivitiesBetween();
        } catch (\Exception $e) {
            $this->timedoctor = $timedoctor->authenticate(true, $this->TIME_DOCTOR_AUTH_TOKEN);
            $this->getActivitiesBetween();
        }        
    }
    

    private function getActivitiesBetween()
    {
        try {
            $activities = $this->timedoctor->getActivityList($this->TIME_DOCTOR_COMPANY_ID, $this->TIME_DOCTOR_AUTH_TOKEN, $this->TIME_DOCTOR_USER_ID);            
            foreach( $activities as $activity){                
                TimeDoctorActivity::create([
                    'user_id' => $activity['user_id'],
                    'task_id' => is_null($activity['task_id']) ? 0 : $activity['task_id'],
                    'starts_at' => $activity['starts_at'],
                    'tracked' => $activity['tracked'],
                    'project_id' => $activity['project'],
                ]);
            }
        } catch (Exception $e) {            
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
            return false;
        }
    }
}
