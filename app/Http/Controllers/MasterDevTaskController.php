<?php

namespace App\Http\Controllers;

use App\Library\Github\GithubClient;
use Illuminate\Http\Request;

class MasterDevTaskController extends Controller
{
    /*
    Database
    - Average load time
    - Database size now vs Database size 24 hours ago

    Development
    - Open branches per repository
    - Number of errors in the current log file

    Cron jobs
    - Number of failed crons last 24 hours

    Whatsapp
    - Number of messages last 3 hours
    - Number of messages last 24 hours

    Scraping
    - Number of errors per scraper last 24 hours
    - Number of products per scraper last 24 hours

    Cropping
    - Number of crops last 3 hours
    - Number of crops last 24 hours
     */
    public function index(Request $request)
    {
        $currentSize = \DB::table("database_historical_records")->orderBy("created_at", "desc")->first();
        //echo '<pre>'; print_r($currentSize); echo '</pre>';exit;
        $sizeBefore  = null;
        if (!empty($currentSize)) {
            $sizeBefore = \DB::table("database_historical_records")
                ->whereRaw(\DB::raw("DATE(created_at) = DATE('" . $currentSize->created_at . "' - INTERVAL 1 DAY)"))
                ->first();
        }

        // find the open branches
        $github     = new GithubClient;
        $repository = $github->getRepository();
        $repoArr    = [];
        if (!empty($repository)) {
            foreach ($repository as $i => $repo) {
                $repoId = $repo->full_name;
                $pulls  = $github->getPulls($repoId, "q=is%3Aopen+is%3Apr");
                 $repoArr[$i]["name"] =  $repoId;
                if (!empty($pulls)) {
                    foreach ($pulls as $pull) {
                        $repoArr[$i]["pulls"][] = [
                            "title" => $pull->title,
                            "no"    => $pull->number,
                            "url"   => $pull->html_url,
                            "user"   => $pull->user->login,
                        ];
                    }
                }
            }
        }
        $cronjobReports = null;
        
        /*$cronjobReports = \App\CronJob::join("cron_job_reports as cjr", "cron_jobs.signature", "cjr.signature")
        ->where("cjr.start_time", '>', \DB::raw('NOW() - INTERVAL 24 HOUR'))
        ->where("cron_jobs.last_status", "error")
        ->groupBy("cron_jobs.signature")
        ->get();*/

        $last3HrsMsg = null;
        $last24HrsMsg = null;

        /*$last3HrsMsg = \DB::table("chat_messages")->where("created_at",">=",\DB::raw("DATE_SUB(NOW(),INTERVAL 3 HOUR)"))->select(
            [\DB::raw("count(*) as cnt")]
        )->first();

        $last24HrsMsg = \DB::table("chat_messages")->where("created_at",">=",\DB::raw("DATE_SUB(NOW(),INTERVAL 24 HOUR)"))->select(
            [\DB::raw("count(*) as cnt")]
        )->first();*/


        return view("master-dev-task.index",compact('currentSize','sizeBefore','repoArr','cronjobReports','last3HrsMsg','last24HrsMsg'));

        echo '<pre>';
        print_r($last3HrsMsg);
        echo '</pre>';exit;

        //echo '<pre>'; print_r([$currentSize,$sizeBefore]); echo '</pre>';exit;
    }
}
