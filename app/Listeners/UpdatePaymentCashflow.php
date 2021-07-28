<?php

namespace App\Listeners;

use App\Events\PaymentCreated;

class UpdatePaymentCashflow
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(PaymentCreated $event)
    {
        /*$payment = $event->payment;
        $user_id = !empty(auth()->id()) ? auth()->id() : 6;
        $receipt->cashFlows()->create([
            'date'                => $payment->created_at,
            'amount'              => $payment->amount,
            'type'                => 'paid',
            'currency'            => $payment->currency,
            'status'              => 1,
            'order_status'        => 'pending',
            'user_id'             => $user_id,
            'updated_by'          => $user_id,
            'cash_flow_able_id'   => $payment->payment_receipt_id,
            'cash_flow_able_type' => \App\PaymentReceipt::class,
            'description'         => 'Vendor paid',
        ]);*/
    }
}
