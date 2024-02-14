<?php

namespace App\Console\Commands;

use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class PushSizeToMagento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'size:push-to-mangento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Size to magento';

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
            $website = \App\StoreWebsite::where('website_source', 'magento')->where('api_token', '!=', '')->get();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Website query finished => ' . json_encode($website->toArray())]);
            $sizes = \App\Size::all();
            foreach ($sizes as $s) {
                echo 'Size Started  : ' . $s->name;
                LogHelper::createCustomLogForCron($this->signature, ['message' => 'Size Started  : ' . $s->name]);
                foreach ($website as $web) {
                    echo 'Store Started  : ' . $web->website;
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store Started  : ' . $web->website]);
                    $checkSite = \App\StoreWebsiteSize::where('size_id', $s->id)->where('store_website_id', $web->id)->where('platform_id', '>', 0)->first();
                    LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store website size query finished. => ' . json_encode($checkSite->toArray())]);
                    if (! $checkSite) {
                        $id = \seo2websites\MagentoHelper\MagentoHelper::addSize($s, $web);
                        if (! empty($id)) {
                            \App\StoreWebsiteSize::where('size_id', $s->id)->where('store_website_id', $web->id)->delete();
                            $sws = new \App\StoreWebsiteSize;
                            $sws->size_id = $s->id;
                            $sws->store_website_id = $web->id;
                            $sws->platform_id = $id;
                            $sws->save();
                            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Store website size added.']);
                        }
                    }
                }
            }
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was ended.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
