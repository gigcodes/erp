<?php

namespace App\Listeners;

use App\Events\PaymentCreated;

class CreatePaymentCashflow
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
     * @param  object  $event
     * @return void
     */
    public function handle(PaymentCreated $event)
    {
        $payment = $event->payment;
        $user_id = ! empty(auth()->id()) ? auth()->id() : 6;
        $receipt = \App\PaymentReceipt::find($payment->payment_receipt_id);
        if ($receipt) {
            $cashflow = $receipt->cashFlows()->where('cash_flow_able_id', $payment->payment_receipt_id)->where('cash_flow_able_type', \App\PaymentReceipt::class)->first();
            if ($cashflow) {
                $cashflow->update([
                    'date' => $payment->created_at,
                    'amount' => $payment->amount,
                    'erp_amount' => $payment->amount,
                    'erp_eur_amount' => \App\Currency::convert($payment->amount, 'EUR', $payment->currency),
                    'type' => 'paid',
                    'currency' => $payment->currency,
                    'status' => 1,
                    'order_status' => 'pending',
                    'user_id' => $user_id,
                    'updated_by' => $user_id,
                    'cash_flow_able_id' => $payment->payment_receipt_id,
                    'cash_flow_able_type' => \App\PaymentReceipt::class,
                    'description' => 'Vendor paid',
                ]);
            } else {
                $receipt->cashFlows()->create([
                    'date' => $payment->created_at,
                    'amount' => $payment->amount,
                    'erp_amount' => $payment->amount,
                    'erp_eur_amount' => \App\Currency::convert($payment->amount, 'EUR', $payment->currency),
                    'type' => 'paid',
                    'currency' => $payment->currency,
                    'status' => 1,
                    'order_status' => 'pending',
                    'user_id' => $user_id,
                    'updated_by' => $user_id,
                    'cash_flow_able_id' => $payment->payment_receipt_id,
                    'cash_flow_able_type' => \App\PaymentReceipt::class,
                    'description' => 'Vendor paid',
                ]);
            }
        }
    }
}
