@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Intent | Chatbot')

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
	    <h2 class="page-heading">Intent | Chatbot</h2>
	</div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
    	<div class="pull-left">
    		<form action="" method="get">
	            <div class="row">
				    <div class="col">
				      <input type="text" name="q" value="<?php echo request("q",""); ?>" class="form-control" placeholder="Keyword">
				    </div>
				    <div class="col">
				      <select name="category_id" class="select-chatbot-category form-control"></select>
				    </div>
				    <div class="col">
				      <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
				    </div>
				</div>
			</form>
        </div>
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
			      <th class="th-sm">Intent</th>
			      <th class="th-sm">User Example</th>
			      <th class="th-sm">Category</th>
			      <th class="th-sm">Action</th>
			    </tr>
			  </thead>
			  <tbody>
			    <?php foreach($chatQuestions as $chatQuestion) { ?>
				    <tr>
				      <td><?php echo $chatQuestion->id; ?></td>
				      <td><?php echo $chatQuestion->value; ?></td>
				      <td><?php echo $chatQuestion->questions; ?></td>
				      <td><?php echo $chatQuestion->category_name; ?></td>
				      <td>
                        <a class="btn btn-image edit-button" data-id="<?php echo $chatQuestion->id; ?>" href="<?php echo route("chatbot.question.edit",[$chatQuestion->id]); ?>"><img src="/images/edit.png"></a>
                        <a class="btn btn-image delete-button" data-id="<?php echo $chatQuestion->id; ?>" href="<?php echo route("chatbot.question.delete",[$chatQuestion->id]); ?>"><img src="/images/delete.png"></a>
				      </td>
				    </tr>
				<?php } ?>
			  </tbody>
			  <tfoot>
			    <tr>
			      <th>Id</th>
			      <th>Intent</th>
			      <th>User Example</th>
			      <th>Category</th>
			      <th>Action</th>
			    </tr>
			  </tfoot>
			</table>
	    </div>
	    <div class="col-lg-12 margin-tb">
	    	<?php echo $chatQuestions->links(); ?>
	    </div>	
	</div>
</div>
@include('chatbot::partial.create_question')
<script type="text/javascript">
	$("#create-keyword-btn").on("click",function() {
		$("#create-question").modal("show");
	});
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
				errorMessage = response.error ? response.error : 'data is not correct or duplicate!';
               	toastr['error'](errorMessage);
               } 
            },
            error: function () {
               toastr['error']('Could not change module!');
            }
        });
	});

	$(".select-chatbot-category").select2({
            placeholder: "Enter category name",
            width: "100%",
            tags: true,
            allowClear: true,
            ajax: {
                url: '/chatbot/question/search-category',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                }
            }
        });		
</script>
@endsection