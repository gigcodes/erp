<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->order = $data;
        $this->fromMailer = 'customercare@sololuxury.co.in';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Order # " . $this->order->order_id . " Status has been changed";
        $order = $this->order;

        $customer = $order->customer;
        $order_products = $order->order_products;

        $this->subject = $subject;
        $this->fromMailer = "customercare@sololuxury.co.in";

        //$email          = "customercare@sololuxury.co.in";

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;
        $order_status = $order->order_status;
        if ($storeWebsiteOrder) {
            $emailAddress = \App\EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = \App\MailinglistTemplate::getOrderStatusChangeTemplate($order_status, $storeWebsiteOrder->website_id);
        } else {
            $emailAddress = \App\EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = \App\MailinglistTemplate::getOrderStatusChangeTemplate($order_status);
        }

        if ($template) {
            
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            $this->subject = $template->subject;
            if (!empty($template->mail_tpl)) {
                //dd($template->mail_tpl);
                $content = $template->html_text;
                $arrToReplace = ['{FIRST_NAME}', '{ORDER_STATUS}', '{ORDER_ID}'];
                $valToReplace = [$order->customer->name, $order->order_status, $order->order_id];
                $content = str_replace($arrToReplace, $valToReplace, $content);
                
                // need to fix the all email address
                return $this->from($this->fromMailer)
                    ->subject($this->subject)
                    ->view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products', 'content'
                    ));
            } else {

                //$content = $template->static_template;
                $content = $template->html_text;
                
                $arrToReplace = ['{FIRST_NAME}', '{ORDER_STATUS}', '{ORDER_ID}'];
                $valToReplace = [$order->customer->name, $order->order_status, $order->order_id];
                $content = str_replace($arrToReplace, $valToReplace, $content);

                return $this->from($this->fromMailer)->subject($this->subject)
                    ->view('emails.blank_content', compact(
                        'order', 'customer', 'order_products', 'content'
                    ));
            }
        }

        if (!$storeWebsiteOrder) {
            return $this->view('emails.orders.update-status', compact(
                'order', 'customer', 'order_products'
            ));
        }

    }
}
