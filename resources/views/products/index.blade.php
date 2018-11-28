@extends('layouts.app')


@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h2>Products</h2>

      <form action="/products/" method="GET">
        <div class="form-group">
          <div class="row">
            <div class="col-md-8 pr-0">
              <input name="term" type="text" id="product-search" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Search">
            </div>
            <div class="col-md-4 pl-0">
              <button type="submit" class="btn btn-primary">Search</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="pull-right">
      @can('product-create')
      <a class="btn btn-success" href="{{ route('products.create') }}"> Create New Product</a>
      @endcan
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<table class="table table-bordered">
  <tr>
    <th>No</th>
    <th>Sku</th>
    <th>Name</th>
    <th width="280px">Action</th>
  </tr>
  @foreach ($products as $product)
  <tr>
    <td>{{ $product->id }}</td>
    <td>{{ $product->sku }}</td>
    <td>{{ $product->name }}</td>
    <td>
      <form action="{{ route('products.destroy',$product->id) }}" method="POST">
        <a class="btn btn-info" href="{{ route('products.show',$product->id) }}">Show</a>
        @csrf
        @method('DELETE')
        @can('product-delete')
        <button type="submit" class="btn btn-danger">Delete</button>
        @endcan
      </form>
    </td>
  </tr>
  @endforeach
</table>


{!! $products->appends(Request::except('page'))->links() !!}

<script type="text/javascript">
  var searchSuggestions = {!! json_encode($search_suggestions) !!};

  $('#product-search').autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

      response(results.slice(0, 10));
    }
  });
</script>


@endsection
