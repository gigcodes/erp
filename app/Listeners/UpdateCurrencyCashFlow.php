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
        \Log::info("this event has been called");
        $cashflow = $event->cashflow;
        if($cashflow->amount_eur <= 0) {
           $cashflow->amount_eur = \App\Currency::convert($cashflow->amount,'EUR',$cashflow->currency);
           \Log::info("amount is not euro so called coveter with amount #".$cashflow->amount_eur."#".$cashflow->currency);
           $cashflow->save();
        }
        
    }
}
