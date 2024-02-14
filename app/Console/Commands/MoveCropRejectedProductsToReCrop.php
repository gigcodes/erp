<?php

namespace App\Console\Commands;

use App\Product;
use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;

class MoveCropRejectedProductsToReCrop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recrop:send-to-recrop-and-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $products = Product::where('is_crop_rejected', 1)->where('crop_remark', 'LIKE', '%sequence%')->get();

            foreach ($products as $key => $product) {
                dump('Reverting....' . $key);
                $product->is_crop_approved = 1;
                $product->crop_approved_at = Carbon::now()->toDateTimeString();
                $product->crop_approved_by = 109;
                $product->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
