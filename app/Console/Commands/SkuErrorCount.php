<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Loggers\LogScraper;
use App\HistorialData;

class SkuErrorCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sku-error:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs Every Hours stores the SKU Regrex error logs';

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
        $errorCount = 0;
        $logs = LogScraper::where('validation_result', 'LIKE', '%SKU failed regex test%')->count();
        $data = new HistorialData();
        $data->object = 'sku_log';
        $data->measuring_point = now().' '.$logs;
        $data->value = $logs;
        $data->save();

    }
}
