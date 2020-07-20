@extends('layouts.app')

@section('title', 'Plesk mail accounts')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Mail accounts</h2>
          </div>
          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary add-new-btn" href="{{ route('plesk.domains') }}">Back</a>
            </div>
        </div>
    </div>
    @include('partials.flash_messages')
    
	</br> 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Name</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($mailAccount as $key => $account)
            <tr>
            <td>{{$account['name']}}</td>
            </tr>
            @endforeach
        </tbody>
      </table>

	</div>
    </div>
   
@endsection
