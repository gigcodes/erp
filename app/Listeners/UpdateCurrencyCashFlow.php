<?php

namespace App\Listeners;

use App\Events\CashFlowUpdated;

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
     * @param object $event
     *
     * @return void
     */
    public function handle(CashFlowUpdated $event)
    {
        \Log::info('this action has been called');

        $cashflow = $event->cashflow;
        if ($cashflow->amount_eur <= 0) {
            \DB::table('cash_flows')->where('id', $cashflow->id)->update(['amount_eur' => \App\Currency::convert($cashflow->amount, 'EUR', $cashflow->currency)]);
        }

        if ($cashflow->monetary_account_id > 0 && ($cashflow->type == 'received' || $cashflow->type == 'paid')) {
            $user_id = ! empty(auth()->id) ? auth()->id : 6;
            $amount  = $cashflow->erp_amount;
            if ($cashflow->type == 'paid') {
                $amount = 0 - $cashflow->erp_amount;
            }

            $monetaryHistory = \App\MonetaryAccountHistory::where('model_id', $cashflow->id)->where('model_type', \App\CashFlow::class)->first();
            if ($monetaryHistory) {
                $monetaryHistory->update([
                    'note'                => $cashflow->description,
                    'model_id'            => $cashflow->id,
                    'model_type'          => \App\CashFlow::class,
                    'amount'              => $amount,
                    'monetary_account_id' => $cashflow->monetary_account_id,
                    'user_id'             => $user_id,
                ]);
            } else {
                \App\MonetaryAccountHistory::create([
                    'note'                => $cashflow->description,
                    'model_id'            => $cashflow->id,
                    'model_type'          => \App\CashFlow::class,
                    'amount'              => $amount,
                    'monetary_account_id' => $cashflow->monetary_account_id,
                    'user_id'             => $user_id,
                ]);
            }
        }
    }
}
