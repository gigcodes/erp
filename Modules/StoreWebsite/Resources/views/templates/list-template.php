<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="2%">Id</th>
		        <th width="5%">Title</th>
		        <th width="10%">Website</th>
		        <th width="2%">Remote software</th>
		        <th width="5%">Facebook</th>
		        <th width="5%">Instagram</th>
		        <th width="3%">Published</th>
				<th width="3%">FCM Key</th>
				<th width="3%">FCM Id</th>
				<th width="3%">Icon</th>
				<th width="5%">Created At</th>
		        <th width="10%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td>{{:prop.title}}</td>
			        <td>
			        	<b>Website : </b>{{:prop.website}}<br>
			        	<b>Country Duty : </b>{{:prop.country_duty}}<br>
			        	<b>Description : </b>{{:prop.description}}<br>
			        </td>
			        <td>{{:prop.remote_software}}</td>
			        <td>
			        	{{:prop.facebook}}
			        	<br>
			        	<i class="fa fa-comment show-facebook-remarks" data-id="{{>prop.id}}" data-value="{{:prop.facebook_remarks}}"></i>
			        </td>
			        <td>{{:prop.instagram}}
			        	<br>
			        	<i class="fa fa-comment show-instagram-remarks" data-id="{{>prop.id}}" data-value="{{:prop.instagram_remarks}}"></i>
			        </td>
					<td>{{if prop.is_published == 1}}Yes{{else}}No{{/if}}</td>
					<td>{{if prop.push_web_key !=null}} {{:prop.push_web_key.substring(0, 10)+'..'}} {{else}} {{/if}} </td>
					<td>{{if prop.push_web_id !=null}} {{:prop.push_web_id.substring(0, 10)+'..'}} {{else}} {{/if}} </td>
					<td>{{if prop.icon !=null}} <img width="25px" height="25px" alt="" src="{{:prop.icon}}""> {{else}} {{/if}} </td>
			        <td>{{:prop.created_at}}</td>
			        <td>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-attach-category">
			        		<i class="fa fa-paperclip" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-attach-brands">	
			        		<img width="15px" title="Attach Brands" src="/images/purchase.png">
			        	</button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn">
			        		<a href="/site-development/{{>prop.id}}">
			        			<img width="15px" title="Site Development" src="/images/project.png">
			        		</a>
						</button> 
						<button type="button" data-id="{{>prop.id}}" class="btn"><a href="/store-website/{{>prop.id}}/goal"><i class="fa fa-bullseye"></i></a></button> 
						<button title="Social media strategy" type="button"  class="btn"><a href="/store-website/{{>prop.id}}/social-strategy"><i class="fa fa-fa"></i></a></button> 
						<button title="Seo Format" data-id="{{>prop.id}}" type="button"  class="btn btn-seo-format"><a href="javascript:;"><i class="fa fa-external-link"></i></a></button> 
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>

<script type="text/x-jsrender" id="template-attached-category">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="store_website_id" value="{{:store_website_id}}">
					  	<div class="row">
					    	<div class="col">
					      		{{:scdropdown}}
					      	</div>
					    	<div class="col">
					      		<input type="text" class="form-control" name="remote_id" placeholder="Remote Id">
					    	</div>
					    	<div class="col">
					      		<button class="btn btn-secondary add-attached-category">ADD</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
			<div class="row mt-5 preview-category">
			</div>	
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>No</th>
					        <th>Name</th>
					        <th>Remote id</th>
					        <th>Created At</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr>
						      	<td>{{:prop.id}}</td>
						        <td>{{:prop.title}}</td>
						        <td>{{:prop.remote_id}}</td>
						        <td>{{:prop.created_at}}</td>
						        <td>
						        	<button type="button" data-store-website-id="{{>prop.store_website_id}}" data-id="{{>prop.id}}" class="btn btn-delete-store-website-category"><i class="fa fa-trash" aria-hidden="true"></i></button>
						        </td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
					{{:pagination}}
				</div>	
			</div>
		</div>
	</div>			
</script>

<script type="text/x-jsrender" id="template-attached-brands">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="store_website_id" value="{{:store_website_id}}">
					  	<div class="row">
					    	<div class="col">
					    		<select class="form-control" name="brand_id">
					    			{{props brands}}
					    				<option value="{{>key}}">{{>prop}}</option>
					    			{{/props}}		
					    		</select>
					      	</div>
					    	<div class="col">
					      		<input type="text" class="form-control" name="markup" placeholder="Mark up percentage">
					    	</div>
					    	<div class="col">
					      		<button class="btn btn-secondary add-attached-brands">ADD</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>	
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>No</th>
					        <th>Name</th>
					        <th>Markup</th>
					        <th>Created At</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr>
						      	<td>{{:prop.id}}</td>
						        <td>{{:prop.name}}</td>
						        <td>{{:prop.markup}}</td>
						        <td>{{:prop.created_at}}</td>
						        <td>
						        	<button type="button" data-store-website-id="{{>prop.store_website_id}}" data-id="{{>prop.id}}" class="btn btn-delete-store-website-brand"><i class="fa fa-trash" aria-hidden="true"></i></button>
						        </td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
					{{:pagination}}
				</div>	
			</div>
		</div>
	</div>			
</script>
<script type="text/x-jsrender" id="template-category-list">
	<div class="col-lg-12">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th><input type="checkbox" class="select-all-preview-category"></th>
		      	<th>Title</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr id="preview-category-{{:prop.id}}">
			      	<td><input class="preview-checkbox" type="checkbox" name="push_category" value="{{:prop.id}}"></td>
			      	<td>{{:prop.title}}</td>
			        <td>
			        	<button type="button" data-category-id="{{:prop.id}}" class="btn btn-delete-preview-category">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
	</div>
	<div class="col-lg-12">
		<button class="btn btn-secondary save-preview-categories"><i class="fa fa-save"></i> Save</button>
	</div>			
</script>

<script type="text/x-jsrender" id="template-update-remarks">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Edit Remarks</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:id}}">
					  	<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">
						         	<label for="{{:field}}">{{if field == "facebook_remarks"}}Facebook{{else}}Instagram{{/if}} Remarks</label>
						         	<textarea name="{{:field}}" class="form-control" id="facebook_remarks" placeholder="Enter {{:field}}">{{if field}}{{:remarks}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button class="btn btn-secondary update-remark-btn">Update</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/x-jsrender" id="template-edit-seo">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Edit Seo Format</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:data.store_website_id}}">
					  	<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_title">Meta Title</label>
						         	<textarea name="meta_title" class="form-control" id="meta_title" placeholder="Enter Meta Title">{{if data}}{{:data.meta_title}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_description">Meta Description</label>
						         	<textarea name="meta_description" class="form-control" id="meta_description" placeholder="Enter Meta Description">{{if data}}{{:data.meta_description}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_keyword">Meta Keywords</label>
						         	<textarea name="meta_keyword" class="form-control" id="meta_keyword" placeholder="Enter Meta Keywords">{{if data}}{{:data.meta_keyword}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button data-id="{{:data.store_website_id}}" class="btn btn-secondary update-seo-format">Update</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>
