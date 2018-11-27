@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">

                <!--roletype-->
                <h2>{{ $roletype }}</h2>

                <!--pending products count-->
                @can('admin')
                  @if( $roletype != 'Selection' && $roletype != 'Sale' )
                      <div class="pt-2 pb-3">
                          <a href="{{ route('pending',$roletype) }}"><strong>Pending
                                  : </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
                      </div>
                  @endif
                @endcan

                <!--attach Product-->
                @if( isset($doSelection) )
                    <p><strong> {{ strtoupper($model_type)  }} ID : {{ $model_id }} </strong></p>
                @endif

                <!--Product Search Input -->
                <form action="{{ route('search') }}" method="GET" id="searchForm">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="term" type="text" class="form-control" id="product-search"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="sku,brand,category,status,stage">
                                <input hidden name="roletype" type="text" value="{{ $roletype }}">
                                {{--@if( $roletype == 'Sale' )
                                    <input hidden name="saleId" type="text" value="{{ $sale_id ?? '' }}">
                                @endif--}}
                                @if( isset($doSelection) )
                                    <input hidden name="doSelection" type="text" value="true">
                                    <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                                    <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                                @endif
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                            <div class="col-md-2">
                              <strong>Brands</strong>
                              @php $brands = \App\Brand::getAll(); @endphp
            	                {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

                            <div class="col-md-2">
                              <strong>Color</strong>
                              @php $colors = new \App\Colors(); @endphp
                              {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

                            <div class="col-md-2">
                              <strong>Category</strong>
                              {!! $category_selection !!}
                            </div>

                            <div class="col-md-2">
                              <strong>Price</strong>
                              <select class="form-control" name="price">
                                <option value>Select Price Range</option>
                                <option value="1" {{ (isset($price) && $price == 1) ? 'selected' : '' }}>Up to 10K</option>
                                <option value="2" {{ (isset($price) && $price == 2) ? 'selected' : '' }}>10K - 30K</option>
                                <option value="3" {{ (isset($price) && $price == 3) ? 'selected' : '' }}>30K - 50K</option>
                                <option value="4" {{ (isset($price) && $price == 4) ? 'selected' : '' }}>50K - 100K</option>
                              </select>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
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

    <div class="productGrid row" id="productGrid">
      @include('partials.image-load')
    </div>

    <div class="row mt-3 text-center">
      <div class="col">
        <form action="{{ route('message.store') }}" method="POST" id="attachImageForm">
          @csrf

          <input type="hidden" name="images" id="images" value="">
          <input type="hidden" name="body" value="Images attached to grid">
          <input type="hidden" name="moduleid" value="{{ $model_id }}">
          <input type="hidden" name="moduletype" value="{{ $model_type }}">
          <input type="hidden" name="assigned_user" value="{{ $assigned_user }}" />
          <input type="hidden" name="status" value="{{ $status }}">
          <button type="submit" class="btn btn-success">Send to Message</button>
        </form>
      </div>
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

	<?php $stage = new \App\Stage(); ?>

    <script>
      var searchSuggestions = {!! json_encode($search_suggestions) !!};
      var image_array = [];

      $('#product-search').autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

          response(results.slice(0, 10));
        }
      });

      $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');

        getProducts(url);
      });

      function getProducts(url) {
        $.ajax({
          url: url
        }).done(function(data) {
          $('#productGrid').html(data);
        }).fail(function() {
          alert('Error loading more products');
        });
      }

      $(document).on('click', '.attach-photo', function(e) {
        e.preventDefault();
        var image = $(this).data('image');

        if ($(this).data('attached') == 0) {
          $(this).data('attached', 1);
          image_array.push(image);
        } else {
          var index = image_array.indexOf(image);

          $(this).data('attached', 0);
          image_array.splice(index, 1);
        }

        $(this).toggleClass('btn-success');

        console.log(image_array);
      });

      $('#attachImageForm').on('submit', function(e) {
        e.preventDefault();

        if (image_array.length == 0) {
          alert('Please select some images');
        } else {
          $('#images').val(JSON.stringify(image_array));
          $('#attachImageForm')[0].submit();
        }
      });

      $('#searchForm').on('submit', function(e) {
        e.preventDefault();

        var url = "{{ route('search') }}";
        var formData = $('#searchForm').serialize();

        $.ajax({
          url: url,
          data: formData
        }).done(function(data) {
          $('#productGrid').html(data);
        }).fail(function() {
          alert('Error searching for products');
        });
      });


            // $('#product-search').on('keyup', function() {
            //   alert('t');
            // });

            {{--@if($roletype == 'Supervisor')
            @can('supervisor-edit')
                attactApproveEvent();
            @endcan
            @endif--}}

            jQuery('.btn-attach').click(function (e) {

                e.preventDefault();

                let btn = jQuery(this);
                let product_id = btn.attr('data-id');
                let model_id = btn.attr('model-id');
                let model_type = btn.attr('model-type');


                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '/attachProductToModel/'+ model_type + '/' + model_id + '/' + product_id,

                    success: function (response) {

                        if (response.msg === 'success') {
                            btn.toggleClass('btn-success');
                            btn.html(response.action);
                        }
                    }
                });
            });
        // });

    </script>
@endsection
