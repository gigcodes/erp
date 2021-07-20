<script type="text/x-jsrender" id="template-create-group">
	<form name="form-create-group" id="form-create-group" method="post">
		<?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">{{if data.type = 'edit'}} Edit Website Group View {{else}}Create Website Group{{/if}}</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      	<span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
		      <div class="form-row">
		         {{if data.type == 'edit'}}
		         	<input type="hidden" name="id" value="{{:data.id}}"/>
		         	<input type="hidden" name="row_id" value="{{:data.row_id}}"/>
		         {{else}}
		         	<input type="hidden" name="row_id" value="{{:data.row_id}}"/>
		         {{/if}}
		         
		      </div>
		      <div class="form-group col-md-12">
	            <label for="name">Name</label>
	            <input type="text" name="name" value="{{if data.type == 'edit'}}{{:data.name}}{{/if}}" class="form-control" id="name" placeholder="Enter Name"> 
	         </div> 

			 <div class="form-group col-md-12">
		         	<label for="code" class="form-group">Agents Priorities</label> 
				<button type="button" title="Create" data-id="" class="btn btn-add-priority">
					<i class="fa fa-plus" aria-hidden="true"></i>
				</button>
			</div>
			
			</div> 

		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      <button type="button" class="btn btn-primary submit-group">Save changes</button>
		   </div>
		</div>
	</form>  	
</script>