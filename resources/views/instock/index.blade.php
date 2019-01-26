@extends('layouts.app')

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <div class="">
        <h2>In stock Products</h2>

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
            {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
          </div>

          <div class="form-group mr-3">
            @php $colors = new \App\Colors();
            @endphp
            {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control', 'multiple' => true]) !!}
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

  <script>
    var searchSuggestions = {!!json_encode($search_suggestions) !!};
    var product_array = [];

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

      console.log(product_array);
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
