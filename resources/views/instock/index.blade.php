@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <div class="">
        <h2 class="page-heading">In stock Products</h2>

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


  @if ($message = Session::get('success'))
  <div class="alert alert-success">
    {{ $message }}
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
