@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Dialog | Chatbot')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Dialog | Chatbot</h2>
	</div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
        <div class="pull-right">
            <div class="form-inline">
                <button type="button" class="btn btn-secondary ml-3" id="create-dialog-btn-rest">Create</button>
        	</div>
        </div>
    </div>
</div>

<div class="tab-pane">
	<div class="row">
	    <div class="col-lg-12 margin-tb" id="tree-container-dialog">
	    	<dialog-x store="store" segment="segment" workspace="workspace" plan="plan" search-config="searchConfig">
			   <div class="wc--dialog">
			      <div class="dialog">
			         <div class="dialog-tree-container">
			            <ul id="dialog-tree" class="node-children">
			               
			            </ul>
			         </div>
			      </div>
			   </div>
			</dialog-x>
	    </div>
	 </div>
</div>

<div class="modal fade" id="leaf-editor-model" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Editor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save-dialog-btn">Save changes</button>
      </div>
    </div>
  </div>
</div>
<?php include_once(app_path()."/../Modules/ChatBot/Resources/views/dialog/includes/template.php"); ?>
@include('chatbot::partial.create_dialog')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript">

	var dialogBoxData = "";
	var allSuggestedOptions = "";
	


	var updateBoxEvent =  function(parentId) {
		var parent_id = 0;
		if(typeof parentId != "undefined") {
			parent_id = parentId;
		}
		$.ajax({
			type: "get",
	        url: "/chatbot/rest/dialog/status",
	        data : {
	        	"parent_id" : parent_id
	        },
	        dataType : "json",
	        success: function (response) {

	        	if(response.code == 200) {
	        		dialogBoxData = response.data.chatDialog;
					allSuggestedOptions = response.data.allSuggestedOptions;
					if(dialogBoxData.length > 0) {
						var html = "";
						$.each(dialogBoxData, function(k,v){
							var myTmpl = $.templates("#dialog-leaf");
								html += myTmpl.render({"data" : v});
						});

						if(parent_id > 0) {
							var dilogTree = $(".node_child_"+parent_id).find(".node-children");
						}else{
							var dilogTree = $("#dialog-tree");
						}
						dilogTree.html(html);
						$("#leaf-editor-model").modal("hide");
					}
	        	}	
	        },
	        error: function () {
	           toastr['error']('Could not change module!');
	        }
	    });
	};

	updateBoxEvent(0);

	$(document).on("click","#create-dialog-btn-rest",function(e){
		e.preventDefault();
		var previous_node = 0;
		var previous = $("#dialog-tree").find("li").last();
			if(previous.length > 0) {
				previous_node = previous.data("id");
			}
		$.ajax({
			type: "get",
	        url: "/chatbot/rest/dialog/create",
	        data : {
	        	"previous_node" : previous_node
	        },
	        dataType : "json",
	        success: function (response) {

	        	if(response.code == 200) {
	        		updateBoxEvent();
	        	}	
	        },
	        error: function () {
	           toastr['error']('Could not change module!');
	        }
	    });
	});
	
	$(document).on("click",".node__contents",function(e) {
		var node = $(this).closest(".node").data("id");
  		$("#leaf-editor-model").modal("show");
		$.ajax({
			type: "get",
            url: "/chatbot/rest/dialog/"+node,
            dataType : "json",
            success: function (response) {
               
               var myTmpl = $.templates("#add-dialog-form");
               var html   = myTmpl.render({"data" : response.data});
               
               $("#leaf-editor-model").find(".modal-body").html(html);
               $("[data-toggle='toggle']").bootstrapToggle('destroy')                 
			   $("[data-toggle='toggle']").bootstrapToggle();
           	
           	   $(".search-alias").select2();	

            },
            error: function () {
               toastr['error']('Could not change module!');
            }
        });
	});



	$(document).on("click",".add-more-condition-btn",function(){
		var buttonOptions = $.templates("#add-more-condition");
		$(".show-more-conditions").append(buttonOptions.render({"allSuggestedOptions" : allSuggestedOptions}));
		$(".search-alias").select2();
	});	

	$(document).on("click",".remove-more-condition-btn",function(){
		$(this).closest(".form-row").remove();
	});

	$(document).on("click",".node__menu",function(e) {
		//e.stopPropagation();
	});

	$(document).click(function(){
		//$(".bx--overflow-menu-options").remove();
	});

	$(document).on("change",".multiple-conditioned-response",function() {
		var hasChecked = $(this).prop('checked');
		if(hasChecked == true) {
			var identifier = "new_"+new Date().getTime();
			var tmpl = $.templates("#multiple-response-condition");
			$(".assistant-response-based").html(tmpl.render({ "identifier" : identifier, "allSuggestedOptions" : allSuggestedOptions }));
			$(".assistant-response-based").find(".search-alias").select2({});
		}else{
			var tmpl = $.templates("#single-response-condition");
			$(".assistant-response-based").html(tmpl.render({}));
		}
	});

	$(document).on("click",".btn-add-mul-response",function() {
		var identifier = "new_"+new Date().getTime();
		var tmpl = $.templates("#multiple-response-condition");
		$(".assistant-response-based").append(tmpl.render({ "identifier" : identifier, "allSuggestedOptions" : allSuggestedOptions }))
	});

	$(document).on("click",".btn-delete-mul-response",function() {
		$(this).closest(".form-row").remove();
	});

	$(document).on("click",".bx--overflow-menu",function() {
		var hasPop = $(this).data("has-pop");
			if(hasPop === false)  {
				var buttonOptions = $.templates("#dialog-leaf-button-options");
				var html = buttonOptions.render({});
				$(this).append(html);
				$(this).attr("data-has-pop",true);
				$(this).data('has-pop', true);
			}else{
				$(this).find(".bx--overflow-menu-options").remove();
				$(this).attr("data-has-pop",false);
				$(this).data('has-pop', false);
			}
	});

	$(document).on("click",".node__expander",function(e) {
		var li = $(this).closest(".node-child");
		updateBoxEvent(li.data("id"));
	});

	$(document).on("change",".search-alias",function() {
		var selectedValue = $(this).val();
		var res = selectedValue.match(/@/g);
		if(res != "" && res != null) {
			$(this).closest(".form-row").find(".extra_condtions").removeClass("dis-none");
		}else{
			$(this).closest(".form-row").find(".extra_condtions").addClass("dis-none");
		}

	});

	$(document).on("click",".bx--overflow-menu-options > li",function() {
		var buttonRole = $(this).find("button").attr("role");

			if(buttonRole == "add_child") {
			  var main = $(this).closest(".node-child");
			 /* var space = main.find(".node-children");
			  	  space.append(myTmpl.render({}));*/

			  	  $.ajax({
					type: "get",
		        	url: "/chatbot/rest/dialog/create",
		        	dataType : "json",
		        	data : {
		        		"parent_id" : main.data("id")
			        },
			        success: function (response) {
			        	if(response.code == 200) {
			        		updateBoxEvent(main.data("id"));
			        	}	
			        },
			        error: function () {
			           toastr['error']('Could not change module!');
			        }
			    });



			}else if(buttonRole == "add_above") {
				var main = $(this).closest(".node-child");
				    //main.before(myTmpl.render({}));
				var current_node  = $(this).closest(".node-child").data("id");
				var previous_node = 0;
				var previousNodeChild = $(this).closest(".node-child").prev();
					if(previousNodeChild.length > 0) {
						previous_node = previousNodeChild.data("id");
					}
				var parent_id = main.data("parent-id");	

				$.ajax({
					type: "get",
		        	url: "/chatbot/rest/dialog/create",
		        	dataType : "json",
		        	data : {
		        		"current_node" : current_node,
			        	"previous_node" : previous_node,
			        	"parent_id" : parent_id
			        },
			        success: function (response) {
			        	if(response.code == 200) {
			        		updateBoxEvent(parent_id);
			        	}	
			        },
			        error: function () {
			           toastr['error']('Could not change module!');
			        }
			    });

			}else if(buttonRole == "add_below") {

				var main = $(this).closest(".node-child");
				var previous_node = $(this).closest(".node-child").data("id");
				var current_node = 0;
				var nextNodeChild = $(this).closest(".node-child").next();
					if(nextNodeChild.length > 0) {
						current_node = nextNodeChild.data("id");
					}

				var parent_id = main.data("parent-id");

				$.ajax({
					type: "get",
		        	url: "/chatbot/rest/dialog/create",
		        	dataType : "json",
		        	data : {
		        		"current_node" : current_node,
			        	"previous_node" : previous_node,
			        	"parent_id" : parent_id
			        },
			        success: function (response) {
			        	if(response.code == 200) {
			        		updateBoxEvent(parent_id);
			        	}	
			        },
			        error: function () {
			           toastr['error']('Could not change module!');
			        }
			    });

			}else if(buttonRole == "delete") {
				var main = $(this).closest(".node-child");
					var node = $(this).closest(".node").data("id");
			  		$.ajax({
						type: "get",
			            url: "/chatbot/rest/dialog/"+node+"/delete",
			            dataType : "json",
			            success: function (response) {
							if(response.code == 200) {
								toastr['success']('data deleted successfully!');
								main.remove();
							}
			            },
			            error: function () {
			               toastr['error']('Could not change module!');
			            }
			        });	
			}

	});

	$(document).on("click",".save-dialog-btn",function(e) {
		e.preventDefault();
		var form = $("#dialog-save-response-form");
			$.ajax({
				type: form.attr("method"),
	            url: form.attr("action"),
	            data: form.serialize(),
	            dataType : "json",
	            success: function (response) {
	               //location.reload();
	               if(response.code == 200) {
	               	  toastr['success']('data updated successfully!');
	               	  updateBoxEvent(form.find("#parent_id_form").val());
	               	  //window.location.replace(response.redirect);
	               }else{
	               	  toastr['error']('data is not correct or duplicate!');
	               } 
	            },
	            error: function () {
	               toastr['error']('Could not change module!');
	            }
	        });
	});

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
