<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\StoreWebsite;
use App\MagentoCronData;
use Carbon\Carbon;

class FetchMagentoCronData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetchMagentoCronData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will fetch data from all magento websites';

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
        $website = StoreWebsite::whereNotNull('magento_url')->get()->pluck('magento_url','id')->toArray();
        //$date = '2021-7-14';
		$date = Carbon::yesterday()->format('Y-n-j');
		$cronstatus = $this->cronStatus();
        foreach($website as $storeId => $web){
            foreach($cronstatus as $status){
				$web = strtolower($web);
				$url = parse_url($web); 
				if(!isset($url['scheme'])) {	
					$web = 'https://'.$web;
				} 

                $api = "$web/default/rest/all/V1/cronbystatus/$status/createdat/$date";
                //$data = json_decode($this->getDataApi($api), true); 
                $this->info('Approved....' . $api);
                // if(!empty($data)){
                //     foreach($data as $da){
                //         //dd($da);
                //         if(isset($da['status']) && ($da['status']== true && $da['Message']=="Cron data returned successfully")){
                //             $insert =[
                //                 'store_website_id'=>$storeId,
                //                 'website'=>$web,
                //                 'cronstatus' => $da['cronstatus'],
                //                 'cron_id' =>$da['cron_id'],
                //                 'job_code' => $da['job_code'],
                //                 'cron_message' => $da['cron_message'],
                //                 'cron_created_at' =>$da['created_at'],
                //                 'cron_scheduled_at' => $da['scheduled_at'],
                //                 'cron_executed_at'=> $da['executed_at'],
                //                 'cron_finished_at' => $da['finished_at']
                //             ];
                //             MagentoCronData::create($insert);
                //         }
                //     }
                // }
            }
        }
        return 'successfully';
    }
	
	public function cronStatus(){
       return array('missed','error','success','pending','running');
    }

    public function getDataApi($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        return  $server_output;
    }
}
