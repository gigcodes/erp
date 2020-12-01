<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ViewInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $orderItems;
    public $orders;
    public $orderTotal;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        if (!empty($params["orders"])) {
            $this->orders = $params["orders"];
        }

        if (!empty($params["invoice"])) {
            $this->invoice = $params["invoice"];
        }

        if (!empty($params["orders"])) {
            $this->orderItems = $this->viewOrderProductBlock($this->orders);
        }

        $this->customer = $this->orders !== null ? $this->getCustomerDetails($this->orders[0]) : null;
    }

    public function preview()
    {
        return view('maileclipse::templates.viewInvoice', [
            'orderItems' => $this->orderItems,
            'customer'   => $this->customer,
            'orders'      => $this->orders,
            'orderTotal' => $this->orderTotal,
            'invoice' => $this->invoice,
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('maileclipse::templates.viewInvoice');
    }


    public function viewOrderProductBlock($orders)
    {
        $string = "";
        if (!empty($orders)) {
            foreach ($orders as $order) {
                foreach ($order->order_product as $products) {
                    if($products->product) {
                        $string .= '<tr class="item last" style="height: 25px;">
                                  <td class="bl br vm" style="height: 25px; width: 300px; text-align: left;">' . $products->product->name . ' '. $products->product->short_description .'</td>
                                  <td class="vm" style="height: 25px; width: 100px; text-align: left;"></td>
                                  <td class="bl vm" style="height: 25px; width: 100px; text-align: left;">' . $products->product->made_in . '</td>
                                  <td class="bl vm" style="height: 25px; width: 100px; text-align: left;">' . $products->qty . '</td>
                                  <td class="bl vm" style="height: 25px; width: 100px; text-align: left;">1</td>
                                  <td class="bl br vm" style="height: 25px; width: 100px; text-align: left;">&#8377;' . $products->product_price . '</td>
                               </tr>';
                    }
                    $this->orderTotal += $products->product_price;
                }
            }
        }

        return $string;
    }

    public function getCustomerDetails($order) {
        if(isset($order->customer)) {
            return $order->customer;
        }
        return false;
    }
}
