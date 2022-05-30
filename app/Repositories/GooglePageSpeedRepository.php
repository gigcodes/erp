<?php

namespace App\Repositories;


class GooglePageSpeedRepository
{
    public function generateReport($gtmatrix){
        
        $Api_key = env('PAGESPEEDONLINE_API_KEY'); 
        $curl = curl_init();
        $url = $gtmatrix->website_url;
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'https://' . ltrim(strtolower($url), '/');
        }
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url='.$url.'&key='.$Api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $JsonfileName = '/uploads/speed-insight/' . $gtmatrix->test_id . '_pagespeedInsight.json';
        $Jsonfile     = public_path() . $JsonfileName;
        file_put_contents($Jsonfile,$response);
        $gtmatrix->pagespeed_insight_json = $JsonfileName;
        $gtmatrix->save();
    }
}