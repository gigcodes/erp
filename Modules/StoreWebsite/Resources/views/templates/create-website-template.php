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
		      <div class="form-group">
		         <label for="magento_url">Magento Url</label>
		         <input type="text" name="magento_url" value="{{if data}}{{:data.magento_url}}{{/if}}" class="form-control" id="magento_url" placeholder="Enter magento url">
		      </div>
		      <div class="form-group">
		         <label for="magento_username">Magento username</label>
		         <input type="text" name="magento_username" value="{{if data}}{{:data.magento_username}}{{/if}}" class="form-control" id="magento_username" placeholder="Enter Username">
		      </div>
		      <div class="form-group">
		         <label for="magento_password">Magento Password</label>
		         <input type="text" name="magento_password" value="{{if data}}{{:data.magento_password}}{{/if}}" class="form-control" id="magento_password" placeholder="Enter Password">
		      </div>
		      <div class="form-group">
		         <label for="facebook">Facebook</label>
		         <input type="text" name="facebook" value="{{if data}}{{:data.facebook}}{{/if}}" class="form-control" id="facebook" placeholder="Enter facebook profle">
		      </div>
		      <div class="form-group">
		         <label for="facebook_remarks">Facebook Remarks</label>
		         <textarea name="facebook_remarks" class="form-control" id="facebook_remarks" placeholder="Enter facebook remarks">{{if data}}{{:data.facebook_remarks}}{{/if}}</textarea>
		      </div>
		      <div class="form-group">
		         <label for="instagram">Instagram</label>
		         <input type="text" name="instagram" value="{{if data}}{{:data.instagram}}{{/if}}" class="form-control" id="instagram" placeholder="Enter instagram profile">
		      </div>
		      <div class="form-group">
		         <label for="instagram_remarks">Instagram Remarks</label>
		         <textarea name="instagram_remarks" class="form-control" id="instagram_remarks" placeholder="Enter instagram remarks">{{if data}}{{:data.instagram_remarks}}{{/if}}</textarea>
		      </div>
		      <div class="form-group">
		         <label for="cropper_color">Cropper color</label>
		         <input type="text" name="cropper_color" value="{{if data}}{{:data.cropper_color}}{{/if}}" class="form-control" id="cropper_color" placeholder="Enter cropper color">
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