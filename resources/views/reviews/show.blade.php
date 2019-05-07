@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

  <div class="row">
        <div class="col-lg-12">
          @include('partials.flash_messages')
        </div>
        <div class="col-lg-12 margin-tb">

          <h2 class="page-heading">Account: {{ $account->first_name }} {{ $account->last_name }}</h2>

          <form action="{{ action('AccountController@sendMessage', $account->id) }}" method="post">
            @csrf
            <div class="form-group">
              <label for="username">Username</label>
              <input class="form-control" type="text" id="username" name="username">
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <input class="form-control" type="text" name="message" id="message">
            </div>
            <div class="form-group">
              <button class="btn-info btn">Send Message</button>
            </div>
          </form>

        </div>
    </div>


@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
@endsection
