<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\StoreWebsite;
use Carbon\Carbon;
use App\Loggers\ScrapPythonLog;
use Illuminate\Support\Facades\Storage;


class GetPytonLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:pythonLogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for get logs from python ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $mailList = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		//dd("test");
		$storeWebsites = ["sololuxury", "avoir-chic", "brands-labels", "o-labels", "suvandnat", "veralusso"];
		$devices=["mobile", "desktop", "tablet"];
	//	$devices=[ "desktop"];
        foreach($storeWebsites as $website){
			$url="http://167.86.88.58:5000/get-logs";
			foreach($devices as $device){
                $date=Carbon::yesterday()->format("m-d-Y");
              //  $date = "12-10-2021";
				$data=["website"=>$website, "date" =>$date,"device" => "desktop"];
				\Log::info($data);
				\Log::info($url);
				$response = $this->callApi($url, "POST", $data );
                if($response == "Log File Not Found"){
                    $data["log_text"]=$response;
                }else{
                    $file_name="python_logs/python_site_log_".$website."_".$data["device"].".log";
                    Storage::put($file_name, $response);
                    $data["log_text"]=url('/storage/app/'.$file_name);
                }
				
				ScrapPythonLog::create($data);
			}

        }
       
    }

    public function callApi($url, $method, $data = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => json_encode($data),
			CURLOPT_HTTPHEADER     => array(
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        \Log::info($response);
        return $response;
    }
	
	
}