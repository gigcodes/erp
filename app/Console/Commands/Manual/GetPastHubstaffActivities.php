<?php

namespace App\Console\Commands\Manual;

use App\Helpers\hubstaffTrait;
use App\Hubstaff\HubstaffActivity;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class GetPastHubstaffActivities extends Command
{
    private $HUBSTAFF_ACTIVITY_LAST_SYNC_FILE_NAME = 'hubstaff_activity_sync.json';

    use hubstaffTrait;

    private $client;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:load_past_activities {start=2019-09-01} {end=2020-04-18} {user_ids=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load past Hubstaff Activities till time';

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

        $now = time();

        $startString = $this->argument('start');
        $endString   = $this->argument('end');
        $userIds     = $this->argument('user_ids');
        $userIds     = explode(",", $userIds);
        $userIds     = array_filter($userIds);

        $start = strtotime($startString . ' UTC');
        $now   = strtotime($endString . ' UTC');

        while ($start < $now) {
            $end = $start + 7 * 24 * 60 * 60; // 1 week limited by API

            echo '=====================' . PHP_EOL;
            echo 'Start: ' . gmdate('c', $start) . PHP_EOL;
            echo 'End: ' . gmdate('c', $end) . PHP_EOL;

            $activities = $this->getActivitiesBetween(gmdate('c', $start), gmdate('c', $end), 0, [], $userIds);
            
            echo "Got activities(count): " . sizeof($activities) . PHP_EOL;
            foreach ($activities as $id => $data) {
                HubstaffActivity::updateOrCreate(
                    [
                        'id' => $id,
                    ],
                    [
                        'user_id'   => $data['user_id'],
                        'task_id'   => is_null($data['task_id']) ? 0 : $data['task_id'],
                        'starts_at' => $data['starts_at'],
                        'tracked'   => $data['tracked'],
                        'keyboard'  => $data['keyboard'],
                        'mouse'     => $data['mouse'],
                        'overall'   => $data['overall'],
                    ]
                );
            }

            sleep(5);

            $start = $end;
        }
    }

    private function getActivitiesBetween($startTime, $endTime, $startId = 0, $resultArray = [], $userIds = [])
    {

        try {
            $response = $this->doHubstaffOperationWithAccessToken(
                function ($accessToken) use ($startTime, $endTime, $startId, $userIds) {
                    $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/activities?time_slot[start]=' . $startTime . '&time_slot[stop]=' . $endTime . '&page_start_id=' . $startId;

                    $q = [];
                    if (!empty($userIds)) {
                        foreach ($userIds as $uid) {
                            $q[] = "user_ids[]=" . $uid;
                        }
                    }
                    $queryString = implode("&", $q);
                    $url .= "&" . $queryString;

                    return $this->client->get(
                        $url,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer ' . $accessToken,
                            ],
                        ]
                    );
                }
            );
            $responseJson = json_decode($response->getBody()->getContents());

            $activities = $resultArray;

            foreach ($responseJson->activities as $activity) {
                $activities[$activity->id] = array(
                    'user_id'   => $activity->user_id,
                    'task_id'   => $activity->task_id,
                    'starts_at' => $activity->starts_at,
                    'tracked'   => $activity->tracked,
                    'keyboard'  => $activity->keyboard,
                    'mouse'     => $activity->mouse,
                    'overall'   => $activity->overall,
                );
            }

            if (isset($responseJson->pagination)) {
                $nextStart = $responseJson->pagination->next_page_start_id;
                return $this->getActivitiesBetween($startTime, $endTime, $nextStart, $activities, $userIds);
            } else {
                return $activities;
            }
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return false;
        }
    }
}
