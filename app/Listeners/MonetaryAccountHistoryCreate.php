<?php

namespace App\Listeners;

use App\Events\MonetaryAccountCreated;

class MonetaryAccountHistoryCreate
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
    public function handle(MonetaryAccountCreated $event)
    {
        $account = $event->account;
        $user_id = auth()->id();
        if (! ($user_id > 0)) {
            $user_id = 6;
        }
        \App\MonetaryAccountHistory::create([
            'note' => 'Account has been created',
            'model_id' => $account->id,
            'model_type' => \App\MonetaryAccount::class,
            'amount' => $account->amount,
            'monetary_account_id' => $account->id,
            'user_id' => $user_id,
        ]);
    }
}
