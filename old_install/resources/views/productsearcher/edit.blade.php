@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Searcher</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('productsearcher.index') }}"> Back</a>
            </div>
        </div>
    </div>

    <form action="{{ route('productsearcher.update',$id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Details not found:</strong>
                    <input type="checkbox" class="form-control" name="dnf" value="Details not found"
                            {{ old('dnf') == 'Details not found' ? 'checked'
                                                         : ($dnf == 'Details not found' ? 'checked' : '') }}/>
                    @if ($errors->has('dnf'))
                        <div class="alert alert-danger">{{$errors->first('dnf')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{old('name') ? old('name') : $name}}"/>
                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Short Description:</strong>

                    <input type="text" class="form-control" name="short_description" placeholder="Short Description"
                           value="{{ old('short_description') ? old('short_description') : $short_description }}"/>

                    @if ($errors->has('short_description'))
                        <div class="alert alert-danger">{{$errors->first('short_description')}}</div>
                    @endif
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Mesaurement/Size</strong>
                    <div style="padding: 10px 0;">
                        <label for="measurement_type"> Measurement :</label>
                        <input id="measurement_type" type="radio" name="measurement_size_type"
                               value="measurement" {{ old('measurement_size_type') == 'measurement' ? 'checked'
                                                        : ($measurement_size_type == 'measurement' ? 'checked' : '') }} />

                        <label for="size_type"> Size :</label>
                        <input id="size_type" type="radio" name="measurement_size_type"
                               value="size" {{ old('measurement_size_type') == 'size' ? 'checked'
                                                        : ($measurement_size_type == 'size' ? 'checked' : '') }} />
                    </div>

                    <div id="measurement_row" class="row" style="display:none;">
                        <div class="col-4">
                            <input type="text" class="form-control" name="lmeasurement" placeholder="L" value="{{ old('lmeasurement') ? old('lmeasurement') : $lmeasurement }}"/>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="hmeasurement" placeholder="H" value="{{ old('hmeasurement') ? old('hmeasurement') : $hmeasurement }}"/>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" name="dmeasurement" placeholder="D" value="{{ old('dmeasurement') ? old('dmeasurement') : $dmeasurement }}"/>
                        </div>
                    </div>

                    <div id="size_row" class="" style="display:none;">
                        <select class="form-control" name="size_value">
                            <option value="" disabled selected>select</option>
                        @foreach( $sizes_array as $size )
                            <option value="{{ $size }}" {{ $size == $size_value ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                        </select>
                    </div>

                    @if ($errors->any())
                        <div style="padding-top: 10px;">
                            @if ($errors->has('measurement_size_type'))
                                <div class="alert alert-danger">{{$errors->first('measurement_size_type')}}</div>
                            @endif

                            @if ($errors->has('lmeasurement'))
                                <div class="alert alert-danger">{{$errors->first('lmeasurement')}}</div>
                            @endif
                            @if ($errors->has('hmeasurement'))
                                <div class="alert alert-danger">{{$errors->first('hmeasurement')}}</div>
                            @endif
                            @if ($errors->has('dmeasurement'))
                                <div class="alert alert-danger">{{$errors->first('dmeasurement')}}</div>
                            @endif
                            @if ($errors->has('size_value'))
                                <div class="alert alert-danger">{{$errors->first('size_value')}}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Composition :</strong>
                    <input type="text" class="form-control" name="composition" placeholder="Composition" value="{{ old('composition') ? old('composition') : $composition }}"/>
                    @if ($errors->has('composition'))
                        <div class="alert alert-danger">{{$errors->first('composition')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> SKU :</strong>
                    <input type="text" class="form-control" name="sku" placeholder="SKU" value="{{ old('sku') ? old('sku') : $sku }}"/>
                    @if ($errors->has('sku'))
                        <div class="alert alert-danger">{{$errors->first('sku')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Made In :</strong>
                    <input type="text" class="form-control" name="made_in" placeholder="Made In" value="{{ old('made_in') ? old('made_in') : $made_in }}"/>
                    @if ($errors->has('made_in'))
                        <div class="alert alert-danger">{{$errors->first('made_in')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Brand :</strong>
                    <input type="text" class="form-control" name="brand" placeholder="Brand" value="{{ old('brand') ? old('brand') : $brand }}"/>
                    @if ($errors->has('brand'))
                        <div class="alert alert-danger">{{$errors->first('brand')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Color :</strong>
                    <input type="text" class="form-control" name="color" placeholder="Color" value="{{ old('color') ? old('color') : $color }}"/>
                    @if ($errors->has('color'))
                        <div class="alert alert-danger">{{$errors->first('color')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong> Price (in Euro):</strong>
                    <input type="number" class="form-control" name="price" placeholder="Color" value="{{ old('price') ? old('price') : $price }}"/>
                    @if ($errors->has('price'))
                        <div class="alert alert-danger">{{$errors->first('price')}}</div>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <label for="men_women" >Men/Women</label>
                            <select id="men_women" class="form-control" name="men_women">
                                <option value="" disabled selected>select</option>
                                <option value="men">Men</option>
                                <option value="women">Women</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <label for="categories" >Categories</label>
                            <select id="categories" class="form-control" name="categories">
                                <option value="" disabled selected>select</option>
                                @foreach( $sizes_array as $size )
                                    <option value="{{ $size }}" {{ $size == $size_value ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <label for="sub_categories" >Sub-Category</label>
                            <select id="sub_categories" class="form-control" name="sub_categories">
                                <option value="" disabled selected>select</option>
                                @foreach( $sizes_array as $size )
                                    <option value="{{ $size }}" {{ $size == $size_value ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php $i = 0 ?>

            @for(  ; $i < sizeof($images) ; $i++ )

            <div class="col-xs-12 col-sm-12 col-md-12">
                <strong>Image {{ $i+1 }}:</strong>
                <div class="old-image{{$i}}" style="
                @if ($errors->has('image.'.$i))
                        display: none;
                @endif
                        ">
                    <p>
                        <img src="{{$images[$i]->getUrl()}}" class="img-responsive" style="max-width: 200px;"  alt="">
                        <input type="text" hidden name="oldImage{{$i}}" value="0">
                    </p>
                    <button class="btn btn-primary removeOldImage" data-id="{{$i}}" media-id="{{ $images[$i]->id }}">Remove</button>
                </div>
                <div class="form-group new-image{{ $i }}" style="
                @if ( !$errors->has('image.'.$i))
                        display: none;
                @endif
                        ">
                    <strong>Upload Image:</strong>
                    <input  type="file" enctype="multipart/form-data" class="form-control" name="image[]" />
                    @if ($errors->has( 'image.'.$i ))
                        <div class="alert alert-danger">{{$errors->first('image.'.$i )}}</div>
                    @endif
                </div>
            </div>

            @endfor

            @for( ;  $i < 5 ; $i++  )
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <strong>Image {{ $i+1  }}:</strong>

                    <div class="form-group new-image">
                        <strong>Upload Image:</strong>
                        <input  type="file" enctype="multipart/form-data" class="form-control" name="image[]" />
                        @if ($errors->has('image.'.$i))
                            <div class="alert alert-danger">{{$errors->first( 'image.'.($i) )}}</div>
                        @endif
                    </div>
                </div>
            @endfor

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <input type="text" hidden name="stage" value="2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>

        </div>
    </form>


@endsection