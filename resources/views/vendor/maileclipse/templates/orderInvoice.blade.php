<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <title>A simple, clean, and responsive HTML invoice template</title>
      <style>
         .invoice-box {
         max-width: 800px;
         margin: auto;
         padding: 30px;
         border: 1px solid #eee;
         box-shadow: 0 0 10px rgba(0, 0, 0, .15);
         font-size: 16px;
         line-height: 24px;
         font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
         color: #555;
         }
         .invoice-box table {
         width: 100%;
         line-height: inherit;
         text-align: left;
         }
         .invoice-box table td {
         padding: 5px;
         vertical-align: top;
         }
         .invoice-box table tr td:nth-child(2) {
         text-align: right;
         }
         .invoice-box table tr.top table td {
         padding-bottom: 20px;
         }
         .invoice-box table tr.top table td.title {
         font-size: 45px;
         line-height: 45px;
         color: #333;
         }
         .invoice-box table tr.information table td {
         padding-bottom: 40px;
         }
         .invoice-box table tr.heading td {
         background: #eee;
         border-bottom: 1px solid #ddd;
         font-weight: bold;
         }
         .invoice-box table tr.details td {
         padding-bottom: 20px;
         }
         .invoice-box table tr.item td{
         border-bottom: 1px solid #eee;
         }
         .invoice-box table tr.item.last td {
         border-bottom: none;
         }
         .invoice-box table tr.total td:nth-child(2) {
         border-top: 2px solid #eee;
         font-weight: bold;
         }
         @media  only screen and (max-width: 600px) {
         .invoice-box table tr.top table td {
         width: 100%;
         display: block;
         text-align: center;
         }
         .invoice-box table tr.information table td {
         width: 100%;
         display: block;
         text-align: center;
         }
         }
         /** RTL **/
         .rtl {
         direction: rtl;
         font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
         }
         .rtl table {
         text-align: right;
         }
         .rtl table tr td:nth-child(2) {
         text-align: left;
         }
         .div-table {
           display: table;         
           width: auto;         
           background-color: #eee;         
           border: 1px solid #666666;         
           border-spacing: 5px; /* cellspacing:poor IE support for  this */
         }
         .div-table-row {
           display: table-row;
           width: auto;
           clear: both;
         }
         .div-table-col {
           float: left; /* fix for  buggy browsers */
           display: table-column;         
           width: 200px;         
           background-color: #ccc;  
         }
      </style>
   </head>
   <body>
<div class="invoice-box">
<table style="height: 432px;" cellspacing="0" cellpadding="0">
<tbody>
<tr class="top" style="height: 97px;">
<td style="height: 97px; width: 729px;" colspan="2">
<table style="width: 696px;">
<tbody>
<tr>
<td class="title" style="width: 416px;"><img style="width: 100%; max-width: 300px;" src="https://erp.amourint.com/images/solo_logo.png" /></td>
<td style="width: 260px;">Invoice #:{{ $order->order_id }}<br />Created: {{ $order->created_at }}<br /><br /></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr class="information" style="height: 72px;">
<td style="height: 72px; width: 632px;">Dear Mr. Dimitri Rolfson, Thank you for your order. Your order will be assigned to a customer care executive who will be avaialable at all times to answer any queries. Our customer care executive will contact you shortly. Your order confirmation is below.</td>
</tr>
<tr class="information" style="height: 117px;">
<td style="height: 117px; width: 729px;" colspan="2">
<table>
<tbody>
<tr>
<td>{{ $order->customer->address }}</td>
<td>Solo luxury</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr class="heading" style="height: 24px;">
<td style="height: 24px; width: 632px;">Item</td>
<td style="height: 24px; width: 87px;">Price</td>
</tr>
<!-- loop -->
<tr class="item last" style="height: 25px;">
<td style="height: auto; width: 632px;">{!! $orderItems !!}</td>
</tr>
<tr class="total" style="height: 49px;">
<td style="height: 49px; width: 632px;">&nbsp;</td>
<td style="height: 49px; width: 87px;">Total: ₹ {{ $orderTotal }}</td>
</tr>
</tbody>
</table>
</div>
</body>
</html>