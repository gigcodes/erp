<?php

namespace App\Mail;

use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ViewInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $orderItems;
    public $orders;
    public $orderTotal;
    public $invoice;
    public $billing;
    public $shipping;
    public $duty_tax;

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

        if (!empty($params["orders"])) {

            // $this->duty_tax = $this->viewDutyTax($this->orders);
            // dd($this->duty_tax->website_code->code);
            $website_code_data = $this->viewDutyTax($this->orders);
            $code = $website_code_data->website_code->code;
           
            $duty_countries = $website_code_data->website_code->duty_of_country->default_duty;
            $shipping_countries = $website_code_data->website_code->shipping_of_country($code)->price;
            
            $order_pro =  $this->orders[0]->order_product;
            $product_price = $order_pro[0]->product_price;
            
        }

        $this->customer = $this->orders !== null ? $this->getCustomerDetails($this->orders[0]) : null;
        $this->billing  = $this->orders !== null ? $this->getBillingDetails($this->orders[0]) : null;
        $this->shipping = $this->orders !== null ? $this->getShippingDetails($this->orders[0]) : null;
    }

    public function preview()
    {
        //echo $this->orderItems;exit;
        return view('maileclipse::templates.viewInvoice', [
            'orderItems'   => $this->orderItems,
            'customer'     => $this->customer,
            'orders'       => $this->orders,
            'orderTotal'   => $this->orderTotal,
            'invoice'      => $this->invoice,
            'buyerDetails' => $this->customer,
            'order'        => $this->orders[0],
            'billing'      => $this->billing,
            'shipping'     => $this->shipping,
        ]);
    }
    //TODO download function - added by jammer
    public function download()
    {
        $html = view('maileclipse::templates.orderInvoice', [
            'orderItems'   => $this->orderItems,
            'customer'     => $this->customer,
            'orders'       => $this->orders,
            'order'        => $this->orders[0],
            'orderTotal'   => $this->orderTotal,
            'buyerDetails' => $this->customer,
            'billing'      => $this->billing,
            'shipping'     => $this->shipping,
            'invoice'      => $this->invoice,
            'duty_tax'     => $this->duty_tax,
        ]);
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->render();
        $pdf->stream(date('Y-m-d H:i:s') . 'invoice.pdf');
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

                    if ($products->product) {

                        $string .= '<tr>
                                    <td>' . $products->product->name . ' ' . $products->product->short_description . '</td>
                                    <td>' . $products->made_in . '</td>
                                    <td>' . $products->qty . '</td>
                                    <td>INR</td>
                                    <td>INR ' . $products->product_price . '</td>
                                    </tr>';
                    }
                    $this->orderTotal += $products->product_price;
                }
            }
        }

        return $string;
    }

    public function getCustomerDetails($order)
    {
        if (isset($order->customer)) {
            return $order->customer;
        }
        return false;
    }

    public function getBillingDetails($order)
    {
        return $order->billingAddress();
    }

    public function getShippingDetails($order)
    {
        return $order->shippingAddress();
    }

    public function viewDutyTax($order)
    {
        return $order[0]->duty_tax;
    }
}
