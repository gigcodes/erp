<?php

namespace App\Console\Commands;

use App\Hubstaff\HubstaffMember;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Storage;
use App\Library\Hubstaff\Src\Hubstaff;


class RefreshHubstaffUsers extends Command
{

    public $HUBSTAFF_TOKEN_FILE_NAME;
    public $SEED_REFRESH_TOKEN;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubstaff:refresh_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the Hubstaff users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';
        $this->SEED_REFRESH_TOKEN       = getenv('HUBSTAFF_SEED_PERSONAL_TOKEN');
        

        // start hubstaff section from here
       $hubstaff = Hubstaff::getInstance();

       echo '<pre>'; print_r($hubstaff); echo '</pre>';exit; 

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        //echo Storage::disk('local')->put('file.txt', 'Contents');
        try {
            $report = \App\CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $this->refreshUserList($this->getTokens()->access_token);
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }

    private function getTokens()
    {
        if (!Storage::disk('local')->exists($this->HUBSTAFF_TOKEN_FILE_NAME)) {
            $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get($this->HUBSTAFF_TOKEN_FILE_NAME));
        return $tokens;
    }

    /**
     * returns boolean
     */
    private function generateAccessToken(string $refreshToken)
    {
        $httpClient = new Client();
        try {
            $response = $httpClient->post(
                'https://account.hubstaff.com/access_tokens',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    ],
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $tokens = [
                'access_token'  => $responseJson->access_token,
                'refresh_token' => $responseJson->refresh_token,
            ];

            return Storage::disk('local')->put($this->HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
            return false;
        }
    }

    private function refreshUserList(string $accessToken)
    {
        // 1. try to get the list of users
        // 2. If users recieved update in database
        // 3. if users not recieved due to token failure refresh the token and retry
        $url        = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/members';
        $httpClient = new Client();
        try {
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());

            foreach ($responseJson->members as $member) {

                try {
                    $url      = 'https://api.hubstaff.com/v2/users/' . $member->user_id;
                    $response = $httpClient->get(
                        $url,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer ' . $accessToken,
                            ],
                        ]
                    );

                    $userResponseJson = json_decode($response->getBody()->getContents());
                    $member->email    = $userResponseJson->user->email;
                } catch (Exception $e) {
                    // do nothing
                }

                //eloquent insert
                HubstaffMember::updateOrCreate(
                    [
                        'hubstaff_user_id' => $member->user_id,
                    ],
                    [
                        'hubstaff_user_id' => $member->user_id,
                        'email'            => $member->email,
                    ]
                );
            }
        } catch (ClientException $e) {

            if ($e->hasResponse()) {

                $errorResponse = $e->getResponse();

                if ($errorResponse->getStatusCode() == 401) {
                    //the token as expired and hence try to generate new access token and retry
                    $this->generateAccessToken($this->getTokens()->refresh_token);
                    $this->refreshUserList($this->getTokens()->access_token);
                }
            }
        }
    }
}
