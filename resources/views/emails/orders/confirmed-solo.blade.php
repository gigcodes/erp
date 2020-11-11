
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <!-- <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0" /> -->
  <title>Your order has been received</title>
  <style type="text/css">
    * {box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
    body {font-family: arial; font-size: 14px; color: #000000; margin: 0; padding: 0;}
    table {border-collapse: collapse;width: 100%;}
  </style>
</head>
<body>
   <div style="width: 800px; margin: 30px auto; border:2px solid #f4e7e1;">
      <div style="width: 100%;text-align: center; padding-top: 30px;background-color: #f4e7e1;">
        <img src="{{ asset('images/emails/logo.png') }}" alt="" />
      </div>
      <div style="width: 100%;background-color: #f4e7e1;padding: 0 30px;">
        <table>
          <tbody>
            <tr>
              <td>
                <h1>You're all sorted.</h1>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%; padding: 30px;">
        <table border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td>
                <h3 style="line-height: 1.24;font-size: 17px;font-weight: bold;letter-spacing: -0.1px;color:#898989;margin: 0;padding: 0;">Hello James</h3>
              </td>
            </tr>
            <tr><td><div style="font-size: 13px;line-height: 1.62;color:#898989;margin: 5px 0;">You've got great taste! We're so glad you chose noon.</div></td></tr>
            <tr><td><div style="font-size: 13px;line-height: 1.62;color:#898989;">Your order NAECB0042412822 has been received and is currently being processed by our crew.</div></td></tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%; padding: 0px 30px;">
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #898989;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                  <td style="width: 100%;"><div style="font-weight: bold;font-size: 20px;color: #898989;padding-top: 10px;"><b style="color: #000000;">Ordered:</b> Nov 07, 2020</div></td>
                </tr>
            </tbody>
          </table>
      </div>
      <div style="width: 100%;padding: 30px 0px 30px;">
        <table border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td width="50%" valign="top" align="left" style="background-color: #f9f2ef;padding: 20px 30px;">
                <table align="left" valign="top">
                  <tbody>
                    <tr>
                      <td><div style="font-size: 14px;font-weight: bold;color: #000000;padding-bottom: 5px;">ORDER SUMMARY</div></td>
                    </tr>
                    <tr>
                      <td><div style="color: #898989;font-size: 12px;padding-top: 5px;">Order No:</div></td>
                      <td><div style="color: #898989;font-size: 12px;font-weight: bold;padding-top: 5px;">{{ $order->order_id }}</div></td>
                    </tr>
                    <tr>
                      <td><div style="color: #898989;font-size: 12px;padding-top: 5px;">Order Total:</div></td>
                      <td><div style="color: #898989;font-size: 12px;font-weight: bold;padding-top: 5px;">{{ $order->currency }} {{ $order->balance_amount }}</div></td>
                    </tr>
                    <tr>
                      <td><div style="color: #898989;font-size: 12px;padding-top: 5px;">Payment :</div></td>
                      <td><div style="color: #898989;font-size: 12px;font-weight: bold;padding-top: 5px;">{{ ucwords($order->payment_mode) }}</div></td>
                    </tr>
                  </tbody>
                </table>
              </td>
               <td width="50%" valign="top" align="right" style="background-color: #f9f2ef;padding: 20px 30px;">
                <table align="left" valign="top">
                  <tbody>
                    <tr>
                      <td><div style="font-size: 14px;font-weight: bold;color: #000000;padding-bottom: 5px;">SHIPPING ADDRESS</div></td>
                    </tr>
                    <tr>
                      <td><div style="color: #898989;font-size: 12px;padding-top: 5px;font-weight: bold;">{{ $order->customer->name }}</div></td>
                    </tr>
                    <tr>
                      <td><div style="color: #898989;font-size: 12px;padding-top: 5px;">{{ $order->customer->address }} </br>  {{ $order->customer->city }} </br>   {{ $order->customer->pincode }} </br>    {{ $order->customer->country }} </br>    T: {{ $order->customer->phone }}</div></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%;padding: 0px 30px;">
        <table cellpadding="0" cellspacing="0" style="border: 1px solid #f4e7e1;">
          <tbody>
            <tr><td style="border-bottom:1px solid #f4e7e1;text-align: center;font-size: 16px;font-weight: bold;padding: 10px;color: #898989;">Confirmed Items</td></tr>
            <tr>
              <td>
                <table border="0" cellpadding="0" cellspacing="0">
                  <tbody>
                    @php $total = $product_total = 0; @endphp
                    @foreach ($order->order_product as $order_product)
                      @php $product = $order_product->product @endphp
                      <tr>
                        <td style="padding:5px 10px;"><div><img src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl() }}"></div></td>
                        <td style="padding: 5px 10px;">
                          <h4 style="margin: 0;padding:0;font-weight: bold;font-size: 14px;color: #898989;">Brand name</h4>
                          <p style="margin: 0;padding: 0;width: 70%;margin: 5px 0;">{{ ($product->brand) ? $product->brand->name : "" }}</p>
                          <div style="font-size: 12px;color: #898989;">Quantity : {{$product->qty}}</div>
                          <div style="font-size: 12px;font-weight: 700;color: #000000;margin-top: 5px;margin-bottom: 10px;">Receive it by {{$order_product->shipment_date}}</div>
                        </td>
                        <td style="font-weight: bold;padding: 5px 10px;">{{ $order->currency }} {{$order_product->order_price}}</td>
                      </tr>
                      @php $product_total += $order_product->product_price; @endphp
                      @php $total += $order_product->order_price; @endphp
                    @endforeach
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
        <table cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td align="right">
               <table align="right" style="width: 230px;">
                  <tbody align="right">
                    <tr>
                      <td align="left"><div style="color: #898989;font-size: 14px;padding-top: 10px;">Subtotal</div></td>
                      <td align="right" style="padding-right: 10px;"><div style="color: #898989;font-size: 14px;font-weight: bold;padding-top: 10px;padding-left: 20px;">{{ $order->currency }}{{$product_total}}</div></td>
                    </tr>
                     <tr>
                      <td align="left"><div style="color: #000000;font-size: 14px;font-weight: bold;padding-top: 10px;">Total <span style="font-size: 11px;font-weight: normal;color: #898989;">(Inclusive of VAT)</span></div></td>
                      <td align="right" style="padding-right: 10px;"><div style="color: #000000;font-size: 14px;font-weight: bold;padding-top: 10px;padding-left: 20px;">{{ $order->currency }}{{$total}}</div></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%;padding: 30px;">
        <table border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td style="color: #898989;font-size: 13px;padding-top: 5px;padding-bottom: 10px;">We'll let you know when your order is on its way to you so you can really get excited about it.</td>
            </tr>
            <tr>
               <td style="color: #000000;font-size: 13px;padding-top: 5px;padding-bottom: 10px;font-weight: bold;">Team Solo Luxury</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%;background-color: #f4e7e1;padding: 30px;">
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td style="padding-bottom: 25px;">
                    <table align="left" style="width: 70%;">
                        <tbody>
                          <tr>
                             <td>
                              <div style="float: left;margin-top: 3px;"><img src="{{ asset('images/emails/mail.png') }}"></div>
                              <div style="margin-left: 30px;"><a href="#" style="font-size: 12px; color: #000000;">customercare@sololuxury.com</a></div>
                            </td>
                          </tr>
                        </tbody>
                    </table>
                    <table align="right" style="width: 30%;">
                      <tbody>
                        <tr>
                          <td style="text-align: right;padding-top: 6px;">
                            <a href="#" style="display: inline-block; margin-left: 15px;"><img style="width: 6px;" src="{{ asset('images/emails/fb.png') }}"></a>
                            <a href="#" style="display: inline-block; margin-left: 15px;"><img style="width: 13px;" src="{{ asset('images/emails/tw.png') }}"></a>
                            <a href="#" style="display: inline-block; margin-left: 15px;"><img style="width: 13px;" src="{{ asset('images/emails/insta.png') }}"></a>
                            <a href="#" style="display: inline-block; margin-left: 15px;"><img style="width: 13px;" src="{{ asset('images/emails/linkin.png') }}"></a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                </td>
              </tr>
              <tr style="border-top: 2px solid #e8dad3;">
                <td style="padding: 25px 0 10px; text-align: center;font-size: 12px;color: #898989;">You are receiving this email as <a href="#" style="color: #000000;">customercare@sololuxury.com</a> is registered on <a href="#" style="color: #000000;">sololuxury.com</a>.</td>
              </tr>
              <tr>
                <td style="text-align: center;font-size: 12px;">2020 sololuxury. <a href="#" style="color: #898989;">Privacy Policy</a> | <a href="#" style="color: #898989;">Terms of Use</a> | <a href="#" style="color: #898989;">Terms of Sale</a></td>
              </tr>
            </tbody>
          </table>
      </div>
   </div>
</body>
</html>
