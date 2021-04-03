<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckScraperRunningStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:scraper-running-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which scraper is running or not';

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
            'signature'  => $this->signature,
            'start_time' => Carbon::now(),
        ]);

        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') .'scrapper-running.sh 2>&1';

        $allOutput = array();
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);

        

        $serverId       = null;
        $scraperNamestr = null;

        if (!empty($allOutput)) {
            foreach ($allOutput as $k => $allO) {
                if ($k == 0) {
                    continue;
                }
                $allO = preg_replace('/\s+/', '', $allO);
                if (strpos($allO, "#####################Server-") !== false) {
                    $serverArray = explode("#####################Server-", $allO);
                    if (!empty($serverArray[1])) {
                        $serverNameArr = explode("###################ServerLoad", $serverArray[1]);
                        if (!empty(trim($serverNameArr[0]))) {
                            $serverId = trim($serverNameArr[0]);
                            continue;
                        }
                    }
                }

                // start to store scarper name
                $scraperNamestr = null;
                if (strpos($allO, "/root/scraper_nodejs/commands/completeScraps") !== false) {
                    $scriptNames = explode("/root/scraper_nodejs/commands/completeScraps", $allO);
                    if (!empty($scriptNames[1])) {
                        $scraperName = explode("/", $scriptNames[1]);
                        if (count($scraperName) > 2) {
                            $scraperNamestr = $scraperName[1];
                        } else {
                            $scraperNamestr = str_replace(".js", "", $scraperName[1]);
                        }

                    }
                }

                if (!empty($scraperNamestr)) {
                    $status = \App\ScraperServerStatusHistory::create([
                        "scraper_name"   => $scraperNamestr,
                        "scraper_string" => $allO,
                        "server_id"      => $serverId,
                    ]);
                }
            }
        }

        $report->update(['end_time' => Carbon::now()]);

    }
}
