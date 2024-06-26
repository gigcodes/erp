<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\CronJobReport;
use App\PageScreenshots;
use Illuminate\Console\Command;
use App\Services\Bots\Screenshot;

class GetPageScreenshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenshot:sites';

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
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $sites = PageScreenshots::where('image_link', '')->get();

            $duskShell = new Screenshot();
            $duskShell->prepare();

            foreach ($sites as $site) {
                $duskShell->emulate($this, $site, '');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
