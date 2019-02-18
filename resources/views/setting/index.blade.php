@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Settings</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post" action="{{ route('settings.store') }}" class="form-horizontal" role="form">
                {!! csrf_field() !!}

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="glyphicon glyphicon-flash"></i>
                        <strong></strong>
                    </div>

                    <div class="panel-body">
                        {{--<div class="row">
                            <div class="col-md-7 col-md-offset-2">
                                <div class="form-group">
                                    <strong> Euro to inr conversion:</strong>
                                    <input type="text" class="form-control" name="euro_to_inr" placeholder="Eur to inr" value="{{ old('euro_to_inr') ? old('euro_to_inr') : $euro_to_inr }}"/>
                                    @if ($errors->has('euro_to_inr'))
                                        <div class="alert alert-danger">{{$errors->first('euro_to_inr')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 col-md-offset-2">
                                <div class="form-group">
                                    <strong> Special Price Discount (%):</strong>
                                    <input type="number" class="form-control" name="special_price_discount" placeholder="Special Price Discount " value="{{ old('special_price_discount') ? old('special_price_discount') : $special_price_discount }}"/>
                                    @if ($errors->has('special_price_discount'))
                                        <div class="alert alert-danger">{{$errors->first('special_price_discount')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>--}}
                        <div class="row">
                            <div class="col-md-7 col-md-offset-2">
                                <div class="form-group">
                                    <strong> Pagination:</strong>
                                    <input type="number" class="form-control" name="pagination" placeholder="Number of products per pages" value="{{ old('pagination') ? old('pagination') : $pagination }}"/>
                                    @if ($errors->has('pagination'))
                                        <div class="alert alert-danger">{{$errors->first('pagination')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <input type="checkbox" name="incoming_calls" id="incoming_calls" {{ $incoming_calls ? 'checked' : '' }} />
                                    <label for="incoming_calls">Incoming Calls for Yogesh:</label>
                                    @if ($errors->has('incoming_calls'))
                                        <div class="alert alert-danger">{{$errors->first('incoming_calls')}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row m-b-md">
                    <div class="col-md-12">
                        <button class="btn-secondary btn">
                            Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
