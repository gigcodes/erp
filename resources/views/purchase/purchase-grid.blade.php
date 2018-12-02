@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2>Purchase Grid</h2>
        </div>
    </div>

    <div class="row">
      <div class="col-lg-12 margin-tb">
        <form action="{{ route('purchase.grid') }}" method="GET" class="form-inline align-items-start">
            <div class="form-group mr-3 mb-3">
                {{-- <div class="row"> --}}
                    {{-- <div class="col-md-4"> --}}
                        <input name="term" type="text" class="form-control" id="product-search"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="name, sku, supplier">
                        {{-- <input hidden name="roletype" type="text" value="{{ $roletype }}"> --}}
                        {{--@if( $roletype == 'Sale' )
                            <input hidden name="saleId" type="text" value="{{ $sale_id ?? '' }}">
                        @endif--}}
                        {{-- @if( isset($doSelection) )
                            <input hidden name="doSelection" type="text" value="true">
                            <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                            <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                        @endif --}}
                      </div>
                    {{-- </div> --}}




                    <button type="submit" class="btn btn-image"><img src="/images/search.png" /></button>
                {{-- </div>
            </div> --}}
        </form>
      </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
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

    <script>


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
                'supplier': '{{ $product['supplier'] }}',
                'image': '{{ $product['image']}}',
                'link': '{{ route('products.show', $product['id']) }}'

            },
            @endforeach
        ];

        const groupedByTime = products.groupBy('supplier');

        jQuery(document).ready(function () {

            Object.keys(groupedByTime).forEach(function (key) {

                let html = '<div class="supplier-wrapper"><div class="form-check pull-right"><input type="checkbox" class="select-all" id="' + key + '"><label class="form-check-label" for="' + key + '">Select All</label></div><h4>' + key + '</h4></div><div class="row">';

                groupedByTime[key].forEach(function (product) {

                    html += `
                        <div class="col-md-3 col-xs-6 text-center">

                            <img src="` + product['image'] + `" class="img-responsive grid-image" alt="" />
                                            <input type="checkbox" class="` + key + `" name="products[]" value="` + product['id'] + `">
                                            <a href="` + product['link'] + `" class="btn btn-image"><img src="/images/view.png" /></a>
                                             {{--<p>Status : `+ ( ( product['isApproved'] ===  '1' ) ?
                                                                    'Approved' : ( product['isApproved'] ===  '-1' ) ? 'Rejected' : 'Nil') +`</p>--}}
                            {{--@can('supervisor-edit')
                                <button data-id="`+product['id']+`"
                                        class="btn btn-approve btn-secondary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                        `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                                </button>
                            @endcan--}}

                        </div>
                    `;
                });

                jQuery('#purchaseGrid').append(html + '</div><div class="row"><div class="col text-center"><button type="submit" class="btn btn-secondary">Submit</button></div></div>');
            });

        });

        $(document).on('click', '.select-all', function() {
          var id = $(this).attr('id');
          $('.' + id).attr('checked', true);
          console.log($('.' + id));
        });

    </script>

@endsection
