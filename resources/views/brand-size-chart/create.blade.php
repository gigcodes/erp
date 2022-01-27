@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-md-12 ">
            
                <h2 class="page-heading">Create Brand Size Chart</h2>
           
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('brand/store/size/chart')  }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="d-flex" style="justify-content: space-between;">
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group pl-5" style="width: 200px;">
                            {!!
                                (new \App\Category)->attr([
                                    'name' => 'category_id', 
                                    'class' => 'form-control select2',
                                    'required' => true,
                                    'data-placeholder' => 'Select Category'
                                ])->selected(request('category'))->renderAsDropdown()
                            !!}

                            @if ($errors->has('category_id'))
                                <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <select name="brand_id" class="form-control select2" id="brand_id"placeholder="Brand"  style="width: 200px;">
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
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <select name="store_website_id"  style="width: 200px;" placeholder="Store website" class="form-control select2" id="brand_id" required>
                                <option value="">Select Website</option>
                                @forelse ($storeWebsite as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                            @if ($errors->has('store_website_id'))
                                <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <input type="file" placeholder="Upload Size Chart" class="form-control" name="size_img"  style="width: 200px;" required/>
                            @if ($errors->has('size_img'))
                                <div class="alert alert-danger">{{$errors->first('size_img')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3 text-center">
                        <a href="{{ route('brand/size/chart') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
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
@endsection>Create Brand Size Chart</h2>
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
                            <strong>Category</strong>
                            {!!
                                (new \App\Category)->attr([
                                    'name' => 'category_id', 
                                    'class' => 'form-control select2',
                                    'required' => true,
                                    'data-placeholder' => 'Select Category'
                                ])->selected(request('category'))->renderAsDropdown()
                            !!}

                            @if ($errors->has('category_id'))
                                <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Brand</strong>
                            <select name="brand_id" class="form-control select2" id="brand_id">
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
                            <strong>Store website</strong>
                            <select name="store_website_id" class="form-control select2" id="brand_id" required>
                                <option value="">Select Website</option>
                                @forelse ($storeWebsite as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                            @if ($errors->has('store_website_id'))
                                <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
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
                        <a href="{{ route('brand/size/chart') }}" class="btn btn-default">Cancel</a>
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