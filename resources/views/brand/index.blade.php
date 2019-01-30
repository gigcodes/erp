@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Brand List</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('brand.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
      <div class="col-12">
        <div class="form-inline">
          <div class="form-group">
            <input type="number" id="product_price" step="0.01" class="form-control" placeholder="Product price">
          </div>

          <div class="form-group ml-3">
            <select class="form-control" id="brand">
              @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" data-brand="{{ $brand }}">{{ $brand->name }}</option>
              @endforeach
            </select>
          </div>

          <button type="button" id="calculatePriceButton" class="btn btn-secondary ml-3">Calculate</button>
        </div>

        <div id="result-container">

        </div>
      </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Euro to Inr</th>
            <th>Deduction%</th>
            <th width="200px">Action</th>
        </tr>
        @foreach ($brands as $key => $brand)
            <tr>
                <td>{{ $brand->id }}</td>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->euro_to_inr }}</td>
                <td>{{ $brand->deduction_percentage }}</td>
                <td>
                    <a class="btn btn-image" href="{{ route('brand.edit',$brand->id) }}"><img src="/images/edit.png" /></a>
                    {!! Form::open(['method' => 'DELETE','route' => ['brand.destroy',$brand->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $brands->links() !!}

@endsection

@section('scripts')
  <script type="text/javascript">
    $('#calculatePriceButton').on('click', function() {
      var price = $('#product_price').val();
      var brand = $(':selected').data('brand');

      var price_inr = Math.round(Math.round(price * brand.euro_to_inr) / 1000) * 1000;
      var price_special = Math.round(Math.round(price_inr - (price_inr * brand.deduction_percentage) / 100) / 1000) * 1000;

      var result = '<strong>INR Price: </strong>' + price_inr + '<br><strong>Special Price: </strong>' + price_special;

      $('#result-container').html(result);
    });
  </script>
@endsection
