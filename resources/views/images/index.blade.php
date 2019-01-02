@extends('layouts.app')


@section('content')
<div class="row">
  <div class="col-lg-10 margin-tb">
    <div class="mb-3">
      <h2>Image Grid</h2>

      {{-- <strong>Sort By</strong>
      <a href="{{ route('image.grid') . '?sortby=asc' }}" class="btn-link">ASC</a>
       |
      <a href="{{ route('image.grid') . '?sortby=desc' }}" class="btn-link">DESC</a> --}}
    </div>

  </div>
  <div class="col-lg-2">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#imageModal">Upload</button>

    <div id="imageModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Upload Images</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <form action="{{ route('image.grid.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-body">
              <div class="form-group">
                   <input type="file" name="images[]" multiple required />
                   @if ($errors->has('images'))
                       <div class="alert alert-danger">{{$errors->first('images')}}</div>
                   @endif
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Upload</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  {{ $message }}
</div>
@endif

<div class="row">
  @foreach ($images as $image)
  <div class="col-md-3 col-xs-6 text-center mb-5">
    <img src="{{ asset('uploads/social-media') . '/' . $image->filename }}" class="img-responsive grid-image" alt="" />

    <a class="btn btn-image" href="{{ route('image.grid.show',$image->id) }}"><img src="/images/view.png" /></a>
    <a class="btn btn-image" href="{{ route('image.grid.edit',$image->id) }}"><img src="/images/edit.png" /></a>

    {!! Form::open(['method' => 'DELETE','route' => ['image.grid.delete', $image->id],'style'=>'display:inline']) !!}
      <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
    {!! Form::close() !!}

    @if (isset($image->approved_user))
      <span>Approved by {{ App\User::find($image->approved_user)->name}} on {{ Carbon\Carbon::parse($image->approved_date)->format('d-m') }}</span>
    @else
      @if (Auth::user()->hasRole('Admin'))
        <form action="{{ route('image.grid.approveImage', $image->id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-secondary">Approve</button>
        </form>
      @endif
    @endif
  </div>
  @endforeach
</div>

{!! $images->appends(Request::except('page'))->links() !!}


<script>
  // var searchSuggestions = ;
  var image_array = [];

  $('#product-search').autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

      response(results.slice(0, 10));
    }
  });
</script>
@endsection
