<?php

namespace App\Listeners;

use App\Events\CashFlowCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCurrencyCashFlow
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CashFlowCreated $event)
    {
        $cashflow = $event->cashflow;
        if($cashflow->amount_eur <= 0) {
           $cashflow->amount_eur = \App\Currency::convert($cashflow->amount,'EUR',$cashflow->currency);
           $cashflow->save();
        }
        
    }
}
