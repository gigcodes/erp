@extends('layouts.app')


@section('content')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">

<div class="row">
  <div class="col-lg-10 margin-tb">
      <h2>Image Edit</h2>
  </div>

  <div class="col-lg-2">
    <a href="{{ route('image.grid') }}" class="btn btn-secondary">Back</a>
  </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
  {{ $message }}
</div>
@endif

<div class="row">
  <div class="col-md-6">
    <img src="{{ asset('uploads/social-media') . '/' . $image->filename }}" class="img-responsive" alt="">

    <form class="mt-3" action="{{ route('image.grid.update', $image->id) }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
         <input type="file" name="image" />
         @if ($errors->has('image'))
             <div class="alert alert-danger">{{$errors->first('image')}}</div>
         @endif
      </div>

      <div class="form-group">
        <strong>Publish Date:</strong>
        <div class='input-group date' id='publish-date'>
          <input type='text' class="form-control" name="publish_date" value="{{ isset($image->publish_date) ? $image->publish_date : date('Y-m-d H:i') }}" />

          <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>

        @if ($errors->has('publish_date'))
            <div class="alert alert-danger">{{$errors->first('publish_date')}}</div>
        @endif
      </div>

      <div class="form-group">
        <strong>Tags</strong> <br>
        <select multiple data-role="tagsinput" name="tags[]" id="tags">
          @foreach ($image->tags as $tag)
            <option value="{{ $tag->tag }}">{{ $tag->tag }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-secondary">Update</button>
    </form>
  </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script type="text/javascript">
  $('#publish-date').datetimepicker({
    format: 'YYYY-MM-DD HH:mm'
  });

  $('#tags').tagsinput({
    tagClass: 'label label-default'
  });
</script>

@endsection
