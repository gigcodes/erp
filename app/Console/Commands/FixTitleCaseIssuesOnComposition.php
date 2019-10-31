<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;
use App\CronJobReport;

class FixTitleCaseIssuesOnComposition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:fix-titlecase-for-composition';

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
        $report = CronJobReport::create([
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
        ]);


        Product::chunk(1000, function($products) {
            foreach ($products as $product) {
                dump($product->id);
                $product->composition = title_case($product->composition);
                $product->save();
            }
        });

        $report->update(['end_time' => Carbon:: now()]);
    }
}
