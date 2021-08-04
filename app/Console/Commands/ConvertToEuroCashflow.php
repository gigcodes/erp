<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertToEuroCashflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert-to-eur:cashflow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conver to cashflow';

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
        $cashflow = \App\CashFlow::where("amount_eur","<=",0)->where("currency","!=", "")->get();
        if(!$cashflow->isEmpty()) {
            foreach($cashflow as $cas) {
                $cas->amount_eur = \App\Currency::convert($cas->amount,'EUR',$cas->currency);
                $cas->save();
            }
        }

    }
}
