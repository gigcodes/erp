<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\NegativeCouponResponse;
use Illuminate\Console\Command;

class NegativeCouponResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:NegativeCouponResponses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Negative Coupon Response';

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
        try {
            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $storeWebsites = \App\StoreWebsite::select('store_websites.id', 'store_websites.api_token', 'store_websites.website')->where('api_token', '!=', '')->where('website_source', 'magento')->get();
            foreach ($storeWebsites as $storeWebsite) {
                $authorization = 'Authorization: Bearer ' . $storeWebsite->api_token;
                // Init cURL
                $curl = curl_init();
                $url = "'https://dev6.sololuxury.com/rest/V1/coupon/logs/'";
                // Set cURL options
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => [
                        'content-type: application/json',
                        $authorization,
                    ],
                ]);

                // Get response
                $response = curl_exec($curl);

                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                LogRequest::log($startTime, $url, 'POST', json_encode([]), json_decode($response), $httpcode, \App\Console\Commands\NegativeCouponResponses::class, 'handle');

                // Get possible error
                $err = curl_error($curl);

                // Close cURL
                curl_close($curl);

                // Check for errors
                if ($err) {
                    // Log error
                    //\Log::error('Get Negative Coupon Response Error : =>'.$err);
                }
                $convertJson = is_array($response) ? json_encode($response) : $response;
                //\DB::enableQueryLog();
                $user_id = \Auth::user()->id ?? '';
                NegativeCouponResponse::create(
                    [
                        'store_website_id' => $storeWebsite->id,
                        'user_id' => $user_id,
                        'website' => $storeWebsite->website,
                        'response' => $convertJson,
                    ]
                );
                //dd(\DB::getQueryLog());
            }
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
