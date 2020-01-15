<?php

namespace App\Services\Whatsapp\ChatApi;

class ChatApi
{

    /**
     * Get instance from whatsapp number
     *
     */
    private function getInstance($number = null)
    {
        $number = !empty($number) ? $number : 0;

        return isset(config("apiwha.instances")[$number])
            ? config("apiwha.instances")[$number]
            : config("apiwha.instances")[0];

    }

    /**
     *
     * Get Queues from Chat-Api
     *
     * @param null $number
     * @return mixed
     *
     */
    public static function chatQueue($number = null)
    {
        function getInstance($number)
        {
            $number = !empty($number) ? $number : 0;
            return isset(config("apiwha.instances")[$number])
                ? config("apiwha.instances")[$number]
                : config("apiwha.instances")[0];
        }

        $instance = getInstance($number);
        /*        dd($instance);*/
        $instanceId = isset($instance["instance_id"]) ? $instance["instance_id"] : 0;
        $token = isset($instance["token"]) ? $instance["token"] : 0;

        $waiting = 0;

        if (!empty($instanceId) && !empty($token)) {
            // executing curl
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/showMessagesQueue?token=$token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                // throw some error if you want
            } else {
                $result = json_decode($response, true);
            }

        }

        return $result;
    }

    /**
     * Get Chat history from Chat-Api
     *
     * @param null $number
     * @return mixed
     *
     */
    public static function chatHistory($number = null)
    {
        function getInstance($number)
        {
            $number = !empty($number) ? $number : 0;
            return isset(config("apiwha.instances")[$number])
                ? config("apiwha.instances")[$number]
                : config("apiwha.instances")[0];
        }

        $instance = getInstance($number);
        $instanceId = isset($instance["instance_id"]) ? $instance["instance_id"] : 0;
        $token = isset($instance["token"]) ? $instance["token"] : 0;
        $waiting = 0;
        if (!empty($instanceId) && !empty($token)) {
            // executing curl
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/messages?token=$token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                // throw some error if you want
            } else {
                $result = json_decode($response, true);
            }
        }

        return $result;

    }

    /**
     * Delete Chat Queue from chat Api
     *
     * @param null $number
     * @return mixed
     */
    public static function deleteQueues($number = null)
    {
        function getInstance($number)
        {
            $number = !empty($number) ? $number : 0;
            return isset(config("apiwha.instances")[$number])
                ? config("apiwha.instances")[$number]
                : config("apiwha.instances")[0];
        }

        $instance = getInstance($number);
        /*        dd($instance);*/
        $instanceId = isset($instance["instance_id"]) ? $instance["instance_id"] : 0;
        $token = isset($instance["token"]) ? $instance["token"] : 0;

        $waiting = 0;

        if (!empty($instanceId) && !empty($token)) {
            // executing curl
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.chat-api.com/instance$instanceId/clearMessagesQueue?token=$token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                // throw some error if you want
            } else {
                $result = json_decode($response, true);
            }

        }

        return $result;
    }
}