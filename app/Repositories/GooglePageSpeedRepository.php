<?php

namespace App\Repositories;


class GooglePageSpeedRepository
{
    public function generateReport($gtmatrix,$gtMatrixAccount){
        
        $Api_key = env('PAGESPEEDONLINE_API_KEY'); 
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://pagespeedonline.googleapis.com/pagespeedonline/v5/runPagespeed?url='.$gtMatrixAccount->website_url.'&key='.$Api_key,
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

        $JsonfileName = '/uploads/speed-insight/' . $gtMatrixAccount->test_id . '_pagespeedInsight.json';
        $Jsonfile     = public_path() . $JsonfileName;
        file_put_contents($Jsonfile,$response);
        $gtmatrix->pagespeed_json_file = $JsonfileName;
        $gtmatrix->save();
    }
}