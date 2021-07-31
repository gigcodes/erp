<?php

namespace App\Listeners;

use App\Events\CashFlowCreated;

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
        if ($cashflow->amount_eur <= 0) {
            $cashflow->amount_eur = \App\Currency::convert($cashflow->amount, 'EUR', $cashflow->currency);
            $cashflow->save();
        }

        if ($cashflow->monetary_account_id > 0 && ($cashflow->type == "received" || $cashflow->type == "paid")) {
            $account = \App\MonetaryAccount::find($cashflow->monetary_account_id);
            $user_id = !empty(auth()->id)  ? auth()->id : 6;
            if ($account) {
                $amount = $account->amount;
                if ($cashflow->type == "received") {
                    $amount += $cashflow->amount;
                } else if ($cashflow->type == "paid") {
                    $amount -= $cashflow->amount;
                }

                $account->amount = $amount;
                $account->save();

                \App\MonetaryAccountHistory::create([
                    "note"                => $cashflow->description,
                    "model_id"            => $cashflow->id,
                    "model_type"          => \App\CashFlow::class,
                    "amount"              => $amount,
                    "monetary_account_id" => $account->id,
                    "user_id"             => $user_id,
                ]);
            }
        }

    }
}
