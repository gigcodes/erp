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

    public function validate($text) {


//        $request = new Http_Request2('https://api.cognitive.microsoft.com/bing/v7.0/spellcheck/');
//        $url = $request->getUrl();
//
//        $headers = array(
//            // Request headers
//            'Content-Type' => 'application/x-www-form-urlencoded',
//            'Ocp-Apim-Subscription-Key' => '{subscription key}',
//        );
//
//        $request->setHeader($headers);
//
//        $parameters = array(
//            // Request parameters
//            'mode' => '{string}',
//            'mkt' => '{string}',
//        );


        dump($text);
        $response = $this->request->request('POST', 'https://api.cognitive.microsoft.com/bing/v7.0/spellcheck', [
            'form_params' => [
                'mode' => 'Proof',
                'text' => $text
            ],
            'headers' => [
                'Ocp-Apim-Subscription-Key' => 'API_KEY'
            ]
        ]);

        dump($response->getBody()->getContents());
    }
}