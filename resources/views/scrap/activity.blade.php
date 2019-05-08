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
            <th colspan="5" class="text-center">G&B</th>
            <th colspan="5" class="text-center">Wise Boutique</th>
            <th colspan="5" class="text-center">Double F</th>
            <th colspan="5" class="text-center">Lidia</th>
            <th colspan="5" class="text-center">Tory</th>
            <th colspan="5" class="text-center">Cuccuini</th>
          </tr>
          <tr>
            <th>Links</th>
            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

            <th>Links</th>
            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

            <th>Links</th>
            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

            <th>Links</th>
            <th>Scraped</th>
            <th>New</th>
            <th>Inventory</th>
            <th>Removed</th>

              <th>Links</th>
              <th>Scraped</th>
              <th>New</th>
              <th>Inventory</th>
              <th>Removed</th>

              <th>Links</th>
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
              <td>{{ $item['G&B']['links'] ?? 0 }}</td>
              <td>{{ $item['G&B']['scraped'] ?? 0 }}</td>
              <td>{{ $item['G&B']['created'] ?? 0 }}</td>
              <td>{{ $item['G&B']['1'] ?? 0 }}</td>
              <td>{{ $item['G&B']['0'] ?? 0 }}</td>

              <td>{{ $item['Wiseboutique']['links'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['scraped'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['created'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['1'] ?? 0 }}</td>
              <td>{{ $item['Wiseboutique']['0'] ?? 0 }}</td>

              <td>{{ $item['DoubleF']['links'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['scraped'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['created'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['1'] ?? 0 }}</td>
              <td>{{ $item['DoubleF']['0'] ?? 0 }}</td>

              <td>{{ $item['lidiashopping']['links'] ?? 0 }}</td>
              <td>{{ $item['lidiashopping']['scraped'] ?? 0 }}</td>
              <td>{{ $item['lidiashopping']['created'] ?? 0 }}</td>
              <td>{{ $item['lidiashopping']['1'] ?? 0 }}</td>
              <td>{{ $item['lidiashopping']['0'] ?? 0 }}</td>

                <td>{{ $item['Tory']['links'] ?? 0 }}</td>
                <td>{{ $item['Tory']['scraped'] ?? 0 }}</td>
                <td>{{ $item['Tory']['created'] ?? 0 }}</td>
                <td>{{ $item['Tory']['1'] ?? 0 }}</td>
                <td>{{ $item['Tory']['0'] ?? 0 }}</td>

                <td>{{ $item['Cuccuini']['links'] ?? 0 }}</td>
                <td>{{ $item['Cuccuini']['scraped'] ?? 0 }}</td>
                <td>{{ $item['Cuccuini']['created'] ?? 0 }}</td>
                <td>{{ $item['Cuccuini']['1'] ?? 0 }}</td>
                <td>{{ $item['Cuccuini']['0'] ?? 0 }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $data->appends(Request::except('page'))->links() !!}

    <hr>

    <h1>Monitor Lnks</h1>

    <table class="table table-striped">
        <tr>
            <th>Date</th>
            <th>Scanned Link</th>
            <th>Website</th>
        </tr>
        @foreach($link_entries as $entry)
            <tr>
                <td>{{ $entry->scraped_date }}</td>
                <td>{{ $entry->link_count }}</td>
                <td>{{ $entry->website }}</td>
            </tr>
        @endforeach
    </table>

@endsection
