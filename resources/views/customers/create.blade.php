@extends('layouts.app')
@section('content')
	{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> --}}
	<div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add New Customer</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('customer.index') }}"> Back</a>

            </div>
        </div>
    </div>
       @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
   		 @endif
     {{--   @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
--}}
     <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
         <div class="row">
             <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                <div class="form-group">
                    <strong>Client Name:</strong>
                    <input type="text" class="form-control" name="name" placeholder="Client Name" value="{{old('name')}}" required/>
                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>
            </div>

              <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                <div class="form-group">
                    <strong>Email:</strong>
                    <input type="email" class="form-control" name="email" placeholder="example@example.com" value="{{old('email')}}"/>
                    @if ($errors->has('email'))
                        <div class="alert alert-danger">{{$errors->first('email')}}</div>
                    @endif
                </div>
            </div>

						<div class="col-xs-12 col-sm-8 col-sm-offset-2">
							<div class="form-group">
									<strong>Phone:</strong>
									<input type="number" class="form-control" name="phone" placeholder="900000000" value="{{old('phone')}}"/>
									@if ($errors->has('phone'))
											<div class="alert alert-danger">{{$errors->first('phone')}}</div>
									@endif
							</div>
					</div>



             <div class="col-xs-12 col-sm-8 col-sm-offset-2 text-center">

                <button type="submit" class="btn btn-secondary">+</button>
            </div>
        </div>
    </form>

		{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>

        $('#created_at').datetimepicker({
          format: 'YYYY-MM-DD HH:mm'
        });

		</script> --}}

@endsection
