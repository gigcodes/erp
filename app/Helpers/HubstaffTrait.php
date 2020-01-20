<?php

namespace App\Helpers;

use Exception;
use FacebookAds\Http\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Storage;

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
        $tokens = $this->getTokens();
        $this->generateAccessToken($tokens->refresh_token);
    }

    private function getTokens()
    {
        if (!Storage::disk('local')->exists($this->HUBSTAFF_TOKEN_FILE_NAME)) {
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

            return Storage::disk('local')->put($this->HUBSTAFF_TOKEN_FILE_NAME, json_encode($tokens));
        } catch (Exception $e) {
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
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $functionToDo($tokens->access_token);
                }
            } else {
                throw $e;
            }
        }
    }
}
