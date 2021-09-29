@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
		div#addCustomer form.ajax-submit .form-group > span.select2 {
			width: 100% !important;
			margin: 6px 0 0 0;
		}
    </style>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet">
@endsection

@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Customers</h2>
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal"
                            data-target="#addCustomer"> Add Customers </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
				{{ Form::open(array('url'=>route('add.customer.group'), 'class'=>'ajax-submit')) }}
                <div class="modal-body edit-modal-body" id="edit-modal">
					<input type="hidden" id="message_group_id" name="message_group_id" value="{{$messageGroupId}}">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Customer Name</label>
                        {{ Form::select('customer_id', [], null, array('class'=>'form-control customer-select', 'required')) }}
                    </div>                      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>                    
                </div>
				</form>
            </div>
        </div>
    </div>

    {{--   <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Name</th>
                <th style="">Store Website</th>
                <th style="">Service</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($data as $value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$value->id}}</td>
                    <td id="name">{{$value->name}}</td>
                    <td id="description">{{$value->store_website_id}}</td>
                    <td id="description">{{$value->service_id}}</td>
                    <td>
					    <i class="fa fa-user-plus change" title="Add user" aria-hidden="true" onclick="showCustomerForm('{{$value->id}}')"></i>
					    <i class="fa fa-pencil-square-o change" title="Edit" aria-hidden="true"></i>
                        <i style="cursor: pointer;" class="fa fa-trash trigger-delete" data-route="{{route('delete-twilio-task-queue')}}" data-id="{{$value->id}}" aria-hidden="true"></i>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(isset($data))
            {{ $data->links() }}
        @endif
    </div>--}}
@endsection


@section('scripts')



    <script>
		var base_url = window.location.origin+'/';
	    $('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { 
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						toastr["success"](data.message);
						setTimeout(function(){
                          location.reload();
                        }, 1000);
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		}); 

	    $('.trigger-delete').on('click', function(e) {
			var id = $(this).attr('data-id');
			e.preventDefault(); 
			var option = { _token: "{{ csrf_token() }}", id:id };
			var route = $(this).attr('data-route');
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
						$(this).closest('tr').remove();
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
					setTimeout(function(){
                          location.reload();
                        }, 1000);
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			}); 
		});	

		$(document).ready(function() {
		$(".customer-select").select2({
			ajax: {
				url: base_url+"fetch/customers",
				dataType: 'json',
				delay: 250,
				data: function(params) {
					return {
						q: params.term // search term
					};
				},
				processResults: function(data, params) {
					var resData = [];
					data.forEach(function(value) {
						if (value.name.indexOf(params.term) != -1)
							resData.push(value)
					})
					return {
						results: $.map(resData, function(item) {
							return {
								text: item.name,
								id: item.id
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 3
		})
	});
    </script>
@endsection