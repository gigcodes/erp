<?php

namespace App\Console\Commands;

use App\ProductPushErrorLog;
use App\Loggers\LogListMagento;
use Illuminate\Console\Command;

class DeleteMagentoPushLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento-log:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Magento Push log';

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
        //
        $logs = LogListMagento::all();

        if (! $logs->isEmpty()) {
            foreach ($logs as $log) {
                echo  $log->product_id . " Started to delete \n";
                $product = ProductPushErrorLog::where('product_id', $log->product_id)->get();
                if (! $product->isEmpty()) {
                    foreach ($product as $p) {
                        $p->delete();
                    }
                }
                $log->delete();
            }
        }
    }
}
