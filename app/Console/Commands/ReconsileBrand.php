<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use App\Helpers\LogHelper;

class ReconsileBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconsile:brand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconsile brand Everyday';

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
        try {
            $report = \App\CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            $storeWebsites = \App\StoreWebsite::where('website_source', 'magento')->get();
            if (! $storeWebsites->isEmpty()) {
                foreach ($storeWebsites as $storeWebsite) {
                    $requestData = new Request();
                    $requestData->setMethod('POST');
                    $requestData->request->add(['store_website_id' => $storeWebsite->id]);
                    app('Modules\StoreWebsite\Http\Controllers\BrandController')->reconsileBrands($requestData);
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
