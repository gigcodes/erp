<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $orderItems;
    public $order;
    public $orderTotal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        if (!empty($params["order"])) {
            $this->order = $params["order"];
        }

        if (!empty($params["customer"])) {
            $this->customer = $params["customer"];
        }

        if (!empty($params["order"])) {
            $this->orderItems = $this->viewOrderProductBlock($this->order);
        }

    }

    public function preview()
    {
        return view('maileclipse::templates.orderInvoice', [
            'orderItems' => $this->orderItems,
            'customer'   => $this->customer,
            'order'      => $this->order,
            'orderTotal' => $this->orderTotal,
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('maileclipse::templates.orderInvoice');
    }

    public function viewOrderProductBlock($order)
    {
        $string = "";
        if (!empty($order)) {
            foreach ($order->order_product as $products) {
                $string .= '<tr class="item last" style="height: 25px;">
                              <td style="height: 25px; width: 628px;">' . $products->sku . '</td>
                              <td style="height: 25px; width: 58px;">&#8377;' . $products->product_price . '</td>
                           </tr>';

                $this->orderTotal += $products->product_price;
            }
        }

        return $string;
    }

}
