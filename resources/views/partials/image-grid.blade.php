@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">

                <!--roletype-->
                <h2 class="page-heading">Attach Images to Message</h2>

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
                <form action="{{ route('search') }}" method="GET" id="searchForm" class="form-inline align-items-start">
                    {{-- <div class="form-group">
                        <div class="row"> --}}
                            <div class="form-group mr-3 mb-3">
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
                                    <input hidden name="assigned_user" type="text" value="{{ $assigned_user ?? '' }}">
                                    <input hidden name="status" type="text" value="{{ $status ?? '' }}">
                                @endif
                            </div>
                            <div class="form-group mr-3 mb-3">
                              {!! $category_selection !!}
                            </div>

                            <div class="form-group mr-3">
                                @php $brands = \App\Brand::getAll(); @endphp
                                {{-- {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                                <select class="form-control select-multiple" name="brand[]" multiple>
                                  <optgroup label="Brands">
                                    @foreach ($brands as $key => $name)
                                      <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                                </select>
                            </div>

                            <div class="form-group mr-3">
                              {{-- <strong>Color</strong> --}}
                              @php $colors = new \App\Colors(); @endphp
                              {{-- {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                              <select class="form-control select-multiple" name="color[]" multiple>
                                <optgroup label="Colors">
                                  @foreach ($colors->all() as $key => $col)
                                    <option value="{{ $key }}" {{ isset($color) && $color == $key ? 'selected' : '' }}>{{ $col }}</option>
                                  @endforeach
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              @php $suppliers = new \App\ReadOnly\SupplierList(); @endphp
                              {{-- {!! Form::select('supplier[]',$suppliers->all(), (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                              <select class="form-control select-multiple" name="supplier[]" multiple>
                                <optgroup label="Suppliers">
                                  @foreach ($suppliers->all() as $key => $name)
                                    <option value="{{ $key }}" {{ isset($supplier) && $supplier == $key ? 'selected' : '' }}>{{ $name }}</option>
                                  @endforeach
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              <input name="size" type="text" class="form-control"
                                     value="{{ isset($size) ? $size : '' }}"
                                     placeholder="Size">
                            </div>

                            <div class="form-group mr-3">
                              <strong class="mr-3">Price</strong>
                              <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>
                            </div>

                            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                        {{-- </div>
                    </div> --}}
                </form>

                <form action="{{ route('search') }}" method="GET" id="quickProducts" class="form-inline align-items-start my-3">
                  <input type="hidden" name="quick_product" value="true">
                  <button type="submit" class="btn btn-xs btn-secondary">Quick Products</button>
                </form>


            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif



    <div class="productGrid" id="productGrid">
      @include('partials.image-load')
    </div>

        <form action="{{ $model_type == 'images' ? route('image.grid.attach') : ($status != 9 ? route('message.store') : url('whatsapp/updateAndCreate')) }}" method="POST" id="attachImageForm">
          @csrf

          <input type="hidden" name="images" id="images" value="">
          <input type="hidden" name="body" value="Images attached from grid">
          <input type="hidden" name="moduleid" value="{{ $model_id }}">
          <input type="hidden" name="moduletype" value="{{ $model_type }}">
          <input type="hidden" name="assigned_user" value="{{ $assigned_user }}" />
          <input type="hidden" name="status" value="{{ $status }}">
        </form>





	<?php $stage = new \App\Stage(); ?>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script>
    $(document).ready(function() {
       $(".select-multiple").multiselect();
       $("body").tooltip({ selector: '[data-toggle=tooltip]' });
    });
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
          $('#productGrid').html(data.html);
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
        $(this).toggleClass('btn-secondary');

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
          $('#productGrid').html(data.html);
        }).fail(function() {
          alert('Error searching for products');
        });
      });

      $('#quickProducts').on('submit', function(e) {
        e.preventDefault();

        var url = "{{ route('search') }}";
        var formData = $('#quickProducts').serialize();

        $.ajax({
          url: url,
          data: formData
        }).done(function(data) {
          $('#productGrid').html(data.html);
          console.log(data);
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

            $(document).on('click', '#sendImageMessage', function() {
              $('#attachImageForm').submit();
            });
        // });

    </script>
@endsection
