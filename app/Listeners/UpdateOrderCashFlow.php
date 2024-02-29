<?php

namespace App\Listeners;

use App\Events\OrderUpdated;

class UpdateOrderCashFlow
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
    public function handle(OrderUpdated $event)
    {
        $order   = $event->order;
        $user_id = auth()->id();
        if ($order->order_status_id == \App\Helpers\OrderHelper::$prepaid) {
            $cash_flow = $order->cashFlows()->whereIn('order_status', ['pending', 'prepaid'])->first();
            if ($cash_flow) {
                $cash_flow->fill([
                    'amount'              => $order->balance_amount,
                    'status'              => 1,
                    'order_status'        => 'prepaid',
                    'updated_by'          => $user_id,
                    'monetary_account_id' => $order->monetary_account_id,
                    'description'         => 'Order Received with full pre payment',
                ])->save();
            } else {
                $order->cashFlows()->create([
                    'date'                => date('Y-m-d H:i:s'),
                    'amount'              => $order->balance_amount, //amount may be entry in any columns by the operator
                    'type'                => 'received',
                    'currency'            => $order->store_currency_code,
                    'status'              => 1,
                    'order_status'        => 'prepaid',
                    'updated_by'          => $user_id,
                    'user_id'             => $user_id,
                    'monetary_account_id' => $order->monetary_account_id,
                    'description'         => 'Order Received with full pre payment',
                ]);
            }
        } elseif ($order->order_status_id == \App\Helpers\OrderHelper::$advanceRecieved) {
            $cash_flow = $order->cashFlows()->where('order_status', 'advance received')->first();
            if ($cash_flow) {
                $cash_flow->fill([
                    'date'   => $order->advance_date ?: $cash_flow->date,
                    'amount' => $order->advance_detail,
                ])->save();
            } else {
                $order->cashFlows()->create([
                    'date'                => $order->advance_date ?: date('Y-m-d H:i:s'),
                    'amount'              => $order->advance_detail,
                    'type'                => 'received',
                    'currency'            => $order->store_currency_code,
                    'status'              => 1,
                    'order_status'        => 'advance received',
                    'updated_by'          => $user_id,
                    'user_id'             => $user_id,
                    'monetary_account_id' => $order->monetary_account_id,
                    'description'         => 'Advance Received',
                ])->save();
            }
            $pending_cash_flow = $order->cashFlows()->firstOrCreate([
                'order_status' => 'pending',
            ]);
            $pending_cash_flow->fill([
                'date'                => $order->date_of_delivery ?: ($order->estimated_delivery_date ?: $order->order_date),
                'amount'              => $order->balance_amount,
                'actual'              => 0,
                'type'                => 'received',
                'currency'            => $order->store_currency_code,
                'status'              => 0,
                'user_id'             => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
                'updated_by'          => $user_id,
            ])->save();
        } elseif ($order->order_status_id == \App\Helpers\OrderHelper::$delivered) {
            $pending_cash_flow = $order->cashFlows()->firstOrCreate([
                'order_status' => 'pending',
            ]);
            $pending_cash_flow->fill([
                'date'                => $order->date_of_delivery ?: ($order->estimated_delivery_date ?: $order->order_date),
                'amount'              => $order->balance_amount,
                'type'                => 'received',
                'currency'            => $order->store_currency_code,
                'status'              => 1,
                'updated_by'          => $user_id,
                'user_id'             => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
                'order_status'        => 'delivered',
            ])->save();
        } elseif ($order->order_status_id == \App\Helpers\OrderHelper::$refundToBeProcessed
            || $order->order_status_id == \App\Helpers\OrderHelper::$refundDispatched
            || $order->order_status_id == \App\Helpers\OrderHelper::$refundCredited) {
        } else {
            $pending_cash_flow = $order->cashFlows()->firstOrCreate([
                'order_status' => 'pending',
            ]);
            $pending_cash_flow->fill([
                'date'                => $order->date_of_delivery ?: ($order->estimated_delivery_date ?: $order->order_date),
                'amount'              => $order->balance_amount,
                'type'                => 'received',
                'currency'            => $order->store_currency_code,
                'status'              => 0,
                'user_id'             => $user_id,
                'updated_by'          => $user_id,
                'monetary_account_id' => $order->monetary_account_id,
            ])->save();
        }
    }
}
