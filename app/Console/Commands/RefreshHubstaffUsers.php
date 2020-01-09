<?php

namespace App\Console\Commands;

use App\HubstaffMember;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Storage;

define('HUBSTAFF_TOKEN_FILE_NAME', 'hubstaff_tokens.json');
define('SEED_REFRESH_TOKEN', getenv('HUBSTAFF_SEED_PERSONAL_TOKEN'));


class RefreshHubstaffUsers extends Command
{

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
        $this->refreshUserList($this->getTokens()->access_token);
    }

    private function getTokens()
    {
        if (!Storage::disk('local')->exists(HUBSTAFF_TOKEN_FILE_NAME)) {
            $this->generateAccessToken(SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get(HUBSTAFF_TOKEN_FILE_NAME));
        return $tokens;
    }

    /**
     * returns boolean
     */
    private function generateAccessToken(string $refreshToken)
    {
        $httpClient = new Client();
        try{
            $response = $httpClient->post(
                'https://account.hubstaff.com/access_tokens',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken
                    ]
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());

            $tokens = [
                'access_token' => $responseJson->access_token,
                'refresh_token' => $responseJson->refresh_token
            ];

            return Storage::disk('local')->put(HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        }catch(Exception $e){
            return false;
        }
    }

    private function refreshUserList(string $accessToken)
    {
        // 1. try to get the list of users
        // 2. If users recieved update in database
        // 3. if users not recieved due to token failure refresh the token and retry
        $url = 'https://api.hubstaff.com/v2/organizations/' . getenv('HUBSTAFF_ORG_ID') . '/members';
        $httpClient = new Client();
        try {
            $response = $httpClient->get(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $accessToken
                    ]
                ]
            );

            $responseJson = json_decode($response->getBody()->getContents());


            foreach ($responseJson->members as $member) {

                try{
                    $url = 'https://api.hubstaff.com/v2/users/' . $member->user_id;
                    $response = $httpClient->get(
                        $url,
                        [
                            RequestOptions::HEADERS => [
                                'Authorization' => 'Bearer ' . $accessToken
                            ]
                        ]
                    );

                    $userResponseJson = json_decode($response->getBody()->getContents());
                    $member->email = $userResponseJson->user->email;
                    
                }catch(Exception $e){
                    // do nothing
                }

                //eloquent insert
                HubstaffMember::updateOrCreate(
                    [
                        'hubstaff_user_id' => $member->user_id,
                    ],
                    [
                        'hubstaff_user_id' => $member->user_id,
                        'email' => $member->email
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
