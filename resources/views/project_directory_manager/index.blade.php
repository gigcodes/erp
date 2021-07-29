@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('large_content')
	<?php $base_url = URL::to('/');?>
	<div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Project Directory Manager (Total size Today : {{$totalSize}} MB)</h2>
        </div>
	</div>
	@if(Session::has('message'))
		<p class="alert alert-info">{{ Session::get('message') }}</p>
	@endif

	<div class = "row">
		<div class="col-lg-6 margin-tb">
			<div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('project-file-manager.index') }}" method="GET">
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Search</label>
                       <input type="text" name="search" class="form-control-sm cls_commu_his form-control" value="{{request()->get('search')}}">
                    </div>
                </form>
            </div>
		</div>	
	</div>
    <div class = "row" style="margin-top: -54px; margin-left:200px;">
		<div class="col-lg-3 margin-tb" >
          
            <form class="form-inline" method="POST">
            <div class="form-group ml-3 ">
                <input type="text" name="limit_size" id="limit_size" class="form-control-sm form-control limit_size"  placeholder="Enter size here..." style="margin-top: 18px;" value="{{ $limit_rec }}">
                <button type="button" class="btn btn-secondary submitsize" style="margin-top: 18px;">Update  </button>
            </div>
        </form>
           
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Directory
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>Directory Name</th>
								<th>Parent Directory</th>
								<th>Size</th>
								<th>Expected Size</th>
								<th>Created</th>
                                <th>Updated</th>
							</tr>
							@foreach ($projectDirectoryData as $data )
								<tr>
									<td>{{$data->name}}</td>
									<td>{{$data->parent}}</td>
									<td class="setup-size">{{$data->size}}(MB)
                                        <a title="File Size Details" class="fa fa-info-circle Size_log" data-id="{{$data->id}}" style="font-size:15px; margin-left:10px; color: #757575;"></a>
                                    </td>
									<td>
										<div class="col-md-12">
											<div class="col-md-6">
												<input class="form-control" id="expected_{{$data->id}}" name="notification_at" placeholder="Expected Size" value="{{$data->notification_at}}">
											</div>
											<div class="col-md-4">
												<button class="btn btn-sm btn-image send-message1" data-id="{{$data->id}}"><img src="images/filled-sent.png"></button>
                                                <button class="btn btn-sm btn-image get-latest-size" data-id="{{$data->id}}" title="Get latest size"><i class="fa fa-file"></i></button>
                                                <button class="btn btn-sm btn-image delete-file" data-id="{{$data->id}}" title="Delete"><i class="fa fa-trash"></i></button>
											</div>
										</div>
									</td>
									<td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
								</tr>
							@endforeach
						</table>
						
						{{ $projectDirectoryData->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>

    <div id="size_log-history-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">File Size History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
    
                <form action="" id="approve-log-btn" method="GET">
                    @csrf
                    <div class="modal-body">
                    <div class="row">
                       
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                       
                                        <th>Old Size</th>
                                        <th>New Size</th>
                                        <th>Updated By</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>             
            </div>
        </div>
      </div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).on('click', '.send-message1', function () {
	console.log("Hello");
	var thiss = $(this);
	var id = $(this).data('id');
	var size = $("#expected_"+id).val();
	
	console.log(size);
	
	if (!$(thiss).is(':disabled')) {
		$.ajax({
			url: "/project-file-manager/update",
			type: 'POST',
			data: {"_token": "{{ csrf_token() }}", id: id, size:size},
			beforeSend: function () {
				$(thiss).attr('disabled', true);
			},
			success: function (response) {
				$(thiss).attr('disabled', false);
				toastr['success'](response, 'success');
			},
			error: function (response) {
				console.log(response.responseText);
				$(thiss).attr('disabled', false);
				alert('Oops, Something went wrong!!');
			}
		});
	}
});

$(document).on("click",".get-latest-size",function() {
   
    var id = $(this).data("id");
    var $this = $(this);
    $.ajax({
        url: "/project-file-manager/get-latest-size",
        type: 'POST',
        data: {"_token": "{{ csrf_token() }}", id: id},
        beforeSend: function () {
            $("#loading-image").show();
        },
        dataType:"json",
        success: function (response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                var html = '';
                html += response.size;
                html += ' <a title="File Size Details" class="fa fa-info-circle Size_log" data-id="'+id+'" style="font-size:15px; margin-left:10px; color: #757575;"></a>';
                $this.closest("tr").find(".setup-size").html();
                toastr['success'](response.message, 'success');
            }else{
                toastr['error'](response.message, 'error');
            }
        },
        error: function (response) {
            $("#loading-image").hide();
            toastr['error']("Requested faield , please check log file.", 'error');
        }
    });
});

$(document).on("click",".delete-file",function() {
    var id = $(this).data("id");
    var $this = $(this);
    if(confirm("Are you sure you want to delete this file ? you will not able to roll back after delete")) {
        $.ajax({
            url: "/project-file-manager/delete-file",
            type: 'POST',
            data: {"_token": "{{ csrf_token() }}", id: id},
            beforeSend: function () {
                $("#loading-image").show();
            },
            dataType:"json",
            success: function (response) {
                $("#loading-image").hide();
                $this.closest("tr").remove();
                if(response.code == 200) {
                    toastr['success'](response.message, 'success');
                }else{
                    toastr['error'](response.message, 'error');
                }
            },
            error: function (response) {
                $("#loading-image").hide();
                toastr['error']("Requested faield , please check log file.", 'error');
            }
        });
    }
});
</script>
<script>
$(document).on("click", ".Size_log", function(e) {

        var id = $(this).data('id');
               
        $('#size_log-history-model table tbody').html('');
        $.ajax({
        url: "{{ route('size/log-history/discount') }}",
        data: {id: id},
        success: function (data) {
            if(data != 'error') {
                           
                $.each(data, function(i, item) {
                    
                    $('#size_log-history-model table tbody').append(
                        '<tr>\
                            <td>'+ ((item['old_size']) ? item['old_size'] : '-') +'</td>\
                            <td>'+item['new_size']+'</td>\<td>'+((item['name']) ? item['name'] : '-')+'</td>\
                            <td>'+ moment(item['updated_at']).format('DD/MM/YYYY') +'</td>\
                        </tr>'
                    );
                });
            }
        }
        });

        $('#size_log-history-model').modal('show');
});


$(document).on("click", ".submitsize", function(e) {

    let size = $("#limit_size").val();
    
    if ((size === "") || ($.isNumeric(size))) {
   
    
    let _token = $("input[name=_token]").val();

    $.ajax({
        url: "{{ route('project-file-manager.insertsize') }}",
        type: "POST",
        data: {
            size: size,
            _token: _token
        },
        success: function(response) {
            toastr["success"]("Data Updated Successfully!", "Message")
            
        }
});
    }else{
        alert('Not Valid NUmber');
        }
});


</script>
@endsection