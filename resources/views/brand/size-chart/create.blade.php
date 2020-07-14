@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Create Brand Size Chart</h2>
            </div>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('brand/store/size/chart')  }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Brand</strong>
                            <select name="brand_id" class="form-control select2" id="brand_id" required>
                                <option value="">Select Brand</option>
                                @forelse ($brands as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                            @if ($errors->has('brand_id'))
                                <div class="alert alert-danger">{{$errors->first('brand_id')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Upload Size Chart</strong>
                            <input type="file" class="form-control" name="size_img" required/>
                            @if ($errors->has('size_img'))
                                <div class="alert alert-danger">{{$errors->first('size_img')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(".select2").select2();
</script>
@endsection