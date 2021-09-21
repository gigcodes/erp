<?php

namespace App\Console\Commands;

use App\ChatMessage;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Scrapermissingdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Scraper';    

    /**
     * The console command description.       
     *
     * @var string
     */
    protected $description = 'Scraper';

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
        $date=date("Y-m-d");
        $ss=\App\ScrapedProducts::join('scrapers','scraped_products.website','scrapers.scraper_name') 
        ->select('scraped_products.*','scrapers.id as scraper_id')
        ->orderBy('scraped_products','desc')
        ->whereRaw("date(scraped_products.created_at)=date('$date')")->get();
        $website='';
        $t=0;
        $c=0;
        $d=0;
        $p=0;
        $msg='';
        foreach($ss as $s)
        {
            if ($website!=$s->website)
              {
                 if ($website!='')
                 {
                    $msg='';
                    if($t>0)
                    $msg=" $t Product Title Missing "; 
                    if($c>0)
                    $msg=" $c Product Composition Missing "; 
                    if($d>0)
                    $msg=" $d Product  Description Missing "; 
                    if($p=='')
                    $msg=" $p Product  Price Missing "; 
                    if ($msg!='')
                    {
                        $u=\App\DeveloperTask::where('scraper_id',$s->scraper_id)->orderBy('created_at','desc')->first();
                                   if ($u)    
                                   {
                                       
                                       if ($u->user_id>0)
                                          $user_id = $u->user_id;
                                       else
                                       $user_id=6;
                                              
                                       $params = [];
                                       $params['message'] = $msg;
                                       $params['erp_user'] = $user_id;
                                       $params['user_id'] = $user_id;
                                       $params['approved'] = 1;
                                       $params['status'] = 2;
                                       $params['developer_task_id'] = $u->id;
                                       $chat_message = \App\ChatMessage::create($params);
       
                                       $requestData = new Request();
                                       $requestData->setMethod('POST');
                                       $requestData->request->add(['user_id' => $user_id,'developer_task_id'=>$u->id, 'message' => $msg, 'status' => 1]);
                                       app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'developer_task');
                             
                                   }
                                       
                    }   

      
                 }

                 $website=$s->website;
                 $t=0;
                 $c=0;
                 $d=0;
                 $p=0;
                if($s->title=='')
                   $t++; 
                if($s->composition=='')
                   $c++;
                if($s->description=='')
                  $d++; 
                if($s->price=='')
                  $p++;
  
              }
            else
            {
                if($s->title=='')
                   $t++; 
                if($s->composition=='')
                   $c++;
                if($s->description=='')
                  $d++; 
                if($s->price=='')
                  $p++;
            }  
                       
        }


        if ($website!='')
                 {
                    $msg='';
                    if($t>0)
                    $msg=" $t Product Title Missing "; 
                    if($c>0)
                    $msg=" $c Product Composition Missing "; 
                    if($d>0)
                    $msg=" $d Product  Description Missing "; 
                    if($p=='')
                    $msg=" $p Product  Price Missing "; 
                    if ($msg!='')
                    {
                        $u=\App\DeveloperTask::where('scraper_id',$s->scraper_id)->orderBy('created_at','desc')->first();
                                   if ($u)    
                                   {
                                       
                                       if ($u->user_id>0)
                                          $user_id = $u->user_id;
                                       else
                                       $user_id=6;
                                              
                                       $params = [];
                                       $params['message'] = $msg;
                                       $params['erp_user'] = $user_id;
                                       $params['user_id'] = $user_id;
                                       $params['approved'] = 1;
                                       $params['status'] = 2;
                                       $params['developer_task_id'] = $u->id;
                                       $chat_message = \App\ChatMessage::create($params);
       
                                       $requestData = new Request();
                                       $requestData->setMethod('POST');
                                       $requestData->request->add(['user_id' => $user_id,'developer_task_id'=>$u->id, 'message' => $msg, 'status' => 1]);
                                       app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'developer_task');
                             
                                   }
                                       
                    }   

      
                 } 
        
        
    }    
}
