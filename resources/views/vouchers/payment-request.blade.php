@extends('layouts.app')
@section('title', 'Payment Request Form')
@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Payment request</h2>
            <div class="pull-right">
              <a class="btn btn-secondary" href="{{ route('voucher.index') }}">Back</a>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')


    <div class="row">
      <div class="col-xs-12 col-md-8 col-md-offset-2">
        {!! Form::open(array('route' => 'voucher.payment.request-submit','method'=>'POST')) !!}



        <div class="form-group">
            <strong>User:</strong>
            <select class="form-control select-multiple" name="user_id" id="user-select">
                <option value="">Select User</option>
                @foreach($users as $key => $user)
                <option value="{{ $user->id }}" {{$user->id == Auth::user()->id ? 'selected' : ''}}>{{ $user->name }}</option>
                @endforeach
            </select>
            @if ($errors->has('user_id'))
              <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
            @endif
          </div>


        <div class="form-group">
          <strong>Date :</strong>
          <div class='input-group date' id='date_of_request'>
            <input type='text' class="form-control" name="date" value="{{ date('Y-m-d') }}" />

            <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>
<br>
          <div class="form-group">
            <strong>Time spent (In minutes):</strong>
            <input type="number" name="worked_minutes" class="form-control" value="{{ old('worked_minutes') }}">

            @if ($errors->has('worked_minutes'))
              <div class="alert alert-danger">{{$errors->first('worked_minutes')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Amount:</strong>
            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required>

            @if ($errors->has('amount'))
              <div class="alert alert-danger">{{$errors->first('amount')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Currency:</strong>
            <input type="text" name="currency" class="form-control" value="{{ old('currency') }}" required>

            @if ($errors->has('currency'))
              <div class="alert alert-danger">{{$errors->first('currency')}}</div>
            @endif
          </div>


          @if ($errors->has('date'))
              <div class="alert alert-danger">{{$errors->first('date')}}</div>
          @endif
        </div>

        <div class="form-group">
          <strong>Details:</strong>
          <textarea name="remarks" rows="4" cols="80" class="form-control">{{ old('remarks') }}</textarea>

          @if ($errors->has('remarks'))
              <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
          @endif
        </div>

        <button type="submit" class="btn btn-secondary">Create</button>

        {!! Form::close() !!}
      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>

  <script>
    $('#date_of_request').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    $('.select-multiple').select2({width: '100%'});
  </script>
@endsection
