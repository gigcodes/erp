<?php

namespace App\Console\Commands;

use App\ScrapedProducts;
use Illuminate\Http\Request;
use Illuminate\Console\Command;

class assignIncompleteProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:assign_incomplete_products';

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
        $products = ScrapedProducts::where(['cron_executed' => 0])->get();
        $pids = [];
        foreach ($products as $product) {
            $missing = [];
            if ($product->properties == null) {
                $missing[] = 'Category';
                $missing[] = 'Color';
            } else {
                if (isset($product->properties['category']) && $product->properties['category'] == null) {
                    $missing[] = 'Category';
                }
                if (isset($product->properties['color']) && $product->properties['color'] == null) {
                    $missing[] = 'Color';
                }
            }
            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add([
                'priority' => 1,
                'issue' => implode(',', $missing) . ' missing in scapped products, whose website is ' . $product->website . ' and supplier is ', // issue detail

                'status' => 'Planned',
                'module' => 'Scraper',
                'subject' => implode(',', $missing) . ' missing in scapped products', // enter issue name

                'assigned_to' => 6,
            ]);
            app(\App\Http\Controllers\DevelopmentController::class)->issueStore($requestData, 'issue');
            $pids[] = $product->id;
        }
        $update_scraped_products = ScrapedProducts::whereIn('id', $pids)->update('cron_executed', 1);
    }
}
