<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScraperProcess;
use App\Scraper;
use Illuminate\Http\Request;
use App\ScrapLog;

class ScrapperNotRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scrapper_not_run';

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
        $scraper_process = ScraperProcess::where("scraper_name","!=","")->orderBy('started_at','DESC')->get()->unique('scraper_id');
        $scraper_proc = [];
        foreach ($scraper_process as $key => $sp) {
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $sp->started_at);
            $from = \Carbon\Carbon::now();
            $diff_in_hours = $to->diffInMinutes($from);
            if ($diff_in_hours > 1440) {
                array_push($scraper_proc,$sp->scraper_id);
            }
        }
        $scrapers = Scraper::where("scraper_name","!=","")->whereNotIn('id', $scraper_proc)->get();
		foreach($scrapers as $scrapperDetails) {
			$hasAssignedIssue = \App\DeveloperTask::where("scraper_id", $scrapperDetails->id)
			->whereNotNull("assigned_to")->where("is_resolved", 0)->first();
			if($hasAssignedIssue != null) {
				$userName = \App\User::where('id', $hasAssignedIssue->assigned_to)->pluck('name')->first();
				$requestData = new Request();
				$requestData->setMethod('POST');
				$requestData->request->add(['issue_id' => $hasAssignedIssue->id, 'message' => "Scraper didn't Run In Last 24 Hr", 'status' => 1]); 
				try{
					app('\App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
					ScrapLog::create(['scraper_id'=>$scrapperDetails->id, 'log_messages'=>"Scraper didn't Run In Last 24 Hr message sent to ".$userName]);
				} catch(\Exception $e){  
					ScrapLog::create(['scraper_id'=>$scrapperDetails->id, 'log_messages'=>"Coundn't send message to ".$userName]);
				}
			}
			
				
		}
    }
}
