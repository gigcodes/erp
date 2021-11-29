@extends('layouts.app')

@section('title', 'Broadcast Messages')

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
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Broadcast Messages</h2>
                    
                    <div class="pull-left cls_filter_box">
                        {{Form::model( [], array('method'=>'get', 'class'=>'form-inline')) }}
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Order By</label>
                                {{Form::select('order', [''=>'Select','asc'=>'ASC','desc'=>'DESC'], @$inputs['order'], array('class'=>'form-control'))}}
                            </div>
                         
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="name">Name</label>
                                {{Form::text('name', @$inputs['name'], array('class'=>'form-control'))}}
                            </div>
                            <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
                        </form>
                    </div>
                    
              
							
							
                </div>
            </div>
        </div>

    </div>

	
    <div class="modal fade" id="messageTitle" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Message Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    {{Form::open(array('url'=>route('create.marketing.message'), 'class'=>'ajax-submit'))}}
					<div id="messageTitleForm"></div>
					</form>
                </div>
            </div>
        </div>
    </div>


    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Name</th>
                <th style="">Created Date</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($data as $key=>$value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$key+1}}</td>
                    <td id="name">{{$value->name??''}}</td>
                    <td id="name">{{$value->created_at->format('Y-m-d')??''}}</td>
                    <td>
                        <a class="btn btn-secondary create_broadcast" data-message-id="<?php echo $value->id; ?>" href="javascript:;"><i class="fa fa-plus" aria-hidden="true"></i></a>
                        <a title="Preview Broadcast Numbers" data-message-id="<?php echo $value->id; ?>" class="btn btn-image preview_broadcast_numbers" href="javascript:;"  ><i class="fa fa-eye" aria-hidden="true"></i></a>
						<a data-route="{{route('delete.message')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(isset($data))
            {{ $data->links() }}
        @endif
    </div>
@endsection

<div id="create_broadcast" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Send Message</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="send_message" method="POST">
                <input type="hidden" name="id" id="bid" value="">
				<div class="modal-body">
					<div class="form-group">
						<strong>Name</strong>
						<input name="name" id="name" autocomplete="off" type="text" class="form-control"/>
					</div>
					<div class="form-group">
						<strong>Message</strong>
						<textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-secondary">Send Message</button>
				</div>
			</form>
		</div>

	</div>
</div>


<div id="previewSendMailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
       <!-- Modal content-->
       <div class="modal-content "  >
          
             <div class="modal-header">
                <h4 class="modal-title">Broadcast Message Numbers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>
 
 
              <div class="modal-body" >
             <div class="table-responsive" style="margin-top:20px;">
               <table class="table table-bordered text-nowrap" id="email-table">
                 <thead>
                   <tr>
                   <th>Date </th> 
                   <th>Message Id</th>
                   <th>Type Id</th>
                   <th>Type</th>
                   </tr>
                 </thead>
                 <tbody class="product-items-list">
                 
                 </tbody>
                 </table>
             </div>
           </div>
 
             
             <div class="modal-footer">
                <div class="row">
                   <button type="button" style="margin-top: 5px;" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
             </div>
          
       </div>
    </div>
 </div>

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

        $(document).on('click', '.create_broadcast', function () {
            var id = $(this).data("message-id");
            $("#bid").val(id);
            $("#create_broadcast").modal("show");
        }); 
        
        $("#send_message").submit(function (e) {
            e.preventDefault();

            if ($("#send_message").find("#name").val() == "") {
                alert('Please type name ');
                return false;
            }

            if ($("#send_message").find("#message_to_all_field").val() == "") {
                alert('Please type message ');
                return false;
            }

            $.ajax({
                type: "POST",
                url: "broadcast-messages/send/message",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: $("#send_message").find("#message_to_all_field").val(),
                    name: $("#send_message").find("#name").val(),
                    id:$("#send_message").find("#bid").val(),
                }
            }).done(function () {
                window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

      $(document).on("click",".preview_broadcast_numbers",function() {

        var id = $(this).data("message-id");

        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "broadcast-messages/preview-broadcast-numbers",
        type: "post",
        data : {
            id: id,
        },
        beforeSend: function() {

            $("loading-image").show();
        }
        }).done( function(response) {

        $("loading-image").hide();

        if(response.code == 200) {
        var items = response.data;
        if(items.length > 0) {
            var itemsHtml = '';
            $.each(items, function(k,v) {
                itemsHtml += `<tr class="in-background filter-message">
                    <td >`+v.created_at+`</td>
                    <td >`+v.broadcast_message_id+`</td>
                    <td >`+v.typeName+`</td>
                    <td >`+v.type+`</td>
                </tr>`;

            });
            
            $("#previewSendMailsModal").find(".product-items-list").html(itemsHtml);
        }

        $("#previewSendMailsModal").modal("show");

        }
        
        }).fail(function(errObj) {
            alert("Could not change status");
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

		function showMessageTitleModal(groupId){
			$('#message_group_id').val(groupId);
			$.get(base_url+"twillio/marketing/message/"+groupId, function(data, status){ 
				$('#messageTitleForm').html(data);
			});
			$('#messageTitle').modal('show');			
		}
    </script>
@endsection