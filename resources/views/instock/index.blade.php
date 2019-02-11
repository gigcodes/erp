@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <div class="">
        <h2 class="page-heading">In stock Products</h2>

        <div class="pull-right">
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productModal">Upload Products</button>
        </div>

        <!--Product Search Input -->
        <form action="{{ route('productinventory.instock') }}" method="GET" id="searchForm" class="form-inline align-items-start">
          <input type="hidden" name="type" value="{{ $type }}">
          <input type="hidden" name="date" value="{{ $date }}">
          <div class="form-group mr-3 mb-3">
            <input name="term" type="text" class="form-control" id="product-search" value="{{ isset($term) ? $term : '' }}" placeholder="sku,brand,category,status">
          </div>
          <div class="form-group mr-3 mb-3">
            {!! $category_selection !!}
          </div>

          <div class="form-group mr-3 mb-3">
            <strong class="mr-3">Price</strong>
            <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]" />
          </div>

          <div class="form-group mr-3">
            @php $brands = \App\Brand::getAll();
            @endphp
            <select class="form-control select-multiple" name="brand[]" multiple>
              <optgroup label="Brands">
                @foreach ($brands as $id => $name)
                  <option value="{{ $id }}" {{ isset($brand) && $brand == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <div class="form-group mr-3">
            @php $colors = new \App\Colors();
            @endphp
            <select class="form-control select-multiple" name="color[]" multiple>
              <optgroup label="Colors">
                @foreach ($colors->all() as $id => $name)
                  <option value="{{ $id }}" {{ isset($color) && $color == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </form>


      </div>
    </div>
  </div>

  <div id="productModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Product</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">


        <div class="modal-body">
          @csrf
          <input type="hidden" name="supplier" value="In-stock">
          <div class="form-group">
              <strong>Image:</strong>
              <input type="file" class="form-control" name="image"
                     value="{{ old('image') }}" id="product-image" required/>
              @if ($errors->has('image'))
                  <div class="alert alert-danger">{{$errors->first('image')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>Name:</strong>
              <input type="text" class="form-control" name="name" placeholder="Name"
                     value="{{ old('name') }}"  id="product-name" required />
              @if ($errors->has('name'))
                  <div class="alert alert-danger">{{$errors->first('name')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>SKU:</strong>
              <input type="text" class="form-control" name="sku" placeholder="SKU"
                     value="{{ old('sku') }}"  id="product-sku" required/>
              @if ($errors->has('sku'))
                  <div class="alert alert-danger">{{$errors->first('sku')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>Color:</strong>
              <input type="text" class="form-control" name="color" placeholder="Color"
                     value="{{ old('color') }}"  id="product-color"/>
              @if ($errors->has('color'))
                  <div class="alert alert-danger">{{$errors->first('color')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>Brand:</strong>
              <?php
              $brands = \App\Brand::getAll();
              echo Form::select('brand',$brands, ( old('brand') ? old('brand') : '' ), ['placeholder' => 'Select a brand','class' => 'form-control', 'id'  => 'product-brand']);?>
                {{--<input type="text" class="form-control" name="brand" placeholder="Brand" value="{{ old('brand') ? old('brand') : $brand }}"/>--}}
                @if ($errors->has('brand'))
                    <div class="alert alert-danger">{{$errors->first('brand')}}</div>
                @endif
          </div>

          <div class="form-group">
              <strong>Price:</strong>
              <input type="number" class="form-control" name="price" placeholder="Price"
                     value="{{ old('price') }}" step=".01"  id="product-price"/>
              @if ($errors->has('price'))
                  <div class="alert alert-danger">{{$errors->first('price')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>Size:</strong>
              <input type="text" class="form-control" name="size" placeholder="Size"
                     value="{{ old('size') }}"  id="product-size"/>
              @if ($errors->has('size'))
                  <div class="alert alert-danger">{{$errors->first('size')}}</div>
              @endif
          </div>

          <div class="form-group">
              <strong>Quantity:</strong>
              <input type="number" class="form-control" name="quantity" placeholder="Quantity"
                     value="{{ old('quantity') }}"  id="product-quantity"/>
              @if ($errors->has('quantity'))
                  <div class="alert alert-danger">{{$errors->first('quantity')}}</div>
              @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Create</button>
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

  @if ($errors->any())
      <div class="alert alert-danger">
          @foreach ($errors->all() as $msg)
            <li>{{ $msg }}</li>
          @endforeach
      </div>
  @endif

  <div class="productGrid" id="productGrid">
    @include('instock.product-items')
  </div>

  <form action="{{ route('stock.privateViewing.store') }}" method="POST" id="selectProductForm">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
    <input type="hidden" name="products" id="selected_products" value="">
  </form>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script>
    var searchSuggestions = {!!json_encode($search_suggestions) !!};
    var product_array = [];

    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });

    // $('#product-search').autocomplete({
    //   source: function(request, response) {
    //     var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
    //
    //     response(results.slice(0, 10));
    //   }
    // });

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

    $('#searchForm').on('submit', function(e) {
      e.preventDefault();

      var url = "{{ route('productinventory.instock') }}";
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

    $(document).on('click', '.select-product', function(e) {
      e.preventDefault();
      var product_id = $(this).data('id');

      if ($(this).data('attached') == 0) {
        $(this).data('attached', 1);
        product_array.push(product_id);
      } else {
        var index = product_array.indexOf(product_id);

        $(this).data('attached', 0);
        product_array.splice(index, 1);
      }

      $(this).toggleClass('btn-success');
      $(this).toggleClass('btn-secondary');
    });

    $(document).on('click', '#privateViewingButton', function() {
      if (product_array.length == 0) {
        alert('Please select some products');
      } else {
        $('#selected_products').val(JSON.stringify(product_array));
        $('#selectProductForm').submit();
      }
    });
  </script>
@endsection
