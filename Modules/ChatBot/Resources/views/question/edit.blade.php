@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Question | Chatbot')

@section('content')
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Edit {{ $chatbotQuestion->value }} | Chatbot</h2>
	</div>
</div>
<div class="tab-pane">
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	    	<div class="well">
	    		<form action="{{ route('chatbot.question.update',[$chatbotQuestion->id]) }}" method="post">
    				  <?php echo csrf_field(); ?>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Value</label>
					      <small id="emailHelp" class="form-text text-muted">Name your entity to match the category of values that it will detect.</small>
					      <?php echo Form::text("value", $chatbotQuestion->value, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Question</label>
					      <?php echo Form::text("question", null, ["class" => "form-control", "id" => "question", "placeholder" => "Enter your question"]); ?>
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
				      <th class="th-sm">Value</th>
				      <th class="th-sm">Type</th>
				      <th class="th-sm">Action</th>
				    </tr>
				  </thead>
				  <tbody>
				    <?php foreach ($chatbotQuestion->chatbotQuestionExamples as $value) {?>
					    <tr>
					      <td><?php echo $value->id; ?></td>
					      <td><?php echo $value->question; ?></td>
					      <td>
	                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.question-example.delete", [$chatbotQuestion->id, $value->id]); ?>">
	                        	<img src="/images/delete.png">
	                        </a>
					      </td>
					    </tr>
					<?php }?>
				  </tbody>
				  <tfoot>
				    <tr>
				      <th>Value</th>
				      <th>Type</th>
				      <th>Action</th>
				    </tr>
				  </tfoot>
				</table>
		</div>
	</div>
</div>
@endsection