<?php

namespace App\Console\Commands;

use App\CronJobReport;
use Carbon\Carbon;

use Illuminate\Console\Command;

class StoreLiveProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:live-project';

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
        //
        //Auth code
        $auth_key = $this->login_api();
        $host = $this->host_api($auth_key);
        // if host id present add and else update 
        //id  mmujhe item api pe bhejna hain
        //data fetch krke store krna 
        //host_id 
    }
    public function login_api(){
        //Get API ENDPOINT response 
        $curl = curl_init("https://monitor.theluxuryunlimited.com/api_jsonrpc.php");
        $data =array(
            "jsonrpc"=> "2.0",     
            "method"=> "user.login",     
            "params"=> array(
                'username'=> 'Admin',         
                'password'=> 'Sk3C@Mon1X*C&!ac'     
            ),         
            'id'=> 1 ,
        );
        $datas = json_encode(array($data));
       
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        $results =json_decode($result);
        
        return $results[0]->result;
       
    }
    public function host_api($auth_key){
        //Get API ENDPOINT response 
        $curl = curl_init("https://monitor.theluxuryunlimited.com/api_jsonrpc.php");
        $data =array(
            "jsonrpc"=> "2.0",     
            "method"=> "host.get",     
            "params"=> array(
                     
            ), 
            "auth"=> $auth_key,     
            "id"=> 1 
        );
        $datas = json_encode(array($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        $results =json_decode($result);
        
        return $results[0]->result;;
    }
}
