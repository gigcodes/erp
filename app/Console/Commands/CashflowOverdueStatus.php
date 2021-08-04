<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CashflowOverdueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashflow:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cashflow over due';

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
        $cashflow = \App\CashFlow::where("type","pending")->whereDate("date",date("Y-m-d"))->get();
        if(!$cashflow->isEmpty()) {
            foreach($cashflow as $ca) {
                $received = \App\CashFlow::where("cash_flow_able_id",$ca->cash_flow_able_id)->where("cash_flow_able_type",$ca->cash_flow_able_type)->where(function($q) {
                    $q->orWhere("type","received")->orWhere("type","paid");
                })->get();

                $totalSum = 0;
                if(!$received->isEmpty()) {
                    foreach($received as $re) {
                        $totalSum += $re->amount_eur;
                    }
                }

                $amountPending = $ca->amount_eur  - $totalSum;
                if($amountPending <= 5) {
                    $ca->type = "settled";
                }else{
                    $ca->type = "overdue";
                }

                $ca->due_amount_eur = $amountPending;
                $ca->save();
            }
        }
    }
}
