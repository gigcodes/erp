<?php

namespace App\Mails\Manual;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDeliveryDateChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    const STORE_ERP_WEBSITE = 15;

    public $order;

    public $fromMailer;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->order      = $data;
        $this->fromMailer = \App\Helpers::getFromEmail($this->order->customer->id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Order # ' . $this->order->order_id . ' delivery date has been changed';
        $order   = $this->order;

        $customer       = $order->customer;
        $order_products = $order->order_products;

        $this->subject = $subject;

        // check this order is related to store website ?
        $storeWebsiteOrder = $order->storeWebsiteOrder;
        if ($storeWebsiteOrder) {
            $emailAddress = \App\EmailAddress::where('store_website_id', $storeWebsiteOrder->website_id)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = \App\MailinglistTemplate::getOrderDeliveryDateChangeTemplate($storeWebsiteOrder->website_id);
        } else {
            $emailAddress = \App\EmailAddress::where('store_website_id', self::STORE_ERP_WEBSITE)->first();
            if ($emailAddress) {
                $this->fromMailer = $emailAddress->from_address;
            }
            $template = \App\MailinglistTemplate::getOrderDeliveryDateChangeTemplate();
        }

        if ($template) {
            if ($template->from_email != '') {
                $this->fromMailer = $template->from_email;
            }

            $this->subject = $template->subject;
            if (! empty($template->mail_tpl)) {
                return $this->from($this->fromMailer)
                    ->subject($this->subject)
                    ->view($template->mail_tpl, compact(
                        'order', 'customer', 'order_products'
                    ));
            } else {
                $content      = $template->static_template;
                $arrToReplace = ['{FIRST_NAME}', '{ORDER_DELIVERY_DATE}', '{ORDER_ID}'];
                $valToReplace = [$order->customer->name, $order->order_status, $order->order_id];
                $content      = str_replace($arrToReplace, $valToReplace, $content);

                return $this->from($this->fromMailer)->subject($this->subject)
                    ->view('emails.blank_content', compact(
                        'order', 'customer', 'order_products', 'content'
                    ));
            }
        }
    }
}
