<?php

namespace App\Observers;

use App\Product;
use App\Customer;
use App\OutOfStockSubscribe;

class ProductObserver
{
    /**
     * Handle the out of stock subscribe "created" event.
     *
     * @param  \App\OutOfStockSubscribe  $outOfStockSubscribe
     * @return void
     */
    public function created(Product $product)
    {
        //
    }

    /**
     * Handle the out of stock subscribe "updated" event.
     *
     * @param  \App\OutOfStockSubscribe  $outOfStockSubscribe
     * @return void
     */
    public function updated(Product $product)
    {
        if ($product->stock_status == 1 and $product->isDirty('stock_status')) {
            $customerIds = OutOfStockSubscribe::where('product_id', $product->id)->where('status', 0)
            ->pluck('customer_id');
            $data['productName'] = $product['name'];
            $customerEmails = Customer::whereIn('id', $customerIds)->pluck('email')->toArray();
            foreach ($customerEmails as $customerEmail) {
                $email_to = $customerEmail;
                \Mail::send('emails.product_in_stock', $data, function ($message) use ($email_to) {
                    $message->to($email_to, '')->subject('Product back to stock');
                });
            }
            OutOfStockSubscribe::where('product_id', $product->id)->where('status', 0)->update(['status' => 1]);
        }
    }

    /**
     * Handle the out of stock subscribe "deleted" event.
     *
     * @return void
     */
    public function deleted(OutOfStockSubscribe $outOfStockSubscribe)
    {
        //
    }

    /**
     * Handle the out of stock subscribe "restored" event.
     *
     * @return void
     */
    public function restored(OutOfStockSubscribe $outOfStockSubscribe)
    {
        //
    }

    /**
     * Handle the out of stock subscribe "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(OutOfStockSubscribe $outOfStockSubscribe)
    {
        //
    }
}
