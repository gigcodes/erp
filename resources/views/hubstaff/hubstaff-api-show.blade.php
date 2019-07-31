@extends('layouts.app')

@section('content')
<h2 class="text-center">Users Api from Hubstaff</h2>
<div class="container">

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
	<div class="row">
		<div class="col-md-5">
			<div class="well">
         {!! Form::open(array('route' => 'post.user.api')) !!}
          <div>
          	<h3 class="text-center">Authorization Credentials</h3>
              <div class="form-group">
                 <input class="form-control" name="app_token" id="email" type="text" placeholder="Your App Token" required>
              </div>
             <div class="form-group">
               <input class="form-control" name="auth_token" id="email" type="text" placeholder="Your Auth Token" required>
             </div>
             <div class="form-group">
                <input class="form-control" name="email" id="email" type="email" placeholder="Your Email" required>
             </div>
             <div class="form-group">
               <input class="form-control" name="email" id="email" type="password" placeholder="Your password" required>
             </div>
            
             <br/>
             <div class="text-center">
             	<button class="btn btn-info btn-lg" type="submit">Get User Details</button>
             </div>
          </div>
         {!! Form::close() !!}
    	 </div>
		</div>
   
</div>
@endsection