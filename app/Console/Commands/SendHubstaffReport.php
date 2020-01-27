<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Helpers\hubstaffTrait;
use App\Hubstaff\HubstaffMember;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;

class SendHubstaffReport extends Command
{
    use hubstaffTrait;

    private $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:send_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends hubstaff report to whatsapp based every hour with details of past hour and today';

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
        $userPastHour =  $this->getActionsForPastHour();

        echo print_r($userPastHour);
        
        $userToday = $this->getActionsForToday();

        echo print_r($userToday);
        
        $users = DB::table('users')
            ->join('hubstaff_members', 'hubstaff_members.user_id', '=', 'users.id')
            ->select(['hubstaff_user_id', 'name'])
            ->get();

        $report = array();
        foreach ($users as $user) {

            $pastHour = (isset($userPastHour[$user->hubstaff_user_id])
                ? $this->formatSeconds($userPastHour[$user->hubstaff_user_id])
                : '0');

            $today = (isset($userToday[$user->hubstaff_user_id])
                ? $this->formatSeconds($userToday[$user->hubstaff_user_id])
                : '0');

            if($today != '0'){
                $message = $user->name . ' ' .  $pastHour . ' ' . $today;
                $report[] = $message;
            }
        }

        $message = implode(PHP_EOL, $report);

        ChatMessage::sendWithChatApi('971502609192',null, $message);
    }

    private function formatSeconds($seconds)
    {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    }


    private function getActionsForPastHour()
    {
        $stop =  gmdate("c");
        $time   = strtotime($stop);
        $time   = $time - (60 * 60); //one hour
        $start = gmdate("c", $time);

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

        $users = array();

        foreach ($responseJson->activities as $activity) {

            if (isset($users[$activity->user_id])) {
                $users[$activity->user_id] += $activity->tracked;
            } else {
                $users[$activity->user_id] = $activity->tracked;
            }
        }

        return $users;
    }

    private function getActionsForToday()
    {

        $now = date('Y-m-d H:i:s');
        $time   = strtotime($now);
        $start = date('Y-m-d', $time);
        $time = $time + (24 * 60 * 60);
        $stop = date('Y-m-d', $time);

        //https://api.hubstaff.com/v2/organizations/:organization_id/activities/daily?date[start]=2020-01-08T00:00:00+0&date[stop]=2020-01-08T05:30:00+0
        $response = $this->doHubstaffOperationWithAccessToken(
            function ($accessToken) use ($start, $stop) {
                $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/activities/daily?date[start]=' . $start . '&date[stop]=' . $stop;
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

        $users = array();

        foreach ($responseJson->daily_activities as $activity) {

            if (isset($users[$activity->user_id])) {
                $users[$activity->user_id] += $activity->tracked;
            } else {
                $users[$activity->user_id] = $activity->tracked;
            }
        }
        return $users;
    }
}
