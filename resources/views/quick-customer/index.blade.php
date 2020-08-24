@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
@section('link-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.daterangepicker .ranges li.active {
		background-color : #08c !important;
	}
    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover 
    {
        background-color :  #fff ;
        border-color : #6c757d;
    }
    .page-item.active .page-link {
        background-color :  #6c757d ;
        border-color : #6c757d;
    }
    .pagination>li>a, .pagination>li>span {
        color: #6c757d;
    }
    .pagination>li>a:focus, .pagination>li>a:hover, .pagination>li>span:focus, .pagination>li>span:hover {
        color: #6c757d;   
    }

    .pagination>li>a, .pagination>li>span {
        color: #6c757d;   
    }

    .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
        color: #6c757d;   
    }

</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 alert alert-success">
	        {{ session()->get('success') }}
	    </div>
	@endif
	@if(session()->has('error'))
	    <div class="col-lg-12 alert alert-danger">
	        {{ session()->get('error') }}
	    </div>
	@endif
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-3">
		    	<div class="row">
	    			
				 </div>
		    </div>
		    <div class="col">
		    	<div class="h">
		    		<form class="form-inline message-search-handler mb-2 fr" method="post" style="float: right;">
					  <div class="row">
				  			<div class="form-group" style="margin-left: 2px">
							    <label for="customer_id">Search Customer ID:</label>
							    <?php echo Form::text("customer_id",request("customer_id"),["class"=> "form-control","placeholder" => "Enter customer id"]) ?>
						  	</div>
						  	<div class="form-group" style="margin-left: 2px">
							    <label for="customer_name">Search Name:</label>
							    <?php echo Form::text("customer_name",request("customer_name"),["class"=> "form-control","placeholder" => "Enter customer name"]) ?>
						  	</div>
						  	<div class="form-group" style="margin-left: 2px">
							    <label for="status">Sort By:</label>
							    <?php echo Form::select("type",[
							    	"" => "-- Select --", 
							    	"unread" => "Unread",
							    	"last_communicated" => "Last Communicated",
							    	"last_received" => "Last Received",
							    ],request("type"),["class"=> "form-control","placeholder" => "Type"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="col-md-12 margin-tb" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>
<div id="task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
                <div class="modal-header">
                    <h2>Task statistics</h2>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="task_statistics_content">
                    
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                <input type="text" name="search_chat_pop_time"  class="form-control search_chat_pop_time" placeholder="Search Time" style="width: 200px;">
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@include("quick-customer.templates.list-template")
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/site-helper.js"></script>
<script type="text/javascript" src="/js/quick-customer.js"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

<script type="text/javascript">
	

	$(document).on("click",".add_next_action",function() {
        siteHelpers.addNextAction($(this));
    });

    $(document).on("click",".delete_next_action",function() {
        siteHelpers.deleteNextAction($(this));
    });

    $(document).on("click",".next_action",function() {
        siteHelpers.changeNextAction($(this));
    });

    $(document).on('submit', "#send_message", function (e) {
        e.preventDefault();
        siteHelpers.erpLeadsSendMessage();
    });

    $(document).on('click', '.send-message', function () {
        siteHelpers.sendMessage($(this));
    });

    $(document).on('click', '.do_not_disturb', function() {
        var id = $(this).data('id');
        var thiss = $(this);
        $.ajax({
            type: "POST",
            url: "/customer/" + id + '/updateDND',
            data: {
                _token: "{{ csrf_token() }}",
                // do_not_disturb: option
            },
            beforeSend: function() {
                $(thiss).text('DND...');
            }
        }).done(function(response) {
          if (response.do_not_disturb == 1) {
            $(thiss).html('<img src="/images/do-not-disturb.png" />');
          } else {
            $(thiss).html('<img src="/images/do-disturb.png" />');
          }
        }).fail(function(response) {
          alert('Could not update DND status');
          console.log(response);
        })
   });

	$(document).on("click",".count-customer-tasks",function() {
        var $this = $(this);
        var customer_id = $(this).data("id");
        $.ajax({
            type: 'get',
            url: BASE_URL+'/erp-customer/task/count/'+customer_id,
            dataType : "json",
            success: function(data) {
                $("#task_statistics").modal("show");
                var table = '';
                table = table + '<div class="table-responsive"><table class="table table-bordered table-striped"><tr><th>Name</th><th>Pending</th><th>Completed</th></tr><tr><td>Devtask</td><td>'+data.taskStatistics.Devtask.pending+'</td><td>'+data.taskStatistics.Devtask.completed+'</td></tr><tr><td>Task</td><td>'+data.taskStatistics.Task.pending+'</td><td>'+data.taskStatistics.Task.completed+'</td></tr></table></div>';
                $("#task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $(document).on("click",".create-customer-related-task",function() {
        var $this = $(this);
        var user_id = $(this).closest("tr").find(".ucfuid").val();
        var customer_id = $(this).data("id");

        var modalH = $("#quick-create-task");
            modalH.find(".task_asssigned_to").select2('destroy');
            modalH.find(".task_asssigned_to option[value='"+user_id+"']").prop('selected', true);
            modalH.find(".task_asssigned_to").select2({});
            modalH.find("#task_subject").val("Customer #"+customer_id+" : ");
            modalH.find("#hidden-category-id").remove();
            modalH.find("form").append('<input id="hidden-category-id" type="hidden" name="category_id" value="42" />');
            modalH.find("form").append('<input id="hidden-customer-id" type="hidden" name="customer_id" value="'+customer_id+'" />');
            modalH.modal("show");  
    });

</script>

@endsection

