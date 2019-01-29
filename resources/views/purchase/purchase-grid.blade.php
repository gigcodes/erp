@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Purchase Grid</h2>
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

            <div class="form-group mr-3">
              <select class="form-control select-multiple" name="status[]" multiple>
                <optgroup label="Order Status">
                  @foreach ($order_status as $key => $name)
                    <option value="{{ $key }}" {{ !empty($status) && in_array($key, $status) ? 'selected' : '' }}>{{ $name }}</option>
                  @endforeach
              </optgroup>
              </select>
            </div>

            <div class="form-group mr-3">
              {!! Form::select('supplier[]', $supplier_list, (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control select-multiple']) !!}
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

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {!! $products->appends(Request::except('page'))->links() !!}

	<?php
	$query = http_build_query( Request::except( 'page' ) );
	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
	?>

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

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
                <select onchange="location.href = this.value;">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    {{-- {!! $leads->links() !!} --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script>
    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });


        Array.prototype.groupBy = function (prop) {
            return this.reduce(function (groups, item) {
                const val = item[prop]
                groups[val] = groups[val] || []
                groups[val].push(item)
                return groups
            }, {})
        };

        const products = [
                @foreach ($products as $product)

            {
                'id': '{{ $product['id'] }}',
                'sku': '{{ $product['sku'] }}',
                'supplier': '{{ $product['supplier'] }}',
                'image': '{{ $product['image']}}',
                'link': '{{ route('products.show', $product['id']) }}',
                'customer_id': '{{ $product['customer_id'] != 'No Customer' && $product['customer_id'] != 'No Order' ? route('customer.show', $product['customer_id']) : '#noCustomer' }}',
                'customer_name': '{{ $product['customer_name'] }}',
                'order_price': '{{ $product['order_price'] }}',
                'order_date': '{{ $product['order_date'] }}'

            },
            @endforeach
        ];

        const groupedByTime = products.groupBy('supplier');

        jQuery(document).ready(function () {

            Object.keys(groupedByTime).forEach(function (key) {

                let html = '<form action="{{ route('purchase.store') }}" method="POST"><input type="hidden" name="_token" value="{{ csrf_token() }}" /><input type="hidden" name="purchase_handler" value="{{ Auth::id() }}" /><input type="hidden" name="supplier" value="' + key + '" />';
                    html += '<div class="supplier-wrapper"><div class="form-check pull-right"><input type="checkbox" class="select-all" id="' + key.replace(/[^a-zA-Z0-9]/g, '-') + '"><label class="form-check-label" for="' + key.replace(/[^a-zA-Z0-9]/g, '-') + '">Select All</label></div><h4>' + key + '</h4></div><div class="row">';

                groupedByTime[key].forEach(function (product) {

                    html += `
                        <div class="col-md-3 col-xs-6 text-center">
                          <a href="` + product['customer_id'] + `">
                            <img src="` + product['image'] + `" class="img-responsive grid-image" alt="" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Name: </strong>` + product['customer_name'] + `<br><strong>Price in Order: </strong>` + product['order_price'] + `<br><strong>Order Date: </strong>` + moment(product['order_date']).format('DD-MM') + `<br><strong>Supplier: </strong>` + product['supplier'] + `<br><strong>Sku: </strong>` + product['sku'] + `" />
                                            <input type="checkbox" class="` + key.replace(/[^a-zA-Z0-9]/g, '-') + `" name="products[]" value="` + product['id'] + `">
                                            <a href="` + product['link'] + `" class="btn btn-image"><img src="/images/view.png" /></a>
                                             {{--<p>Status : `+ ( ( product['isApproved'] ===  '1' ) ?
                                                                    'Approved' : ( product['isApproved'] ===  '-1' ) ? 'Rejected' : 'Nil') +`</p>--}}
                            {{--@can('supervisor-edit')
                                <button data-id="`+product['id']+`"
                                        class="btn btn-approve btn-secondary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                        `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                                </button>
                            @endcan--}}

                        </a></div>
                    `;
                });

                jQuery('#purchaseGrid').append(html + '</div><div class="row"><div class="col text-center"><button type="submit" class="btn btn-secondary">Submit</button></div></div></form>');
            });

        });

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

    </script>

@endsection
