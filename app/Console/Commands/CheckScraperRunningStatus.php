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

        /*$cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'scrapper-running.sh 2>&1';

        $allOutput   = array();
        $allOutput[] = $cmd;
        $result      = exec($cmd, $allOutput);*/

        /*$allOutput   = [];
        $allOutput[] = "";
        $allOutput[] = "#####################   Server -   s01 ################### Server Load - 0.27,###############";
        $allOutput[] = "Mon Apr  5 20:00:02 2021  /root/scraper_nodejs/commands/completeScraps/conceptstore.js";*/

        $array = [];
        $array[] = "";
        $array[] = "#####################   Server -   s01 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 2.69 G";
        $array[] = "Used Memory in Percentage = 69.00%";
        $array[] = "16765 5-22:50:59 /root/scraper_nodejs/commands/completeScraps/coltorti/coltorti4.js";
        $array[] = "16984 5-22:45:47 /root/scraper_nodejs/commands/completeScraps/coltorti/coltorti6.js";
        $array[] = "25735 3-01:48:08 /root/scraper_nodejs/commands/completeScraps/externalScraper/main.js";
        $array[] = "#####################   Server -   s02 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 2.24 G";
        $array[] = "Used Memory in Percentage = 58.00%";
        $array[] = "16473 5-03:01:30 /root/scraper_nodejs/commands/completeScraps/tizianafausti.js";
        $array[] = "17781 5-02:20:58 /root/scraper_nodejs/commands/completeScraps/binisilvia.js";
        $array[] = "18054 5-02:15:13 /root/scraper_nodejs/commands/completeScraps/coltorti/coltorti1.js";
        $array[] = "#####################   Server -   s03 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 1.38 G";
        $array[] = "Used Memory in Percentage = 35.00%";
        $array[] = "3851 2-23:32:08 /root/scraper_nodejs/commands/completeScraps/deliberti.js";
        $array[] = "5824 2-21:56:46 /root/scraper_nodejs/commands/completeScraps/sugar.js";
        $array[] = "#####################   Server -   s04 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 2.73 G";
        $array[] = "Used Memory in Percentage = 70.00%";
        $array[] = "572 2-21:53:45 /root/scraper_nodejs/commands/completeScraps/missbaby.js";
        $array[] = "2218 21:29:17 /root/scraper_nodejs/commands/completeScraps/cuccuini.js";
        $array[] = "29940 3-19:57:07 /root/scraper_nodejs/commands/completeScraps/tessabit.js";
        $array[] = "#####################   Server -   s05 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 3.50 G";
        $array[] = "Used Memory in Percentage = 90.00%";
        $array[] = "#####################   Server -   s06 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .84 G";
        $array[] = "Used Memory in Percentage = 21.00%";
        $array[] = "17238 21:29:17 /root/scraper_nodejs/commands/completeScraps/divo.js";
        $array[] = "28163 6-00:02:19 /root/scraper_nodejs/commands/completeScraps/insightConcept.js";
        $array[] = "#####################   Server -   s07 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .98 G";
        $array[] = "Used Memory in Percentage = 25.00%";
        $array[] = "2099 3-19:29:51 /root/scraper_nodejs/commands/completeScraps/tizianafausti.js";
        $array[] = "17525 5-02:02:45 /root/scraper_nodejs/commands/completeScraps/italiani.js";
        $array[] = "#####################   Server -   s08 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 1.93 G";
        $array[] = "Used Memory in Percentage = 50.00%";
        $array[] = "8835 5-01:47:08 /root/scraper_nodejs/commands/completeScraps/coltorti/coltorti8.js";
        $array[] = "#####################   Server -   s09 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 1.16 G";
        $array[] = "Used Memory in Percentage = 30.00%";
        $array[] = "13146 1-13:21:22 /root/scraper_nodejs/commands/completeScraps/sugar.js";
        $array[] = "#####################   Server -   s10 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = 1.18 G";
        $array[] = "Used Memory in Percentage = 30.00%";
        $array[] = "4225 5-01:40:42 /root/scraper_nodejs/commands/completeScraps/julian-reverse.js";
        $array[] = "#####################   Server -   s11 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .40 G";
        $array[] = "Used Memory in Percentage = 10.00%";
        $array[] = "#####################   Server -   s12 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .27 G";
        $array[] = "Used Memory in Percentage = 7.00%";
        $array[] = "#####################   Server -   s13 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .95 G";
        $array[] = "Used Memory in Percentage = 24.00%";
        $array[] = "963 3-03:59:16 /root/scraper_nodejs/commands/completeScraps/nugnes1920.js";
        $array[] = "20668 6-00:03:44 /root/scraper_nodejs/commands/completeScraps/lidia.js";
        $array[] = "#####################   Server -   s14 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .88 G";
        $array[] = "Used Memory in Percentage = 22.00%";
        $array[] = "21956 20:36:36 /root/scraper_nodejs/commands/completeScraps/tessabit.js";
        $array[] = "#####################   Server -   s15 #################################";
        $array[] = "Total Memory = 3.85 G";
        $array[] = "Used Memory = .68 G";
        $array[] = "Used Memory in Percentage = 17.00%";
        $array[] = "27942 3-21:43:21 /root/scraper_nodejs/commands/completeScraps/vitaleBoutique.js";



        $serverId       = null;
        $scraperNamestr = null;
        $totalMemory    = null;
        $usedMemory     = null;
        $inPercentage   = null;
        $allOutput      = $array;

        if (!empty($allOutput)) {
            foreach ($allOutput as $k => $allO) {
                if ($k == 0) {
                    continue;
                }
                $allO = preg_replace('/\s+/', ' ', $allO);
                if (strpos($allO, "##################### Server - ") !== false) {
                    $serverArray = explode("##################### Server - ", $allO);
                    if (!empty($serverArray[1])) {
                        $serverNameArr = explode("################### Server Load ", $serverArray[1]);
                        if (!empty(trim($serverNameArr[0]))) {
                            $serverId = trim(str_replace("#", "", $serverNameArr[0]));
                            continue;
                        }
                    }
                }

                if (strpos($allO, "Total Memory = ") !== false) {
                    $memoryArr = explode("Total Memory = ", $allO);
                    if (!empty($memoryArr[1])) {
                        $totalMemory = $memoryArr[1];
                    }
                }

                if (strpos($allO, "Used Memory = ") !== false) {
                    $memoryArr = explode("Used Memory = ", $allO);
                    if (!empty($memoryArr[1])) {
                        $usedMemory = $memoryArr[1];
                    }
                }

                if (strpos($allO, "Used Memory in Percentage = ") !== false) {
                    $memoryArr = explode("Used Memory in Percentage = ", $allO);
                    if (!empty($memoryArr[1])) {
                        $inPercentage = $memoryArr[1];
                    }
                }

                // start to store scarper name
                $scraperNamestr  = null;
                $scraperStarTime = null;
                $pid = null;
                if (strpos($allO, "/root/scraper_nodejs/commands/completeScraps") !== false) {
                    $scriptNames = explode("/root/scraper_nodejs/commands/completeScraps", $allO);
                    if (!empty($scriptNames[1])) {
                        $pidStringArr = explode(" ",$scriptNames[0]);
                        $pid = $pidStringArr[0];
                        $scraperStarTime = date("Y-m-d H:i:s", strtotime($pidStringArr[1]));
                        $scraperName     = explode("/", $scriptNames[1]);
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
                        "start_time"     => $scraperStarTime,
                        "total_memory"   => $totalMemory,
                        "used_memory"    => $usedMemory,
                        "in_percentage"  => $inPercentage,
                        "pid"            => $pid
                    ]);
                }
            }
        }

        $report->update(['end_time' => Carbon::now()]);

    }
}
