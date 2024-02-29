<?php

namespace App\Console\Commands;

use App\Order;
use App\Coupon;
use Carbon\Carbon;
use App\StoreWebsite;
use App\CronJobReport;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use GuzzleHttp\Cookie\CookieJar;

class OrderCouponMerger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:merge-coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get orders attached with coupons from API and merge over the portal';

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
            $report = CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $cookieJar = CookieJar::fromArray([
                '__cfduid' => 'd866a348dc8d8be698f25655b77ada8921560006391',
            ], '.giglio.com');
            try {
                $guzzle                   = new Client();
                $get_api_endpoint_details = StoreWebsite::get();
                if (! empty($get_api_endpoint_details)) {
                    foreach ($get_api_endpoint_details as $store_detail) {
                        if ($store_detail->magento_url != null && $store_detail->api_token != null) {
                            $response        = $guzzle->request('GET', $store_detail->magento_url . '/rest/V1/orders?searchCriteria[filter_groups][0][filters][0][field]=coupon_code&searchCriteria[filter_groups][0][filters][0][value]=&searchCriteria[filter_groups][0][filters][0][condition_type]=notnull', ['headers' => ['Authorization' => 'Bearer ' . $store_detail->api_token]]);
                            $response_object = json_decode($response->getBody()->getContents());
                            if (json_last_error() == JSON_ERROR_NONE) {
                                if (! empty($response_object)) {
                                    if (! empty($response_object->items)) {
                                        foreach ($response_object->items as $obj) {
                                            if ($obj->coupon_code != '') {
                                                //Check if coupon is valid
                                                $get_coupon_id = Coupon::where('code', $obj->coupon_code)->first();
                                                if (! empty($get_coupon_id)) {
                                                    foreach ($obj->items as $item) {
                                                        //Check if order id exists in record
                                                        $get_order_id = Order::where('order_id', $item->order_id)->first();
                                                        if (! empty($get_order_id)) {
                                                            //Merge coupon with order is all goes well
                                                            $update_order_table_with_coupon = Order::where('id', $get_order_id->id)->update(['coupon_id' => $get_coupon_id->id]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        \App\CronJob::insertLastError($this->signature, 'No record found');
                                    }
                                } else {
                                    \App\CronJob::insertLastError($this->signature, 'No record found');
                                }
                            }
                        }
                    }
                    exit;
                } else {
                    \App\CronJob::insertLastError($this->signature, 'Could not find magento url or token');
                }
            } catch (Guzzle\Http\Exception\BadResponseException $e) {
                \App\CronJob::insertLastError($this->signature, $response->getBody()->getContents());
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
