@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Intent | Chatbot')

@section('content')
<style type="text/css">
	.border-div {
		border-style: dashed;
	    margin: 2px;
	    text-align: center;
	    float: left;
	    border-width: 3px;
	    cursor: pointer;
	}

	.dashed-question.selected {
		border-color: coral
	}
</style>
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Edit {{ $chatbotQuestion->value }} | Chatbot</h2>
	</div>
</div>
<div class="tab-pane">
		<div class="row">
			<div class="col-lg-12 margin-tb">
			@if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
		</div>
	    <div class="col-lg-12 margin-tb">
	    	<div class="well">
	    		<form action="{{ route('chatbot.question.update',[$chatbotQuestion->id]) }}" method="post">
    				  <?php echo csrf_field(); ?>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Intent</label>
					      <small id="emailHelp" class="form-text text-muted">Name your intent to match the category of values that it will detect.</small>
					      <?php echo Form::text("value", $chatbotQuestion->value, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Category</label>
					      <?php echo Form::select("category_id",[], null, ["class" => "form-control select-chatbot-category", "id" => "chatbot-category-id"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">User Example</label>
					      <?php echo Form::text("question", null, ["class" => "form-control", "id" => "question", "placeholder" => "Enter your question"]); ?>
					    </div>
					  </div>
					  <button type="submit" class="btn btn-primary">Add Intent</button>
				</form>
	    	</div>
		</div>
		<div class="col-lg-12 margin-tb">
			<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
			  <thead>
			    <tr>
			      <th class="th-sm">Id</th>
			      <th class="th-sm">User Example</th>
			      <th class="th-sm">Annotation</th>
			      <th class="th-sm">Action</th>
			    </tr>
			  </thead>
			  <tbody>
			    <?php foreach ($chatbotQuestion->chatbotQuestionExamples as $value) {?>
				    <tr>
				      <td><?php echo $value->id; ?></td>
				      <td class="text-question" data-question-id="<?php echo $value->id; ?>" data-question="<?php echo $value->question; ?>">
				      	<?php echo $value->question; ?>
				     </td>
				     <td>
				     	<?php foreach ($value->highLightQuestion() as $key => $valueRaw) { ?>
				     			<div class="delete-annotation-raw" style="display: inline-block;padding-left: 2px;">
				     				<?php echo $valueRaw; ?>
					     			<span data-id="<?php echo $key; ?>" class="close delete-annotation" aria-label="Close"><span aria-hidden="true">&times;</span></span>
					     		</div>
				     	<?php } ?>
				     </td>	
				      <td>
                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.question-example.delete", [$chatbotQuestion->id, $value->id]); ?>">
                        	<img src="/images/delete.png">
                        </a>
                        <a class="btn btn-image annotation-button">
                        	<img src="/images/starred.png">
                        </a>
				      </td>
				    </tr>
				<?php }?>
			  </tbody>
			  <tfoot>
			    <tr>
			      <th>Id</th>
			      <th>User Example</th>
			      <th>Annotation</th>
			      <th>Action</th>
			    </tr>
			  </tfoot>
			</table>
		</div>
	</div>
</div>

@include('chatbot::partial.create_question_annotation')

<script type="text/javascript">

	var searchForKeyword = function(ele) {
    	var keywordBox = ele.find(".search-keyword");
	    if (keywordBox.length > 0) {
	        keywordBox.select2({
	            placeholder: "Enter entity name or create new one",
	            width: "100%",
	            tags: true,
	            allowClear: true,
	            ajax: {
	                url: '/chatbot/keyword/search',
	                dataType: 'json',
	                processResults: function(data) {
	                    return {
	                        results: data.items
	                    };
	                }
	            }
	        });
	    }
	};
	
	var annotationButton = $(".annotation-button");
		annotationButton.on("click",function() {
			var text =  $(this).closest("tr").find(".text-question");
			var words = text.data("question").split(" ");
			var html = "";
			var char = 0; 
			var start = 0; 
			$.each(words,function(k,v) {
				char += v.length;
				var ln = (k == 0) ? 0 : char;

				if(k != 0) {
					start = ln - v.length;
				}

				html += "<div class='border-div dashed-question' data-start='"+start+"' data-end='"+ln+"'>"+v+"</div>";
				char += 1;
			})
			text.html(html);
		});

		$(document).on("click",".dashed-question",function() {
			var $this = $(this);
			var question = $this.closest(".text-question");
				if($this.hasClass("selected")) {
					$this.removeClass("selected")
				}else{
					$this.addClass("selected");
				}
				if(question.find(".selected").length > 1) {
					$.each($this.prevAll(),function(k,v){
						if($(v).hasClass("selected")) {
							return false;
						}else{
							$(v).addClass("selected");
						}
					})
					
					searchForKeyword($("#create-question-annotation"));

					var startRange = question.find(".selected").first().data("start");
					var endRange   = question.find(".selected").last().data("end");

					var keywordValue = question.data("question").substring(startRange, endRange);
					
					var modelBox = $("#create-question-annotation");
						modelBox.find("#question-example-id").val(question.data("question-id"));
						modelBox.find("#start-char-range").val(startRange);
						modelBox.find("#end-char-range").val(endRange);
						modelBox.find("#keyword-value").val(keywordValue);
						modelBox.modal("show");
				}
				$this.nextAll().removeClass("selected");
		});

		$("#create-question-annotation").on("click",".form-save-btn",function() {
			var form = $(this).closest("form");
			$.ajax({
				type: form.attr("method"),
	            url: form.attr("action"),
	            data: form.serialize(),
	            dataType : "json",
	            success: function (response) {
	               if(response.code == 200) {
	               	  toastr['success']('data updated successfully!');
	               	  location.reload();
	               }else{
	               	  toastr['error']('data is not correct or duplicate!');
	               } 
	            },
	            error: function () {
	               toastr['error']('Could not change module!');
	            }
	        });
		});

		$(document).on("click",".delete-annotation",function() {
			var $this = $(this);
			var anntid = $this.data("id");
			$.ajax({
				type: "GET",
	            url: "/chatbot/question/annotation/delete",
	            data: {id : anntid},
	            dataType : "json",
	            success: function (response) {
	               if(response.code == 200) {
	               	  toastr['success']('data updated successfully!');
	               	  $this.closest(".delete-annotation-raw").remove();
	               }else{
	               	  toastr['error']('Oops, something went wrong!');
	               } 
	            },
	            error: function () {
	               toastr['error']('Could not change module!');
	            }
	        });
		});

		$(".select-chatbot-category").select2({
            placeholder: "Enter category name or existing",
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