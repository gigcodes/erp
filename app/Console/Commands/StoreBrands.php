<?php

namespace App\Console\Commands;

use App\Brand;
use App\Supplier;
use Carbon\Carbon;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class StoreBrands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:store-brands-from-supplier';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $supplierBrands = Supplier::select('brands')->whereNotNull('brands')->get()->all();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Supplier query finished.']);
            $brandsArray = [];
            $brandsTableArray = [];
            foreach ($supplierBrands as $key => $value) {
                array_push($brandsArray, str_replace('[', '', str_replace(']', '', explode(',', $value->brands))));
            }
            $brands = array_filter(str_replace('"', '', array_unique(array_map('strtolower', array_reduce($brandsArray, 'array_merge', [])))));
            $brandsInBrands = Brand::select('name')->whereNotNull('name')->get()->all();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Brand query finished.']);
            foreach ($brandsInBrands as $key => $value) {
                array_push($brandsTableArray, trim($value->name));
            }
            $brandsTable = array_unique(array_map('strtolower', array_filter($brandsTableArray)));
            foreach ($brands as $key => $value) {
                $value = trim($value);
                if (! in_array($value, $brandsTable)) {
                    $params = [
                        'name' => $value,
                        'created_at' => Carbon::now(),
                    ];
                    $brandsTable[] = $value;
                    Brand::create($params);
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Brand added.']);
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
