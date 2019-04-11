@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Product Details</h2>
            </div>
            <div class="pull-right">
                {{--<a class="btn btn-secondary" href="{{ route('home') }}"> Back</a>--}}
            </div>
        </div>
    </div>

    @if (  $isApproved == -1 )
        <div class="alert alert-danger alert-block mt-2">
            <button type="button" class="close" data-d ismiss="alert">×</button>
            <p><strong>Product has been rejected</strong></p>
            <p><strong>Reason : </strong> {{ $rejected_note }}</p>
        </div>
    @endif

    @if ($message = Session::get('rejected'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

    {{--<form action="{{ route('productattribute.update',$id) }}" method="POST" enctype="multipart/form-data">--}}
        {{--@csrf--}}
        {{--@method('PUT')--}}
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Details not found:</strong>
                        <input type="checkbox" disabled class="" name="dnf" value="Details not found"
                                {{ old('dnf') == 'Details not found' ? 'checked'
                                                             : ($dnf == 'Details not found' ? 'checked' : '') }}/>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>ID:</strong>
                        <p>{{$id}}</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <p>{{$name}}</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Short Description:</strong>
                        <p>{{$short_description}}</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Mesaurement{{--/Size--}}</strong>
                        <div style="padding: 10px 0;">
                            <label for="measurement_type"> Measurement :</label>
                            <input disabled id="measurement_type" type="radio" name="measurement_size_type"
                                   value="measurement" {{ old('measurement_size_type') == 'measurement' ? 'checked'
                                                        : ($measurement_size_type == 'measurement' ? 'checked' : '') }} />

                           {{-- <label for="size_type"> Size :</label>
                            <input disabled id="size_type" type="radio" name="measurement_size_type"
                                   value="size" {{ old('measurement_size_type') == 'size' ? 'checked'
                                                        : ($measurement_size_type == 'size' ? 'checked' : '') }} />--}}
                        </div>

                        <div id="measurement_row" class="row" style="display:none;">
                            <div class="col-4">
                                <strong>L</strong>
                                <p>{{$lmeasurement}}</p>
                            </div>
                            <div class="col-4">
                                <strong>H</strong>
                                <p>{{$hmeasurement}}</p>
                            </div>
                            <div class="col-4">
                                <strong>D</strong>
                                <p>{{$dmeasurement}}</p>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                    <strong>Size</strong> : {{$size}}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Composition :</strong>
                        <p>{{ $composition  }}</p>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> SKU :</strong> {{ $sku }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> SKU+Color:</strong>
                        {{ $sku.$color }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="row">


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Made In :</strong> {{ $made_in }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Brand :</strong> {{ \App\Http\Controllers\BrandController::getBrandName($brand)}}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Color :</strong> {{ $color }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Price (in Euro):</strong> {{ $price }}
                    </div>
                </div>

                {{--<div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Euro to Inr (conversion):</strong> {{ $euro_to_inr }}
                    </div>
                </div>--}}

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Price (in INR):</strong> {{ $price_inr }}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Price Special (in INR):</strong> {{ $price_special }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Category : </strong>
                        @for( $i = 0 ; $i < sizeof($categories) - 1 ; $i++)
                            {{ $categories[$i] }}->
                        @endfor
                        {{ $categories[$i] }}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Product Link :</strong>
                        {{ $product_link }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Supplier :</strong>
                        {{ $supplier }}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Supplier Link :</strong>
                        <a href="{{ $supplier_link }}" target="_blank">{{ $supplier_link }}</a>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong> Description Link :</strong>
                        {{ $description_link }}
                    </div>
                </div>

                @if (Auth::user()->hasRole('Admin'))
                  <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="form-group">
                          <strong>Location :</strong>
                          {{ $location }}
                      </div>
                  </div>
                @endif
            </div>
        </div>

	    <?php $i = 0 ?>

        @for(  ; $i < sizeof($images) ; $i++ )

            <div class="col-xs-12 col-sm-6 col-md-3">
                <strong>Image {{ $i+1 }}:</strong>
                <div class="old-image{{$i}}" style="
                @if ($errors->has('image.'.$i))
                        display: none;
                @endif
                        ">
                    <p>
                        <img src="{{$images[$i]->getUrl()}}" class="img-responsive" style="max-width: 200px;" alt="">
                    </p>
                </div>
            </div>

        @endfor


        <div class="col-xs-12 col-sm-12 col-md-12 ">

            <div class="row">

                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="form-group">

                        <a href="{{ route('productattribute.edit',$id) }}">
                            <button type="button" class="btn btn-image">
                                <img src="/images/edit.png" />
                            </button>
                        </a>

                        @if ($has_reference)
                          <span class="badge">Has Reference</span>
                        @endif

                        {{-- @can('supervisor-edit')
                            <form method="POST" action="{{ route('productsupervisor.approve',$id) }}"
                                  style="display: inline;">
                                @csrf
                                <button data-id="{{ $id }}"
                                        class="btn btn-approve btn-secondary {{ ( $isApproved  ==  '1' ) ? 'btn-success' : ''  }} ">
                                    {{ ( $isApproved  ==  '1' ) ? 'Approved' : 'Approve'  }}
                                </button>
                            </form>
                            <button type="button"
                                    class="btn btn-reject btn-success {{ ( $isApproved  ==  '-1' ) ? '' : 'btn-danger'  }} ">
                                {{ ( $isApproved  ==  '-1' ) ? 'Rejected' : 'Reject'  }}
                            </button>
                            <script> jQuery(document).ready(() => {
                                    attachRejectEvent()
                                }); </script>
                        @endcan --}}
                    </div>
                    <div class="form-group">
                        @can('lister-edit')
                            <form method="POST" action="{{ route('productlister.isuploaded',$id) }}"
                                  style="display: inline;">
                                @csrf
                                <button type="submit" data-id="{{ $id }}"
                                        class="btn btn-secondary {{ ( $isUploaded  ==  '1' ) ? 'btn-success' : ''  }} ">
                                    {{ ( $isUploaded  ==  '1' ) ? 'Uploaded' : 'Upload'  }}
                                </button>
                            </form>
                        @endcan
                        @can('approver-edit')
                            <form method="POST" action="{{ route('productapprover.isfinal',$id) }}"
                                  style="display: inline;">
                                @csrf
                                <button type="submit" data-id="{{ $id }}"
                                        class="btn {{ ( $isFinal  ==  '1' ) ? 'btn-success' : 'btn-secondary'  }} ">
                                    {{ ( $isFinal  ==  '1' ) ? 'Final Approved' : 'Final Approve'  }}
                                </button>
                            </form>
                        @endcan
                    </div>
                    {{-- <div class="form-group">
                        @can('supervisor-edit')
                            <form method="POST" action="{{ route('productsupervisor.reject',$id) }}">
                                @csrf
                                <div class="row" id="rejectWhom" style="
                                @if( !$errors->has('reason') && !$errors->has('role') && empty($reason ))
                                display: none;
                                @endif
                                ">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                                        <div class="form-group">
                                            <strong>Select which role to pass</strong>
                                            {!! Form::select('role', ['Selectors'=>'Selectors',
                                                                     'Searchers' => 'Searchers',
                                                                     'Attribute' => 'Attribute',
                                                                     'ImageCropers' => 'ImageCropers'
                                                                     ]
                                                                     ,old('role'),
                                                                      ['class' => 'form-control'])
                                            !!}
                                            @if($errors->has('role'))
                                                <div class="alert alert-danger">{{ $errors->first('role') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <strong>Reason :</strong>
                                            <textarea type="text" name="reason" class="form-control"
                                                      placeholder="Reason">{{ old('reason') ? old('reason') : $reason }}</textarea>
                                            @if($errors->has('reason'))
                                                <div class="alert alert-danger">{{ $errors->first('reason') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            {{--<input type="text" hidden name="stage" value="2">
                                            <button type="submit" class="btn btn-secondary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endcan
                    </div> --}}
                    <div class="form-group">
                        @can('inventory-edit')
                            <form method="POST" action="{{ route('productinventory.stock',$id) }}"
                                  style="display: inline;">
                                @csrf
                                <div class="form-group">
                                    <strong>Stock</strong>
                                    <input type="number" class="form-control" name="stock" value="{{ old('stock') ? old('stock') : $stock }}" />
                                </div>
                                @if($errors->has('stock'))
                                    <div class="alert alert-danger">{{$errors->first('stock')}}</div>
                                @endif
                                <button type="submit" class="btn btn-secondary">+</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--</form>--}}


@endsection
