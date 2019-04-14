@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrap Activity</h2>
            {{-- <div class="pull-left">
                <form action="/order/" method="GET">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search">
                            </div>
                            <div class="col-md-4">
                                <button hidden type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('order.create') }}">+</a>
            </div> --}}
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th rowspan="2">Date</th>
            <th colspan="4" class="text-center">G&B</th>
            <th colspan="4" class="text-center">Wise Boutique</th>
            <th colspan="4" class="text-center">Double F</th>
          </tr>
          <tr>
            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($data as $date => $item)
            <tr>
              <td>{{ \Carbon\Carbon::parse($date)->format('d-m') }}</td>
              <td>{{ $item['G&B']['scraped'] ?? 0 }}</td>
              <td>{{ $item['G&B']['created'] ?? 0 }}</td>
              <td>{{ $item['G&B']['inventory'] ?? 0 }}</td>
              <td>{{ $item['G&B']['removed'] ?? 0 }}</td>

              <td>{{ $item['Wiseboutique']['scraped'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['created'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['inventory'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['removed'] ?? 0 }}</td>

              <td>{{ $item['DoubleF']['scraped'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['created'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['inventory'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['removed'] ?? 0 }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $data->appends(Request::except('page'))->links() !!}

@endsection
