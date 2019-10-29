<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use App\CronJobReport;
use Illuminate\Console\Command;

class UpdateCustomerSizeFromOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-customer-size-from-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update customer size from order';

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
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
     ]);

        $orders = DB::table('orders')
                        ->join('order_products', function($query) {
                            $query->on('orders.id', '=', 'order_products.order_id');
                            $query->where('order_products.size', '<>', '');
                        })
                        ->join('customers', function($query) {
                            $query->on('customers.id', '=', 'orders.customer_id');
                            $query->whereNull('customers.shoe_size');
                        })
                        ->select(['order_products.size', 'customers.id'])
                        ->groupBy('customers.id')
                        ->get();
        if ($orders) {
            foreach ($orders as $order) {
                if ($order->id) {
                    \App\Customer::where('id', $order->id)->update(['shoe_size' => $order->size]);
                }
            }
        }
        echo 'Successfully update!!';

         $report->update(['end_time' => Carbon:: now()]);
    }
}
