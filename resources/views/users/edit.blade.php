@extends('layouts.app')


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


    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


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

              @foreach ($api_keys as $api_key)
                <option value="{{ $api_key->number }}" {{ $user->whatsapp_number == $api_key->number ? 'selected' : '' }}>{{ $api_key->number }}</option>
              @endforeach
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
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-secondary">+</button>
        </div>
    </div>
    {!! Form::close() !!}


@endsection
