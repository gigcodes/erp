@extends('layouts.app')


@section('content')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

<div class="row">
  <div class="col-12 margin-tb mb-3">
    <h2 class="page-heading">Image Grid</h2>

    <form action="{{ route('image.grid') }}" method="GET" class="form-inline align-items-start">
      <div class="form-group mr-3 mb-3">
        {!! $category_selection !!}
      </div>

      <div class="form-group mr-3">
        <select class="form-control select-multiple" name="brand[]" multiple>
          <optgroup label="Brands">
            @foreach ($brands as $key => $name)
              <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </optgroup>
        </select>
      </div>

      <div class="form-group mr-3">
        <strong class="mr-3">Price</strong>
        <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>
      </div>

      <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
    </form>

    {{-- <strong>Sort By</strong>
    <a href="{{ route('image.grid') . '?sortby=asc' }}" class="btn-link">ASC</a>
     |
    <a href="{{ route('image.grid') . '?sortby=desc' }}" class="btn-link">DESC</a> --}}
  {{-- </div>
  <div class="col-lg-2 mt-4"> --}}
  @can('social-create')
    <div class="pull-right btn-group">
      <a href="{{ route('attachImages', ['images']) }}" class="btn btn-secondary">Attach Images</a>
      <a href class="btn btn-secondary" data-toggle="modal" data-target="#imageModal">Upload</a>
    </div>

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
            <input type="hidden" name="status" value="1">

            <div class="modal-body">
              <div class="form-group">
                   <input type="file" name="images[]" multiple required />
                   @if ($errors->has('images'))
                       <div class="alert alert-danger">{{$errors->first('images')}}</div>
                   @endif
              </div>

              <div class="form-check">
                <input class="" type="radio" name="lifestyle" id="exampleRadios1" value="0" checked>
                <label class="form-check-label" for="exampleRadios1">
                  Default
                </label>
              </div>

              <div class="form-check">
                <input class="" type="radio" name="lifestyle" id="exampleRadios2" value="1">
                <label class="form-check-label" for="exampleRadios2">
                  Lifestyle
                </label>
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
  @endcan
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
    <img src="{{ $image->filename ? (asset('uploads/social-media') . '/' . $image->filename) : ($image->getMedia(config('constants.media_tags'))->first() ? $image->getMedia(config('constants.media_tags'))->first()->getUrl() : '') }}" class="img-responsive grid-image" alt="" />

    <a class="btn btn-image" href="{{ route('image.grid.show',$image->id) }}"><img src="/images/view.png" /></a>
    @can ('social-create')
      <a class="btn btn-image" href="{{ route('image.grid.edit',$image->id) }}"><img src="/images/edit.png" /></a>

      {!! Form::open(['method' => 'DELETE','route' => ['image.grid.delete', $image->id],'style'=>'display:inline']) !!}
        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
      {!! Form::close() !!}
    @endcan

    @if (isset($image->approved_user))
      <span>Approved by {{ App\User::find($image->approved_user)->name}} on {{ Carbon\Carbon::parse($image->approved_date)->format('d-m') }}</span>
    @else
      @can ('social-manage')
        {{-- <form action="{{ route('image.grid.approveImage', $image->id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-xs btn-secondary">Approve</button>
        </form> --}}
        <button type="button" class="btn btn-xs btn-secondary approve-image" data-id="{{ $image->id }}">Approve</button>
      @endcan
    @endif
  </div>
  @endforeach
</div>

{!! $images->appends(Request::except('page'))->links() !!}

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
     $(".select-multiple").multiselect();
  });

  $(document).on('click', '.approve-image', function() {
    var id = $(this).data('id');
    var thiss = $(this);

    $.ajax({
      type: "POST",
      url: "{{ url('images/grid') }}/" + id + "/approveImage",
      data: {
        _token: "{{ csrf_token() }}"
      },
      beforeSend: function() {
        $(thiss).text('Approving');
      }
    }).done(function() {
      $(thiss).parent('div').remove();
    }).fail(function(response) {
      console.log(response);
      alert('Error while approving image');
    });
  });
</script>
{{-- <script>
  // var searchSuggestions = ;
  var image_array = [];

  $('#product-search').autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

      response(results.slice(0, 10));
    }
  });
</script> --}}
@endsection
