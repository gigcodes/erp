@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('large_content')
     <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
	<div class = "row">
		<div class="col-lg-12 margin-tb">
			<?php $base_url = URL::to('/');?>
			<h2 class="page-heading">Setting Data</h2>
            <div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ url('settings') }}" method="GET">
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Setting ID</label>
                       <input type="text" name="id" class="form-control-sm cls_commu_his form-control" value="{{request()->get('id')}}">
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Setting Name</label>
                       <input type="text" name="name" class="form-control-sm cls_commu_his form-control" value="{{request()->get('name')}}">
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Setting Value</label>
                       <input type="text" name="value" class="form-control-sm cls_commu_his form-control" value="{{request()->get('value')}}">
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox margin-top">
						<button type='submit' class="btn btn-default">Search</button>
                    </div>
					
				</form>
            </div>
			<div class="pull-right mt-3">
                <a class="btn btn-default" href="{{ url('settings/update') }}">Edit Setting</a>
            </div>
        </div>
	</div>
   
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Routes
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>ID</th>
								<th>Setting Name</th>
								<th>Setting Value</th>
							</tr>
							@foreach ($data as $key => $val )
								<tr>
									<td>{{$key}}</td>
									@foreach($val as $settingName=>$settingVal)
										<td>{{$settingName}}</td>
										<td>{{$settingVal}}</td>
									@endforeach
								</tr>
							@endforeach
						</table>
						
						
                    </div>
                </div>
            </div>
		</div>
	</div>
@endsection
