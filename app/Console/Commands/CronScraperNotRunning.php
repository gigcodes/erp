<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\CronJobReport;
use Carbon\Carbon;

class CronScraperNotRunning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:cron-scraper-not-running';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to admin for scraper is not running.';


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
        'signature' => $this->signature,
        'start_time'  => Carbon::now()
      ]);

         $suppliers_all = DB::select('SELECT suppliers.id, l.created_at, suppliers.supplier, suppliers.email, suppliers.whatsapp_number, suppliers.scraper_name, suppliers.inventory_lifetime from suppliers INNER JOIN log_scraper l on l.website = suppliers.scraper_name');            
        if(count($suppliers_all) > 0){       
       
          foreach ($suppliers_all as $supplier){

            $start_date = strtotime($supplier->created_at); 
            $end_date = time();
            $diff = ($end_date - $start_date)/60/60; 
            $inventory_lifetime = $supplier->inventory_lifetime * 24;
            // check date if different more than 48 hours then send notification
            if($diff >= $inventory_lifetime)
            {
              $message = 'Scraper not running '.$supplier->scraper_name;

              dump("Scraper not running $supplier->scraper_name");

              try {
                dump("Sending message");

                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi('00971545889192', $supplier->whatsapp_number, $message); 
              } catch (\Exception $e) {
                dump($e->getMessage());
              }
             
            }
               
          }
        }
    }
}
