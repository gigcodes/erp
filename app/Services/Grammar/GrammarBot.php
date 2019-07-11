<?php


namespace App\Services\Grammar;


use GuzzleHttp\Client;

class GrammarBot
{
    private $request;
    public function __construct(Client $client)
    {
        $this->request = $client;
    }

    public function validate($text)
    {
        sleep(1.2);
        try {
            $response = $this->request->request('POST', 'https://api.cognitive.microsoft.com/bing/v7.0/SpellCheck', [
                'form_params' => [
//                    'mode' => 'Spell',
                    'text' => $text
                ],
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => 'fdcfc2cb689346a39265829bb50bf39b',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);
        } catch (\Exception $exception) {
            return false;
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if ($data['flaggedTokens'] === []) {
            return $text;
        }

        return false;

    }
}