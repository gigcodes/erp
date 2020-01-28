<script type="text/x-jsrender" id="template-create-website">
	<form name="form-create-website" method="post">
		<?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
		      <div class="form-row">
		         {{if data}}
		         	<input type="hidden" name="id" value="{{:data.id}}"/>
		         {{/if}}
		         <div class="form-group col-md-6">
		            <label for="title">Title</label>
		            <input type="text" name="title" value="{{if data}}{{:data.title}}{{/if}}" class="form-control" id="title" placeholder="Enter Title">
		         </div>
		         <div class="form-group col-md-6">
		            <label for="website">Website</label>
		            <input type="text" name="website" value="{{if data}}{{:data.website}}{{/if}}" class="form-control" id="website" placeholder="Enter Website">
		         </div>
		      </div>
		      <div class="form-group">
		         <label for="description">Description</label>
		         <input type="text" name="description" value="{{if data}}{{:data.description}}{{/if}}" class="form-control" id="description" placeholder="Enter Description">
		      </div>
		      <div class="form-group">
		         <label for="remote_software">Remote software</label>
		         <input type="text" name="remote_software" value="{{if data}}{{:data.remote_software}}{{/if}}" class="form-control" id="remote_software" placeholder="Enter Remote software">
		      </div>
		      <div class="form-row">
		         <div class="form-group col-md-4">
		            <label for="inputState">Is Published?</label>
		            <select name="is_published" id="inputState" class="form-control">
		               <option {{if data && data.is_published == 0}}selected{{/if}} value="0">No</option>
		               <option {{if data && data.is_published == 1}}selected{{/if}} value="1">Yes</option>
		            </select>
		         </div>
		      </div>
		   </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      <button type="button" class="btn btn-primary submit-store-site">Save changes</button>
		   </div>
		</div>
	</form>  	
</script>