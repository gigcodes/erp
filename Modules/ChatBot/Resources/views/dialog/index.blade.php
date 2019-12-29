@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Dialog | Chatbot')

@section('content')
<style type="text/css">
	table.dataTable thead .sorting:after,
	table.dataTable thead .sorting:before,
	table.dataTable thead .sorting_asc:after,
	table.dataTable thead .sorting_asc:before,
	table.dataTable thead .sorting_asc_disabled:after,
	table.dataTable thead .sorting_asc_disabled:before,
	table.dataTable thead .sorting_desc:after,
	table.dataTable thead .sorting_desc:before,
	table.dataTable thead .sorting_desc_disabled:after,
	table.dataTable thead .sorting_desc_disabled:before {
	bottom: .5em;
	}
</style>
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Dialog | Chatbot</h2>
	</div>
</div>
<div class="row">
        <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
            <div class="pull-right">
                <div class="form-inline">
                    <button type="button" class="btn btn-secondary ml-3" id="create-keyword-btn">Create</button>
            	</div>
            </div>
        </div>
    </div>
<div class="tab-pane">
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
			  <thead>
			    <tr>
			      <th class="th-sm">Id</th>
			      <th class="th-sm">Name</th>
			      <th class="th-sm">Condition</th>
			      <th class="th-sm">Response</th>
			      <th class="th-sm">Action</th>
			    </tr>
			  </thead>
			  <tbody>
			    <?php foreach($chatDialog as $raw) { ?>
				    <tr>
				      <td><?php echo $raw->id; ?></td>
				      <td><?php echo $raw->name; ?></td>
				      <td><?php echo $raw->match_condition; ?></td>
				      <td><?php echo $raw->responses; ?></td>
				      <td>
                        <a class="btn btn-image edit-button" data-id="<?php echo $raw->id; ?>" href="<?php echo route("chatbot.dialog.edit",[$raw->id]); ?>"><img src="/images/edit.png"></a>
                        <a class="btn btn-image delete-button" data-id="<?php echo $raw->id; ?>" href="<?php echo route("chatbot.dialog.delete",[$raw->id]); ?>"><img src="/images/delete.png"></a>
				      </td>
				    </tr>
				<?php } ?>
			  </tbody>
			  <tfoot>
			    <tr>
			      <th>Id</th>
			      <th>Name</th>
			      <th>Condition</th>
			      <th>Response</th>
			      <th>Action</th>
			    </tr>
			  </tfoot>
			</table>
	    </div>
	    <div class="col-lg-12 margin-tb">
	    	<?php echo $chatDialog->links(); ?>
	    </div>
	</div>
</div>
@include('chatbot::partial.create_dialog')
<script type="text/javascript">
	$("#create-keyword-btn").on("click",function() {
		$("#create-dialog").modal("show");
	});

	$(".select2").select2();

	$(".form-save-btn").on("click",function(e) {
		e.preventDefault();
		var form = $(this).closest("form");
		$.ajax({
			type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType : "json",
            success: function (response) {
               //location.reload();
               if(response.code == 200) {
               	  toastr['success']('data updated successfully!');
               	  window.location.replace(response.redirect);
               }else{
               	  toastr['error']('data is not correct or duplicate!');
               } 
            },
            error: function () {
               toastr['error']('Could not change module!');
            }
        });
	});
</script>
@endsection