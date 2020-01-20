<?php

namespace App\Console\Commands;

use App\Helpers\hubstaffTrait;
use App\Hubstaff\HubstaffActivity;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Storage;

class LoadHubstaffActivities extends Command
{

    private $HUBSTAFF_ACTIVITY_LAST_SYNC_FILE_NAME = 'hubstaff_activity_sync.json';

    use hubstaffTrait;


    private $client;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:load_activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load activities for users per task from Hubstaff';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
        $this->init(getenv('HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        if (Storage::disk('local')->exists($this->HUBSTAFF_ACTIVITY_LAST_SYNC_FILE_NAME)) {
            $startTime = json_decode(Storage::disk('local')->get($this->HUBSTAFF_ACTIVITY_LAST_SYNC_FILE_NAME))->time;
        } else {
            $time   = strtotime(date("c"));
            $time   = $time - (60 * 60); //one hour
            $startTime = date("c", $time);
        }

        $time = strtotime($startTime);
        $time   = $time + (60 * 60); //one hour
        $stopTime = date("c", $time);

        $activities = $this->getActivitiesBetween($startTime,  $stopTime);
        if ($activities === false) {
            echo 'Error in activities' . PHP_EOL;
            return;
        }

        Storage::disk('local')->put(
            $this->HUBSTAFF_ACTIVITY_LAST_SYNC_FILE_NAME,
            json_encode([
                'time' => $stopTime
            ])
        );

        foreach ($activities as $id => $data) {
            HubstaffActivity::updateOrCreate(
                [
                    'id' => $id
                ],
                [
                    'user_id' => $data['user_id'],
                    'task_id' => $data['task_id'],
                    'starts_at' => $data['starts_at'],
                    'tracked' => $data['tracked']
                ]
            );
        }
    }

    private function getActivitiesBetween($start, $stop)
    {

        try {
            $response = $this->doHubstaffOperationWithAccessToken(
                function ($accessToken) use ($start, $stop) {
                    $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/activities?time_slot[start]=' . $start . '&time_slot[stop]=' . $stop;
                    return $this->client->get(
                        $url,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer ' . $accessToken
                            ]
                        ]
                    );
                }
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $activities = array();

            foreach ($responseJson->activities as $activity) {


                $activities[$activity->id] = array(
                    'user_id' => $activity->user_id,
                    'task_id' => $activity->task_id,
                    'starts_at' => $activity->starts_at,
                    'tracked' => $activity->tracked
                );
            }

            return $activities;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return false;
        }
    }
}
