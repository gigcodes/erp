<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait hubstaffTrait
{
    private $HUBSTAFF_TOKEN_FILE_NAME = 'hubstaff_tokens.json';
    private $SEED_REFRESH_TOKEN;

    public function init($seedToken)
    {
        $this->SEED_REFRESH_TOKEN = $seedToken;
    }

    private function checkSeedToken()
    {
        if (!$this->SEED_REFRESH_TOKEN) {
            throw new Exception('Seed token not initialized');
        }
    }

    private function refreshTokens()
    {
        $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
    }

    private function getTokens($force = false)
    {
        if (!Storage::disk('local')->exists($this->HUBSTAFF_TOKEN_FILE_NAME) || $force == true) {
            $this->generateAccessToken($this->SEED_REFRESH_TOKEN);
        }
        $tokens = json_decode(Storage::disk('local')->get($this->HUBSTAFF_TOKEN_FILE_NAME));
        return $tokens;
    }

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
            $tokens       = [
                'access_token'  => $responseJson->access_token,
                'refresh_token' => $responseJson->refresh_token,
                'expires_in'    => $responseJson->expires_in,
            ];

            return Storage::disk('local')->put($this->HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
            // we need to send email and whatsapp
            /*$requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add([
                'priority'    => 1,
                'issue'       => $e->getMessage(),
                'status'      => "Planned",
                'module'      => "Hubstaff",
                'subject'     => "Hubstaff token regenerate issue - create a personal token if expired",
                'assigned_to' => \App\Setting::get("cron_issue_assinged_to",6),
            ]);
            app('App\Http\Controllers\DevelopmentController')->issueStore($requestData, 'issue');*/

            \Log::info("Hubstaff token regenerate issue - create a personal token if expired");

            return false;
        }
    }

    public function doHubstaffOperationWithAccessToken($functionToDo, $shouldRetry = true)
    {
        $this->checkSeedToken();
        $tokens = $this->getTokens();
        try {
            return $functionToDo($tokens->access_token);
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                echo "Got error";
                $this->refreshTokens();
                if ($shouldRetry) {
                    echo "Retrying";
                    $tokens = $this->getTokens();
                    return $functionToDo($tokens->access_token);
                }
            } else {
                throw $e;
            }
        }
    }
}
