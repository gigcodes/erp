@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Assets Manager User Access")

@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Assets Manager User Access</h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
    <div class="col-lg-12 margin-tb">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="4%">ID</th>
                            <th width="9%">Selected User</th>
                            <th width="9%">User Name</th>
                            <th width="9%">Password</th>
                            <th width="8%">Created Date</th>
                            <th width="8%">Request Data</th>
                            <th width="8%">Response Data</th>
                        </tr>
                    </thead>
				    <tbody>
						<?php 
						if($user_accesses) {
							$i = 1;
							foreach($user_accesses as $user_access) { ?>
								<tr class="trrow">
									<td width="5%" class="expand-row">
										{{$i}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->selectedUser}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->username}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->password}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->created_at}}
									</td>
									<td width="20%" class="expand-row">
										{{$user_access->request_data}}
									</td>
									<td width="20%" class="expand-row">
										{{$user_access->response_data}}
									</td>
								</tr>
						<?php 
								$i++;
							}  
						} ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
$(".btn-copy-password").click(function() {
            var password = $(this).data('id');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(password).select();
            document.execCommand("copy");
            $temp.remove();
        });