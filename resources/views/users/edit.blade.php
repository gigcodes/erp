@extends('layouts.app')

@section('title', 'User Edit Page')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit New User</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('users.index') }}"> Back</a>
            </div>
        </div>
    </div>


    @include('partials.flash_messages')


    {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id]]) !!}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12">
    			<div class="form-group">
    				<strong>Phone:</strong>
    				<input type="number" class="form-control" name="phone" placeholder="900000000" value="{{ $user->phone }}" />
    				@if ($errors->has('phone'))
    				<div class="alert alert-danger">{{$errors->first('phone')}}</div>
    				@endif
    			</div>
    		</div>

        <div class="col-xs-12">
    			<div class="form-group">
    				{{-- <strong>Solo phone:</strong>
    				<Select name="whatsapp_number" class="form-control">
    					<option value>None</option>
    					<option value="919167152579" {{ $user->whatsapp_number == '919167152579' ? 'selected' : '' }}>00</option>
    					<option value="918291920452" {{ $user->whatsapp_number == '918291920452' ? 'selected' : '' }}>02</option>
    					<option value="918291920455" {{ $user->whatsapp_number == '918291920455' ? 'selected' : '' }}>03</option>
    					<option value="919152731483" {{ $user->whatsapp_number == '919152731483' ? 'selected' : '' }}>04</option>
    					<option value="919152731484" {{ $user->whatsapp_number == '919152731484' ? 'selected' : '' }}>05</option>
    					<option value="919152731486" {{ $user->whatsapp_number == '919152731486' ? 'selected' : '' }}>06</option>
    					<option value="918291352520" {{ $user->whatsapp_number == '918291352520' ? 'selected' : '' }}>08</option>
    					<option value="919004008983" {{ $user->whatsapp_number == '919004008983' ? 'selected' : '' }}>09</option>
    				</Select> --}}
            <select name="whatsapp_number" class="form-control" id="whatsapp_change">
              <option value>Whatsapp Number</option>
              <option value="919004780634" {{ '919004780634' == $user->whatsapp_number ? ' selected' : '' }}>919004780634 Indian</option>
              <option value="971545889192" {{ '971545889192' == $user->whatsapp_number ? ' selected' : '' }}>971545889192 Dubai</option>
              {{-- @foreach ($api_keys as $api_key)
                <option value="{{ $api_key->number }}" {{ $user->whatsapp_number == $api_key->number ? 'selected' : '' }}>{{ $api_key->number }}</option>
              @endforeach --}}
            </select>

    				@if ($errors->has('whatsapp_number'))
    						<div class="alert alert-danger">{{$errors->first('whatsapp_number')}}</div>
    				@endif
    			</div>
    		</div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Password:</strong>
                {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Confirm Password:</strong>
                {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Role:</strong>
                {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
            </div>
        </div>

               <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Agent Role:</strong>
                {!! Form::select('agent_role[]', $agent_roles,$user_agent_roles, array('class' => 'form-control','multiple')) !!}
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Responsible User:</strong>
                <select name="responsible_user" class="form-control">
                  <option value="">Select User</option>
                  @foreach($users as $useritem)
                    <option value="{{$useritem->id}}" {{ $useritem->id == $user->responsible_user ? 'selected' : '' }}>{{$useritem->name}}</option>
                  @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="form-group">
            <strong>Amount of Assigned Products:</strong>
            <input type="number" name="amount_assigned" class="form-control" value="{{ $user->amount_assigned }}">
          </div>
        </div>

        @if ($user->hasRole('Customer Care'))
          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
              <strong>Assigned Customers:</strong>
              <select class="selectpicker form-control" data-live-search="true" data-size="15" name="customer[]" title="Choose Customers" multiple>
                @foreach ($customers_all as $customer)
                  <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}" {{ $user->customers && $user->customers->contains($customer['id']) ? 'selected' : '' }}>{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
                @endforeach
              </select>

              @if ($errors->has('customer'))
                <div class="alert alert-danger">{{$errors->first('customer')}}</div>
              @endif
            </div>
          </div>
        @endif

        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-secondary">+</button>
        </div>
    </div>
    {!! Form::close() !!}

    <div class="form-group">
      <form action="{{ route('user.activate', $user->id) }}" method="POST">
        @csrf

        <button type="submit" class="btn btn-secondary">
          @if ($user->is_active == 1)
            Is Active
          @else
            Not Active
          @endif
        </button>
      </form>
    </div>

    {{-- <div class="form-group">
      <form action="{{ route('user.assign.products', $user->id) }}" method="POST">
        @csrf

        <button type="submit" class="btn btn-secondary">Assign Products</button>
      </form>
    </div> --}}


@endsection
