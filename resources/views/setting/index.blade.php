@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

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

            @include('partials.flash_messages')

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

                                <hr>

                                <div class="form-group">
                                    <input type="checkbox" name="incoming_calls_yogesh" id="incoming_calls_yogesh" {{ $incoming_calls_yogesh ? 'checked' : '' }} />
                                    <label for="incoming_calls_yogesh">Incoming Calls for Yogesh:</label>
                                    @if ($errors->has('incoming_calls_yogesh'))
                                        <div class="alert alert-danger">{{$errors->first('incoming_calls_yogesh')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <input type="checkbox" name="incoming_calls_andy" id="incoming_calls_andy" {{ $incoming_calls_andy ? 'checked' : '' }} />
                                    <label for="incoming_calls_andy">Incoming Calls for Andy:</label>
                                    @if ($errors->has('incoming_calls_andy'))
                                        <div class="alert alert-danger">{{$errors->first('incoming_calls_andy')}}</div>
                                    @endif
                                </div>

                                <hr>

                                <div class="form-group">
                                    <strong>User for Image Shortcut:</strong>
                                    <select class="selectpicker form-control" data-live-search="true" data-size="15" name="image_shortcut" title="Choose a User" required>
                                        @foreach ($users_array as $index => $user)
                                            <option data-tokens="{{ $index }} {{ $user }}" value="{{ $index }}" {{ $index == $image_shortcut ? 'selected' : '' }}>{{ $user }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('image_shortcut'))
                                        <div class="alert alert-danger">{{$errors->first('image_shortcut')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <strong>User for Price Shortcut:</strong>
                                    <select class="selectpicker form-control" data-live-search="true" data-size="15" name="price_shortcut" title="Choose a User" required>
                                        @foreach ($users_array as $index => $user)
                                            <option data-tokens="{{ $index }} {{ $user }}" value="{{ $index }}" {{ $index == $price_shortcut ? 'selected' : '' }}>{{ $user }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('price_shortcut'))
                                        <div class="alert alert-danger">{{$errors->first('price_shortcut')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <strong>User for Call Shortcut:</strong>
                                    <select class="selectpicker form-control" data-live-search="true" data-size="15" name="call_shortcut" title="Choose a User" required>
                                        @foreach ($users_array as $index => $user)
                                            <option data-tokens="{{ $index }} {{ $user }}" value="{{ $index }}" {{ $index == $call_shortcut ? 'selected' : '' }}>{{ $user }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('call_shortcut'))
                                        <div class="alert alert-danger">{{$errors->first('call_shortcut')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <strong>User for Attach Image Physically Shortcut:</strong>
                                    <select class="selectpicker form-control" data-live-search="true" data-size="15" name="screenshot_shortcut" title="Choose a User" required>
                                        @foreach ($users_array as $index => $user)
                                            <option data-tokens="{{ $index }} {{ $user }}" value="{{ $index }}" {{ $index == $screenshot_shortcut ? 'selected' : '' }}>{{ $user }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('screenshot_shortcut'))
                                        <div class="alert alert-danger">{{$errors->first('screenshot_shortcut')}}</div>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <strong>User for Give Details Shortcut:</strong>
                                    <select class="selectpicker form-control" data-live-search="true" data-size="15" name="details_shortcut" title="Choose a User" required>
                                        @foreach ($users_array as $index => $user)
                                            <option data-tokens="{{ $index }} {{ $user }}" value="{{ $index }}" {{ $index == $details_shortcut ? 'selected' : '' }}>{{ $user }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('details_shortcut'))
                                        <div class="alert alert-danger">{{$errors->first('details_shortcut')}}</div>
                                    @endif
                                </div>

                                <hr>

                                <div class="form-group">
                                  <strong>Consignor Name:</strong>
                                  <input type="text" class="form-control" name="consignor_name" value="{{ $consignor_name }}" required>

                                  @if ($errors->has('consignor_name'))
                                    <div class="alert alert-danger">{{$errors->first('consignor_name')}}</div>
                                  @endif
                                </div>

                                <div class="form-group">
                                  <strong>Consignor Address:</strong>
                                  <input type="text" class="form-control" name="consignor_address" value="{{ $consignor_address }}" required>

                                  @if ($errors->has('consignor_address'))
                                    <div class="alert alert-danger">{{$errors->first('consignor_address')}}</div>
                                  @endif
                                </div>

                                <div class="form-group">
                                  <strong>Consignor City:</strong>
                                  <input type="text" class="form-control" name="consignor_city" value="{{ $consignor_city }}" required>

                                  @if ($errors->has('consignor_city'))
                                    <div class="alert alert-danger">{{$errors->first('consignor_city')}}</div>
                                  @endif
                                </div>

                                <div class="form-group">
                                  <strong>Consignor Country:</strong>
                                  <input type="text" class="form-control" name="consignor_country" value="{{ $consignor_country }}" required>

                                  @if ($errors->has('consignor_country'))
                                    <div class="alert alert-danger">{{$errors->first('consignor_country')}}</div>
                                  @endif
                                </div>

                                <div class="form-group">
                                  <strong>Consignor Phone:</strong>
                                  <input type="text" class="form-control" name="consignor_phone" value="{{ $consignor_phone }}" required>

                                  @if ($errors->has('consignor_phone'))
                                    <div class="alert alert-danger">{{$errors->first('consignor_phone')}}</div>
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

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
@endsection
