<div id="update-invoice-address">
   <form method="post" action="{{route('order.update.customer.address')}}">
       @csrf
       @foreach($invoice->orders as $order) 
          <div class="card">
            <div class="card-header">{{$order->order_id}}</div>
            <div class="card-body">
              @php
                  $shipping  = $order->shippingAddress();
              @endphp
              <div class="col-md-12">
                <div class="form-group">
                   <strong>Address:</strong>
                   <textarea name="order[{{$order->id}}][street]" class="form-control">@if($shipping){{$shipping->street}}@endif</textarea>
                </div>
                <div class="form-group">
                   <strong>City:</strong>
                   <input name="order[{{$order->id}}][city]" class="form-control" value="@if($shipping){{$shipping->city}}@endif" />
                </div>
                <div class="form-group">
                   <strong>Country:</strong>
                   <input name="order[{{$order->id}}][country_id]" class="form-control" value="@if($shipping){{$shipping->country_id}}@endif"/>
                </div>
                <div class="form-group">
                   <strong>Pincode:</strong>
                   <input name="order[{{$order->id}}][postcode]" class="form-control" value="@if($shipping){{$shipping->postcode}}@endif"/>
                </div>
              </div>
              @foreach($order->order_product as $orderProduct) 
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">Order Product #{{$orderProduct->id}}</div>
                    <div class="card-body">
                       <div class="row">
                          <div class="col">
                              <div class="form-group">
                                  <strong>SKU&nbsp;:&nbsp;</strong>
                                  <input name="order_product[{{$orderProduct->id}}][sku]" class="form-control" value="{{$orderProduct->sku}}" />
                              </div>
                          </div>
                          <div class="col">
                              <div class="form-group">
                                  <strong>Price&nbsp;:&nbsp;</strong>
                                  <input name="order_product[{{$orderProduct->id}}][product_price]" class="form-control" value="{{$orderProduct->product_price}}" />
                              </div>
                          </div>
                          <div class="col">
                              <div class="form-group">
                                  <strong>Size&nbsp;:&nbsp;</strong>
                                  <input name="order_product[{{$orderProduct->id}}][size]" class="form-control" value="{{$orderProduct->size}}" />
                              </div>
                          </div>
                          <div class="col">
                              <div class="form-group">
                                  <strong>Color&nbsp;:&nbsp;</strong>
                                  <input name="order_product[{{$orderProduct->id}}][color]" class="form-control" value="{{$orderProduct->color}}" />
                              </div>
                          </div>
                          <div class="col">
                              <div class="form-group">
                                  <strong>QTY&nbsp;:&nbsp;</strong>
                                  <input name="order_product[{{$orderProduct->id}}][qty]" class="form-control" value="{{$orderProduct->qty}}" />
                              </div>
                          </div>
                      </div>
                     </div> 
                  </div>
                </div>  
              @endforeach
            </div>
          </div>
       @endforeach
      <button type="submit" name="update_details" data-id="{{$invoice->id}}" class="btn btn-primary btn-sm btn-update-invoice">Update Invoice</button>
   </form>
</div>