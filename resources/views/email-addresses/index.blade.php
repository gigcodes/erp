@extends('layouts.app')

@section('title', 'Email Addresses List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Email Addresses List</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#emailAddressModal">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>From Name</th>
            <th>From Address</th>
            <th>Driver</th>
            <th>Host</th>
            <th>Port</th>
            <th>Encryption</th>
            <th>Store Website</th>
            <!--th>Username</th-->
            <!--th>Password</th-->
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($emailAddress as $server)
            <tr>
              <td>{{ $server->id }}</td>
              <td>
                  {{ $server->from_name }}
              </td>
              <td>
                  {{ $server->from_address }}
              </td>
              <td>
                  {{ $server->driver }}
              </td>
              <td>
                  {{ $server->host }}
              </td>
              <td>
                  {{ $server->port }}
              </td>
              <td>
                  {{ $server->encryption }}
              </td>
              <!--td>
                  {{ $server->username }}
              </td-->
              <!--td>
                  {{ $server->password }}
              </td-->
			  <td>
                  @if($server->website){{ $server->website->title }} @endif
              </td>
              <td>
                  <button type="button" class="btn btn-image edit-email-addresses d-inline" data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($server) }}"><img src="/images/edit.png" /></button>
                  <button type="button" class="btn btn-image view-email-history d-inline" data-id="{{ $server->id }}"><img width="2px;" src="/images/view.png"/></button>
                  {!! Form::open(['method' => 'DELETE','route' => ['email-addresses.destroy', $server->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $emailAddress->appends(Request::except('page'))->links() !!}

<div id="emailAddressModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('email-addresses.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>From Name:</strong>
            <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

            @if ($errors->has('from_name'))
              <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>From Address:</strong>
            <input type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>

            @if ($errors->has('from_address'))
              <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
            @endif
          </div>
			<div class="form-group">
            	<strong>Store Website:</strong>
				<Select name="store_website_id" class="form-control">
					<option value>None</option>
					@foreach ($allStores as $key => $val)
						<option value="{{ $val->id }}">{{ $val->title }}</option>
					@endforeach
				</Select>
				@if ($errors->has('store_website_id'))
						<div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
				@endif
			</div>
          <div class="form-group">
            <strong>Driver:</strong>
            <input type="text" name="driver" class="form-control" value="{{ old('driver') }}" required>

            @if ($errors->has('driver'))
              <div class="alert alert-danger">{{$errors->first('driver')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Host:</strong>
            <input type="text" name="host" class="form-control" value="{{ old('host') }}" required>

            @if ($errors->has('host'))
              <div class="alert alert-danger">{{$errors->first('host')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Port:</strong>
            <input type="text" name="port" class="form-control" value="{{ old('port') }}" required>

            @if ($errors->has('port'))
              <div class="alert alert-danger">{{$errors->first('port')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Encryption:</strong>
            <input type="text" name="encryption" class="form-control" value="{{ old('encryption') }}" required>

            @if ($errors->has('encryption'))
              <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Username:</strong>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

            @if ($errors->has('username'))
              <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="EmailRunHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Run History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>From Name</th>
                <th>Status</th>
                <th>Message</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
          </table>
        </div>
      </div>    
			</div>
		</div>
	</div>
</div>

<div id="emailAddressEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>From Name:</strong>
            <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

            @if ($errors->has('from_name'))
              <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>From Address:</strong>
            <input type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>

            @if ($errors->has('from_address'))
              <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
            @endif
          </div>
		  <div class="form-group">
            	<strong>Store Website:</strong>
				<Select name="store_website_id" id="edit_store_website_id" class="form-control">
					<option value = ''>None</option>
					@foreach ($allStores as $key => $val)
						<option value="{{ $val->id }}">{{ $val->title }}</option>
					@endforeach
				</Select>
				@if ($errors->has('store_website_id'))
						<div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
				@endif
			</div>
          <div class="form-group">
            <strong>Driver:</strong>
            <input type="text" name="driver" class="form-control" value="{{ old('driver') }}" required>

            @if ($errors->has('driver'))
              <div class="alert alert-danger">{{$errors->first('driver')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Host:</strong>
            <input type="text" name="host" class="form-control" value="{{ old('host') }}" required>

            @if ($errors->has('host'))
              <div class="alert alert-danger">{{$errors->first('host')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Port:</strong>
            <input type="text" name="port" class="form-control" value="{{ old('port') }}" required>

            @if ($errors->has('port'))
              <div class="alert alert-danger">{{$errors->first('port')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Encryption:</strong>
            <input type="text" name="encryption" class="form-control" value="{{ old('encryption') }}" required>

            @if ($errors->has('encryption'))
              <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Username:</strong>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

            @if ($errors->has('username'))
              <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>

@endsection

@section('scripts')
  
  <script type="text/javascript">
    $(document).on('click', '.edit-email-addresses', function() {
      var emailAddress = $(this).data('email-addresses');
      var url = "{{ route('email-addresses.index') }}/" + emailAddress.id;

      $('#emailAddressEditModal form').attr('action', url);
      $('#emailAddressEditModal').find('input[name="from_name"]').val(emailAddress.from_name);
      $('#emailAddressEditModal').find('input[name="from_address"]').val(emailAddress.from_address);
      $('#emailAddressEditModal').find('input[name="driver"]').val(emailAddress.driver);
      $('#emailAddressEditModal').find('input[name="host"]').val(emailAddress.host);
      $('#emailAddressEditModal').find('input[name="port"]').val(emailAddress.port);
      $('#emailAddressEditModal').find('input[name="encryption"]').val(emailAddress.encryption);
      $('#emailAddressEditModal').find('input[name="username"]').val(emailAddress.username);
      $('#emailAddressEditModal').find('input[name="password"]').val(emailAddress.password);
	  
	  $('#edit_store_website_id').val(emailAddress.store_website_id).trigger('change');
      
    });

    $(document).on('click', '.view-email-history', function(e) {
        var id = $(this).attr('data-id');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/getemailhistory/'+id,
          dataType: 'json',
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal
          $('#EmailRunHistoryModal tbody').html(response.data);
          $('#EmailRunHistoryModal').modal('show');

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });
  </script>
@endsection
