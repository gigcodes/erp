<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AssetsManager;
use App\CashFlow;
class AssetsManagerPaymentCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assetsmanagerpayment:cron {payment_cycle}';

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
        $payment_cycle = $this->argument('payment_cycle');
        if(!$payment_cycle){
            $this->info(" please input payment_cycle 'Weekly' or 'Daily' or 'Monthly' or 'Bi-Weekly' or 'Yearly' ");
            return false;
        }
        switch ($payment_cycle) {
            case 'Weekly':
                $this->addPaymentCycleToCashflow('Weekly');
              break;
            case 'Daily':
                $this->addPaymentCycleToCashflow('Daily');
              break;
            case 'Monthly':
                $this->addPaymentCycleToCashflow('Monthly');
              break;
            case 'Bi-Weekly':
                $this->addPaymentCycleToCashflow('Bi-Weekly');
                break;
            case 'Yearly':
                $this->addPaymentCycleToCashflow('Yearly');
                break;
            default:
            $this->info(" please input payment_cycle 'Weekly' or 'Daily' or 'Monthly' or 'Bi-Weekly' or 'Yearly' ");
          }         
    }
    public function addPaymentCycleToCashflow($payment_cycle){
        $results = AssetsManager::where('payment_cycle',$payment_cycle)->get();
        if(count($results)==0){
           return $this->info(" no record exist for ".$payment_cycle." payments ");
           
        }
        $count = count($results);
        $i=0;
        $success =false;
        foreach($results as $result){
            
            //create entry in table cash_flows
            CashFlow::create(
                [
                    'description'=>'Asset Manager Payment for id '.$result->id,
                    'date'=>date('Y-m-d'),
                    'amount'=>$result->amount,
                    'type'=>'paid',
                    'cash_flow_able_type'=>'App\AssetsManager',
                ]
            );
            $i++;
            if($i==$count){
                $success=true;
            }
        }
        if($success==true){
        return $this->info($payment_cycle." payment added to cashflow successfully ");
        }
    }
}
