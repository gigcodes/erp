<?php

namespace App\Http\Controllers\ChatGPT;

class ChatGPTService
{
    private $api_key = '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';
    private $base_api = 'https://api.openai.com/v1/';

    public function getCompletions($prompt)
    {
        $params = [
            "model" => "text-davinci-003",
            "prompt" => $prompt,
            "temperature" => 0.7,
            "max_tokens" => 1024
        ];
        return $this->callApi('POST', 'completions', $params);
    }

    /**
     * Common function to fetch data from API using CURL.
     */
    public function callApi($method, $url, array $params = []): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->base_api . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api_key,
            ],
        ]);

        if ($method == 'POST' || $method == 'PATCH' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return ['status' => false, 'message' => 'cURL Error #:' . $err];
        } else {
            $response = json_decode($response, true);
            if (is_array($response)) {
                return ['status' => true, 'message' => 'Data found', 'data' => $response];
            } else {
                if ($method == 'DELETE') {
                    return ['status' => true, 'message' => 'Data found', 'data' => $response];
                } else {
                    return ['status' => false, 'message' => 'cURL Error #:' . $response];
                }
            }
        }
    }
}
