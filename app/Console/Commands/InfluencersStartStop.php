<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\InfluencerKeyword;
use Illuminate\Console\Command;

class InfluencersStartStop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'influencers:startstop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command checks the status of influencer script and start/stop';

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
        //get all keywords
        $keywords = InfluencerKeyword::all();
        //check scrapper
        $runningCount     = 0;
        $runningKeywordId = 0;
        foreach ($keywords as $keyword) {
            $status = $this->get_status($keyword->name);
            if ($status == 'Script Already Running ' . $keyword->name) {
                $runningKeywordId = $keyword->id;
                $runningCount++;
            }
        }

        if ($runningCount == 0) {
            //scrapper is not running, run it for first keyword
            $firstkeyword = InfluencerKeyword::first();
            $success      = $this->start_script($firstkeyword->name);
            $this->info($success);
        } else {
            //stop running script
            $currentkeyword = InfluencerKeyword::find($runningKeywordId);
            if ($this->stop_script($currentkeyword->name) == 'Script Killed') {
                $nextkeyword = $currentkeyword->next();
                if (isset($nextkeyword)) {
                    //run next script
                    $status = $this->start_script($nextkeyword->name);
                    $this->info($status);
                }
            }
        }
    }

    public function stop_script($name)
    {
        //stop current script
        $startTime      = date('Y-m-d H:i:s', LARAVEL_START);
        $name           = str_replace(' ', '', $name);
        $cURLConnection = curl_init();
        $url            = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/stop-script?' . $name;
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $phoneList = curl_exec($cURLConnection);
        $httpcode  = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($phoneList), $httpcode, \App\Console\Commands\InfluencersStartStop::class, 'stop_script');
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($phoneList);
        $b64               = $jsonArrayResponse->status;

        return $b64;
    }

    public function start_script($name)
    {
        //start script
        $startTime      = date('Y-m-d H:i:s', LARAVEL_START);
        $name           = str_replace(' ', '', $name);
        $cURLConnection = curl_init();
        $url            = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/start-script?' . $name;
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $phoneList = curl_exec($cURLConnection);
        $httpcode  = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($phoneList), $httpcode, \App\Console\Commands\InfluencersStartStop::class, 'start_script');
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($phoneList);
        $b64               = $jsonArrayResponse->status;

        return $b64;
    }

    public function get_status($name)
    {
        $startTime      = date('Y-m-d H:i:s', LARAVEL_START);
        $name           = str_replace(' ', '', $name);
        $cURLConnection = curl_init();
        $url            = env('INFLUENCER_SCRIPT_URL') . ':' . env('INFLUENCER_SCRIPT_PORT') . '/get-status?' . $name;
        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $phoneList = curl_exec($cURLConnection);
        $httpcode  = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
        LogRequest::log($startTime, $url, 'GET', json_encode([]), json_decode($phoneList), $httpcode, \App\Console\Commands\InfluencersStartStop::class, 'get_status');
        curl_close($cURLConnection);
        $jsonArrayResponse = json_decode($phoneList);
        $b64               = isset($jsonArrayResponse->status) ? $jsonArrayResponse->status : '';

        return $b64;
    }
}
