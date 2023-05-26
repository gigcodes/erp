<?php

namespace App\Helpers;

class SocialHelper
{
    public static function curlGetRequest($url)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return $response;
    }

    public static function curlPostRequest($url, $post_params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        //   curl_setopt($ch, CURLOPT_USERPWD, $user_cred);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            //echo 'Error:' . curl_error($ch);
            return curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }
}
