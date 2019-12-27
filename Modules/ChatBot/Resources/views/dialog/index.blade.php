@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Dialog | Chatbot')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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
	#tree-container-dialog ul {
		list-style-type: none;
	}
	#dialog-tree.node-children {
	    margin: 0 32px 32px 34px;
	}
	.wc--body * {
	    box-sizing: border-box;
	}
	.node-children {
	    list-style: none;
	    margin: 0;
	}
	.node-children>.node-child>.node-container {
	    position: relative;
	    margin: 0 0 16px 16px;
	}
	.node {
	    position: relative;
	    display: flex;
	    outline: none;
	    width: 460px;
	    border: 1px solid #8897a2;
	    background-color: #fff;
	    height: 90px;
	}
	.node__expander {
	    width: 36px;
	    min-width: 36px;
	}
	.node__expander>button {
	    border: none;
	    background-color: transparent;
	    cursor: pointer;
	    height: 100%;
	    width: 100%;
	    vertical-align: middle;
	    text-align: center;
	    border-right: 1px solid #8897a2;
	}
	.node__expander>button>svg {
	    fill: #3d70b2;
	}
	.node__contents {
	    display: flex;
	    flex-direction: column;
	    flex: 1;
	    padding: 8px 16px;
	    max-width: 400px;
	    text-align: left;
	    color: #5a6872;
	    cursor: pointer;
	}
	.node__summary {
	    height: 64px;
	}
	.node__text {
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    overflow: hidden;
	    margin-bottom: 4px;
	    line-height: 1.2;
	}
	.node__subtext {
	    font-size: 0.75em;
	    line-height: 1.5em;
	    color: #5a6872;
	    white-space: nowrap;
	    text-overflow: ellipsis;
	    overflow: hidden;
	}
	.bx--overflow-menu {
	    position: relative;
	    width: 1.25rem;
	    height: 2.375rem;
	    cursor: pointer;
	}
	.bx--overflow-menu__icon {
	    width: 100%;
	    height: 100%;
	    padding: 0.5rem;
	    fill: #5a6872;
	}
	.node-children>.node-child>.node-container::before {
	    content: '';
	    position: absolute;
	    display: block;
	    border-left: 2px solid #8897a2;
	    color: #41d6c3;
	    width: 0;
	    height: 18px;
	    left: -23px;
	    top: -17px;
	   } 
	.node-children>.node-child:not(:last-of-type)>.node-container::after {
	    content: '';
	    position: absolute;
	    display: block;
	    border-left: 2px solid #8897a2;
	    width: 0;
	    height: 100%;
	    left: -23px;
	    top: 16px;
	}
	.node::before {
    content: '';
    display: block;
    border-bottom: 2px solid #8897a2;
    border-left: 2px solid #8897a2;
    border-bottom-left-radius: 50%;
    width: 22px;
    height: 47px;
    left: -24px;
    top: 0;
    padding: 0;
    position: absolute;
}
.node-container .node--expanded ~ .node-children {
    display: block;
}
.node-container .node-children {
    margin-top: 16px;
    margin-left: 28px;
}
.bx--overflow-menu--flip {
    left: -140px;
}
.bx--overflow-menu-options--open {
    display: flex;
}
.bx--overflow-menu-options {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.1);
    display: none;
    flex-direction: column;
    align-items: flex-start;
    position: absolute;
    z-index: 10000;
    background-color: #fff;
    border: 1px solid #dfe3e6;
    width: 11.25rem;
    list-style: none;
    margin-top: .25rem;
    padding: .25rem 0 .5rem;
    left: -20px;
}
.bx--overflow-menu-options__option {
    display: flex;
    background-color: transparent;
    align-items: center;
    width: 100%;
    padding: 0;
}
.bx--overflow-menu-options__btn {
    font-size: 0.875rem;
    font-family: "ibm-plex-sans",Helvetica Neue,Arial,sans-serif;
    font-weight: 400;
    width: 100%;
    height: 100%;
    border: none;
    display: inline-block;
    background-color: transparent;
    text-align: left;
    padding: .5rem 1rem;
    cursor: pointer;
    color: #152934;
    max-width: 11.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bx--overflow-menu-options--open {
    display: flex;
}

@media screen and (min-width: 992px) {
	.node-editor--open {
		min-width: 810px;
	}
}


.node-editor {
    width: 100%;
    background-color: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    overflow-x: hidden;
    z-index: 100;
    max-width: 0;
    min-width: 0;
}

/*.node-editor--open {
    max-width: 810px;
    min-width: 100%;
}

.dialog-tree-container {
    margin-top: .5rem;
    margin-left: 1rem;
}
.dialog-tree-container {
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1 1 auto;
}*/
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

<script id="dialog-leaf-editor" type="text/x-jsrender">

</script>	

<script id="dialog-leaf-button-options" type="text/x-jsrender">
	<ul class="bx--overflow-menu-options bx--overflow-menu--flip bx--overflow-menu-options--open" tabindex="-1" role="menu">
	  	<li class="bx--overflow-menu-options__option" role="menuitem">
	  		<button class="bx--overflow-menu-options__btn" tabindex="-1" role="add_child">Add child node</button>
	  	</li>
	  	<li class="bx--overflow-menu-options__option" role="menuitem">
	  		<button class="bx--overflow-menu-options__btn" tabindex="-1" role="add_above">Add node above</button>
	  	</li>
	  	<li class="bx--overflow-menu-options__option" role="menuitem">
	  		<button class="bx--overflow-menu-options__btn" tabindex="-1" role="add_below">Add node below</button>
	  	</li>
	  	<li class="bx--overflow-menu-options__option bx--overflow-menu--divider bx--overflow-menu-options__option--danger" role="menuitem">
	  		<button class="bx--overflow-menu-options__btn" tabindex="-1" role="delete">Delete</button>
	  	</li>
	</ul>
</script>	
	
<script id="dialog-leaf" type="text/x-jsrender">
	<li class="node-child">
	  <div class="node-container node--selected-sibling">
	     <div id="Weather_Hot" class="node">
	        <div class="node__expander">
	           <button id="node-expander-Weather_Hot" type="button">
	              <svg width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd"><path d="M0 10.6L4.7 6 0 1.4 1.4 0l6.1 6-6.1 6z"></path></svg>
	           </button>
	        </div>
	        <div class="node__contents">
	           <div class="node__summary">
	              <div class="node__text">Test</div>
	              <div class="node__subtext">#Weather_Hot</div>
	           </div>
	           <div dir="ltr" class="node__subtext"><span>1 Responses</span><span> / 0 Context Set</span><span></span><span></span><span> / Does not return</span></div>
	        </div>
	        <div class="node__menu">
	           <div role="button" data-has-pop=false class="bx--overflow-menu" id="node__options-menu-Weather_Hot" tabindex="0">
	              <svg class="bx--overflow-menu__icon" fill-rule="evenodd" height="15" role="img" viewBox="0 0 3 15" width="3" focusable="false" aria-label="Node options" alt="Node options">
	                 <title>Node options</title>
	                 <path d="M0 1.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 1 1-3 0M0 7.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 1 1-3 0M0 13.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 1 1-3 0"></path>
	              </svg>
	           </div>
	        </div>
	     </div>
	     <ul class="node-children">
	     	
	     </ul>
	  </div>
	</li>
</script>

<script id="multiple-response-condition" type="text/x-jsrender">
	<div class="form-row">
		<div class="form-group col-md-3">
	      <select name="condition" class="form-control">
	      	<option value="and">AND</option>
	      	<option value="or">OR</option>
	      </select>	
	      <small id="emailHelp" class="form-text text-muted">IF ASSISTANT RECOGNIZES</small>
	  	</div>
	  	<div class="form-group col-md-9">
	      <input class="form-control" id="value" placeholder="Enter a response" name="value" type="text">
	  	</div>
	</div>
</script>
<script id="single-response-condition" type="text/x-jsrender">
	<div class="form-row">
		<div class="form-group col-md-9">
	      <input class="form-control" id="value" placeholder="Enter a response" name="value" type="text">
	    </div>
	</div>
</script>

<script id="add-more-condition" type="text/x-jsrender">
	<div class="form-row">
		<div class="form-group col-md-3">
	      <select name="condition" class="form-control">
	      	<option value="and">AND</option>
	      	<option value="or">OR</option>
	      </select>
	  	</div>
	  	<div class="form-group col-md-3">
	      <input class="form-control" id="value" placeholder="Enter Condition" name="value" type="text">
	  	</div>
	  	<div class="form-group col-md-3">
		  	<a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn">
	          <span class="glyphicon glyphicon-plus"></span> 
	        </a>
	        <a href="javascript:;" class="btn btn-secondary btn-sm remove-more-condition-btn">
	          <span class="glyphicon glyphicon-minus"></span> 
	        </a>	
	  	</div>
	</div>
</script>	

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
        <form action="http://pravin.sololux/chatbot/keyword/11/edit" method="post">
			<input type="hidden" name="_token" value="OPxcvWgELBT3BBa971puWtvlq6WLizb79gIYVcth">					  
			<div class="form-row">
			    <div class="form-group col-md-9">
			      <input class="form-control" id="keyword" placeholder="Enter your keyword" name="keyword" type="text" value="Bags">					    
			      <small id="emailHelp" class="form-text text-muted">Node name will be shown to customers for disambiguation so use something descriptive</small>
			  	</div>
			  	<div class="col-md-3">
			  		<input type="checkbox" class="multiple-conditioned-response" checked data-toggle="toggle">
			  		<small id="emailHelp" class="form-text text-muted">Multiple conditioned responses</small>
			  	</div>		
			</div>
			<hr>
				<h4>If assistant recognizes</h4>
			<hr>
			<div class="form-row">
			    <div class="form-group col-md-3">
			      <input class="form-control" id="value" placeholder="Enter Condition" name="value" type="text">
			    </div>
			  	<div class="form-group col-md-3">
				  	<a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn">
			          <span class="glyphicon glyphicon-plus"></span> 
			        </a>	
			  	</div>
			</div>
			<div class="show-more-conditions">
			</div>	
			<hr>
				<h4>Assistant responds</h4>
			<hr>
			<div class="assistant-response-based">
				<div class="form-row">
					<div class="form-group col-md-9">
				      <input class="form-control" id="value" placeholder="Enter a response" name="value" type="text">
				    </div>
				</div>
			</div>	
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

@include('chatbot::partial.create_dialog')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript">

	var myTmpl = $.templates("#dialog-leaf");
	var html = myTmpl.render({});
	var dilogTree = $("#dialog-tree");
		dilogTree.html(html);

	$(document).on("click",".node__contents",function(e) {
	   $("#leaf-editor-model").modal("show");
	});

	$(document).on("click",".add-more-condition-btn",function(){
		var buttonOptions = $.templates("#add-more-condition");
		$(".show-more-conditions").append(buttonOptions.render({}));
	});	

	$(document).on("click",".remove-more-condition-btn",function(){
		$(this).closest(".form-row").remove();
	});

	$(document).on("change",".multiple-conditioned-response",function() {
		var hasChecked = $(this).prop('checked');
		if(hasChecked == true) {
			var tmpl = $.templates("#multiple-response-condition");
			$(".assistant-response-based").html(tmpl.render({}));
		}else{
			var tmpl = $.templates("#single-response-condition");
			$(".assistant-response-based").html(tmpl.render({}));
		}
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

	$(document).on("click",".bx--overflow-menu-options > li",function() {
		var buttonRole = $(this).find("button").attr("role");

			if(buttonRole == "add_child") {
			  var main = $(this).closest(".node-child");
			  var space = main.find(".node-children");
			  	  space.append(myTmpl.render({}));	

			}else if(buttonRole == "add_above") {
				var main = $(this).closest(".node-child");
				    main.before(myTmpl.render({}));

			}else if(buttonRole == "add_below") {

				var main = $(this).closest(".node-child");
				    main.after(myTmpl.render({}));

			}else if(buttonRole == "delete") {
				var main = $(this).closest(".node-child");
					main.remove();
			}

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
