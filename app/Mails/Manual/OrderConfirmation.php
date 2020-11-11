<?php

namespace App\Mails\Manual;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject        = "New Order # " . $this->order->order_id;
        $order          = $this->order;
        $customer       = $order->customer;
        $order_products = $order->order_products;

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;
        if ($storeWebsiteOrder) {
            $template = $storeWebsiteOrder->getOrderConfirmationTemplate();
            if ($template) {
                if(!empty($template->mail_tpl)) {
                    // need to fix the all email address
                    $view = (string)view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products'
                    ));
                    return $this->from('customercare@sololuxury.co.in')->subject($template->subject)
                    ->view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products'
                    ));
                }else{
                    $content = $template->static_template;
                    return $this->from('customercare@sololuxury.co.in')->subject($template->subject)->view('emails.blank_content', compact(
                        'order', 'customer', 'order_products','content'
                    ));
                }
            }

        } else {
            return $this->from('customercare@sololuxury.co.in')->bcc('customercare@sololuxury.co.in')->subject($subject)->view('emails.orders.confirmed-solo', compact(
                'order', 'customer', 'order_products'
            ));
        }

    }
}
