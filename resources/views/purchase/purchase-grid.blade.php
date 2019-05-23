@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Purchase {{ $page == 'canceled-refunded' ? 'Canceled / Refunded' : ($page == 'delivered' ? 'Delivered' : ($page == 'ordered' ? 'Ordered' : '')) }} Grid</h2>
        </div>
    </div>

    <div class="row">
      <div class="col-lg-12 margin-tb">
        <form action="{{ route('purchase.grid') }}" method="GET" class="form-inline align-items-start">
            <div class="form-group mr-3 mb-3">
              <input name="term" type="text" class="form-control" id="product-search"
                     value="{{ isset($term) ? $term : '' }}"
                     placeholder="name, sku, supplier">
            </div>

            @if (!$page)
              <div class="form-group mr-3">
                <select class="form-control select-multiple" name="status[]" multiple>
                  <optgroup label="Order Status">
                    @foreach ($order_status as $key => $name)
                      <option value="{{ $key }}" {{ !empty($status) && in_array($key, $status) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </optgroup>
                </select>
              </div>
            @endif

            <div class="form-group mr-3">
              {!! Form::select('supplier[]', $suppliers_array, (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control select-multiple']) !!}
            </div>

            <div class="form-group mr-3">
                @php $brands = \App\Brand::getAll(); @endphp
                <select class="form-control select-multiple" name="brand[]" multiple>
                  <optgroup label="Brands">
                    @foreach ($brands as $key => $name)
                      <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </optgroup>
                </select>
            </div>

            <button type="submit" class="btn btn-image"><img src="/images/search.png" /></button>
        </form>
      </div>
    </div>

    @include('partials.flash_messages')

    {!! $products->appends(Request::except('page'))->links() !!}

	<?php
	$query = http_build_query( Request::except( 'page' ) );
	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
	?>

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;" class="form-control">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <form action="{{ route('purchase.store') }}" method="POST">
      @csrf
      <input type="hidden" name="purchase_handler" value="{{ Auth::id() }}" />
      <input type="hidden" name="supplier_id" value="" />

      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Product</th>
              <th>SKU</th>
              <th>Customers</th>
              <th>Price In Order</th>
              <th>Order Date</th>
              <th>Order Advance</th>
              <th>Supplier</th>
              <th>Suppliers</th>
              <th>Brand</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($products as $product)
              <tr>
                <td>
                  <input type="checkbox" class="select-product" name="products[]" value="{{ $product['id'] }}" data-supplier="{{ $product['single_supplier'] }}" />
                </td>
                <td>
                  <a href="{{ route('products.show', $product['id']) }}" target="_blank"><img src="{{ $product['image'] }}" class="img-responsive" style="width: 100px !important" alt=""></a>
                </td>
                <td>{{ $product['sku'] }}</td>
                <td>
                  @foreach ($product['customers'] as $customer)
                    <a href="{{ route('customer.show', $customer->id) }}" target="_blank">{{ $customer->name }}</a>
                  @endforeach
                </td>
                <td>
                  <ul>
                    @foreach ($product['order_products'] as $order_product)
                      <li>{{ $order_product->product_price }}</li>
                    @endforeach
                  </ul>
                </td>
                <td>
                  <ul>
                    @foreach ($product['order_products'] as $order_product)
                      @if ($order_product->order)
                        <li>{{ \Carbon\Carbon::parse($order_product->order->order_date)->format('d-m') }}</li>
                      @else
                        <li>No Order</li>
                      @endif
                    @endforeach
                  </ul>
                </td>
                <td>
                  <ul>
                    @foreach ($product['order_products'] as $order_product)
                      @if ($order_product->order)
                        <li>{{ $order_product->order->advance_detail }}</li>
                      @else
                        <li>No Order</li>
                      @endif
                    @endforeach
                  </ul>
                </td>
                <td>{{ array_key_exists($product['single_supplier'], $suppliers_array) ? $suppliers_array[$product['single_supplier']] : 'No Supplier' }}</td>
                <td>{{ $product['supplier_list'] }}</td>
                <td>{{ $product['brand'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="row">
        <div class="col text-center">
          <button type="submit" class="btn btn-secondary">Submit</button>
        </div>
      </div>
    </form>

    {{-- <div class="row mt-6"> --}}
    <div class="purchaseGrid" id="purchaseGrid">

      {{-- @foreach ($products as $supplier => $supplier_products)
        <h4>{{ $supplier }}</h4>
        <div class="row mt-6">
          {{dd($products)}}
          @foreach ($supplier_products as $product)
            <div class="col-md-3 col-xs-6 text-center">
              <img src="{{ $product['image'] }}" class="img-responsive grid-image" alt="" />

              {{dd($product)}}
              <a href="{{ route('products.show', $product['id']) }}" class="btn btn-image"><img src="/images/view.png" /></a>
            </div>
          @endforeach
        </div>


      @endforeach --}}
    </div>

    {!! $products->appends(Request::except('page'))->links() !!}

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;" class="form-control">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    {{-- {!! $leads->links() !!} --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script>
    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });


        // Array.prototype.groupBy = function (prop) {
        //     return this.reduce(function (groups, item) {
        //         const val = item[prop]
        //         groups[val] = groups[val] || []
        //         groups[val].push(item)
        //         return groups
        //     }, {})
        // };
        //
        // var suppliers_array = {!! json_encode($suppliers_array) !!};
        // const products = [
        //         @foreach ($products as $product)
        //
        //     {
        //         'id': '{{ $product['id'] }}',
        //         'sku': '{{ $product['sku'] }}',
        //         'supplier': '{{ $product['supplier'] }}',
        //         'suppliers' : "{{ $product['supplier_list'] }}",
        //         'single_supplier': "{{ $product['single_supplier'] }}",
        //         'image': '{{ $product['image']}}',
        //         'link': '{{ route('products.show', $product['id']) }}',
        //         'customer_id': '{{ $product['customer_id'] != 'No Customer' && $product['customer_id'] != 'No Order' ? route('customer.show', $product['customer_id']) : '#noCustomer' }}',
        //         'customer_names': '{{ $product['customer_names'] }}',
        //         'order_price': '{{ $product['order_price'] }}',
        //         'order_date': '{{ $product['order_date'] }}'
        //
        //     },
        //     @endforeach
        // ];
        //
        // const groupedByTime = products.groupBy('single_supplier');
        //
        // jQuery(document).ready(function () {
        //
        //     Object.keys(groupedByTime).forEach(function (key) {
        //
        //         let html = '<form action="{{ route('purchase.store') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}" /><input type="hidden" name="purchase_handler" value="{{ Auth::id() }}" /><input type="hidden" name="supplier_id" value="' + key + '" />';
        //             html += '<div class="supplier-wrapper"><div class="form-check pull-right"><input type="checkbox" class="select-all" id="' + key.replace(/[^a-zA-Z0-9]/g, '-') + '"><label class="form-check-label" for="' + key.replace(/[^a-zA-Z0-9]/g, '-') + '">Select All</label></div><h4>' + suppliers_array[key] + '</h4></div><div class="row">';
        //
        //         groupedByTime[key].forEach(function (product) {
        //
        //             html += `
        //                 <div class="col-md-3 col-xs-6 text-center">
        //                   <a href="` + product['customer_id'] + `">
        //                     <img src="` + product['image'] + `" class="img-responsive grid-image" alt="" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Name: </strong>` + product['customer_names'] + `<br><strong>Price in Order: </strong>` + product['order_price'] + `<br><strong>Order Date: </strong>` + moment(product['order_date']).format('DD-MM') + `<br><strong>Supplier: </strong>` + product['supplier'] + `<br><strong>Suppliers: </strong>` + product['suppliers'] + `<br><strong>Sku: </strong>` + product['sku'] + `" />
        //                                     <input type="checkbox" class="` + key.replace(/[^a-zA-Z0-9]/g, '-') + `" name="products[]" value="` + product['id'] + `">
        //                                     <a href="` + product['link'] + `" class="btn btn-image"><img src="/images/view.png" /></a>
        //                                      {{--<p>Status : `+ ( ( product['isApproved'] ===  '1' ) ?
        //                                                             'Approved' : ( product['isApproved'] ===  '-1' ) ? 'Rejected' : 'Nil') +`</p>--}}
        //                     {{--@can('supervisor-edit')
        //                         <button data-id="`+product['id']+`"
        //                                 class="btn btn-approve btn-secondary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
        //                                 `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
        //                         </button>
        //                     @endcan--}}
        //
        //                 </a></div>
        //             `;
        //         });
        //
        //         jQuery('#purchaseGrid').append(html + '</div><div class="row"><div class="col text-center"><button type="submit" class="btn btn-secondary">Submit</button></div></div></form>');
        //     });
        //
        // });

        $(document).ready(function() {
          $("body").tooltip({ selector: '[data-toggle=tooltip]' });
        });

        $(document).on('click', '.select-all', function() {
          var id = $(this).attr('id');

          if ($(this).is(':checked')) {
            $('.' + id).prop('checked', true);
          } else {
            $('.' + id).prop('checked', false);
          }
        });

        $(document).on('click', '.select-product', function() {
          var supplier_id = $(this).data('supplier');

          $('input[name="supplier_id"]').val(supplier_id);
        });

    </script>

@endsection
