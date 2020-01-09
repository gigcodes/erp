@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Entities | Chatbot')

@section('content')
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Edit {{ $chatbotKeyword->keyword }} | Chatbot</h2>
	</div>
</div>
<div class="tab-pane">
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	    	<div class="well">
	    		<form action="{{ route('chatbot.keyword.update',[$chatbotKeyword->id]) }}" method="post">
    				  <?php echo csrf_field(); ?>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="keyword">Entity</label>
					      <small id="emailHelp" class="form-text text-muted">Name your entity to match the category of values that it will detect.</small>
					      <?php echo Form::text("keyword", $chatbotKeyword->keyword, ["class" => "form-control", "id" => "keyword", "placeholder" => "Enter your keyword"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Value</label>
					      <?php echo Form::text("value", null, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					  </div>
					  <button type="submit" class="btn btn-primary">Add Value</button>
				</form>
	    	</div>
		</div>
		<div class="col-lg-12 margin-tb">
			<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
				  <thead>
				    <tr>
				      <th class="th-sm">Id</th>
				      <th class="th-sm">Value</th>
				      <th class="th-sm">Action</th>
				    </tr>
				  </thead>
				  <tbody>
				    <?php foreach ($chatbotKeyword->chatbotKeywordValues as $value) {?>
					    <tr>
					      <td><?php echo $value->id; ?></td>
					      <td><?php echo $value->value; ?></td>
					      <td>
	                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.value.delete", [$chatbotKeyword->id, $value->id]); ?>">
	                        	<img src="/images/delete.png">
	                        </a>
					      </td>
					    </tr>
					<?php }?>
				  </tbody>
				  <tfoot>
				    <tr>
				      <th>Id</th>
				      <th>Value</th>
				      <th>Action</th>
				    </tr>
				  </tfoot>
				</table>
		</div>
	</div>
</div>
@endsection