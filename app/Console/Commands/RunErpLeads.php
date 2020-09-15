<?php

namespace App\Console\Commands;
use App\Customer;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class RunErpLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erpleads:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Erp Leads';

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
        try {
   
            $Customers = Customer::all();
            if (!$Customers->isEmpty()) {
                foreach ($Customers as $Customer) {
                 
                
                    $is_blocked_lead    = $Customer->is_blocked_lead;
                    $freq = $Customer->lead_product_freq;
                 
                    if (isset($is_blocked_lead) && $is_blocked_lead != 1){
                        if(isset($freq) && $freq != 0){
                            $lead_product_limit = $freq;
                        }else{
                            $lead_product_limit  = \App\Setting::where("name","send_leads_product")->value('val');
                        }


                        $products = \App\Product::where(function ($q) {
                            $q->where("stock", ">", 0)->orWhere("supplier", "in-stock");
                        });


                        $allProduts = $products->limit($lead_product_limit)->get();
                      

                        if (!empty($allProduts)) {                        
                           
                            $requestData = new Request();
                            $requestData->setMethod('POST');
                            $requestData->request->add(['customer_id' => $Customer->id,'selected_product' => $allProduts]);

                            $res = app('App\Http\Controllers\LeadsController')->sendPrices($requestData, new GuzzleClient);
                                
                          
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
