@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Private Viewing</h2>
            <div class="pull-left">

                {{-- <form action="/purchases/" method="GET">
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
                </form> --}}
            </div>
            {{-- <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('stock.create') }}">+</a>
            </div> --}}
        </div>
    </div>

    @if ($message = Session::get('success'))
      <div class="alert alert-success">
        <p>{{ $message }}</p>
      </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
        <tr>
          <th>Customer</th>
          <th>Products</th>
          <th>Date</th>
          <th>Delivery Images</th>
          <th width="280px">Action</th>
        </tr>
        @foreach ($private_views as $key => $view)
            <tr class="{{ \Carbon\Carbon::parse($view->date)->format('Y-m-d') == date('Y-m-d') ? 'row-highlight' : '' }}">
                <td>{{ $view->customer->name }}</td>
                <td>
                  @foreach ($view->products as $product)
                    <img src="{{ $product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="img-responsive" style="width: 50px;" alt="">
                  @endforeach
                </td>
                <td>{{ Carbon\Carbon::parse($view->date)->format('d-m-Y') }}</td>
                <td>
                  @if ($view->getMedia(config('constants.media_tags'))->first())
                    @foreach ($view->getMedia(config('constants.media_tags')) as $image)
                      <a href="{{ $image->getUrl() }}" target="_blank" class="d-inline-block">
                        <img src="{{ $image->getUrl() }}" class="img-responsive" style="width: 50px;" alt="">
                      </a>
                    @endforeach
                  @elseif (\Carbon\Carbon::parse($view->date)->format('Y-m-d') == date('Y-m-d'))
                    <form action="{{ route('stock.private.viewing.upload') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <div class="form-group">
                        <input type="hidden" name="view_id" value="{{ $view->id }}">
                        <input type="file" name="images[]" required multiple>
                      </div>

                      <button type="submit" class="btn btn-xs btn-secondary">Upload</button>
                    </form>
                  @endif
                </td>
                <td>
                  {{-- <a class="btn btn-image" href="{{ route('stock.show', $stock->id) }}"><img src="/images/view.png" /></a>

                  {!! Form::open(['method' => 'DELETE','route' => ['stock.destroy', $stock->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {!! Form::close() !!}

                  {!! Form::open(['method' => 'DELETE','route' => ['stock.permanentDelete', $stock->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!} --}}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $private_views->appends(Request::except('page'))->links() !!}
@endsection
