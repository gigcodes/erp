<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="2%">Id</th>
		      	<th width="30%">Product</th>
		        <th width="10%">New category</th>
		        <th width="10%">Old category</th>
		        <th width="10%">Updated by</th>
		        <th width="10%">Created</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>[{{:prop.product_id}}] {{:prop.product_name}}</td>
			      	<td>{{:prop.new_cat_name}}</td>
			        <td>{{:prop.old_cat_name}}</td>
			        <td>{{:prop.user_name}}</td>
			        <td>{{:prop.created_at}}</td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>


<script type="text/x-jsrender" id="template-merge-category">
<div class="modal-content">
   <div class="modal-header">
      <h5 class="modal-title">Merge Category</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      	<span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
		<div class="row">
			<div class="col-lg-12">
				<form>
					<?php echo csrf_field(); ?>
					<div class="row">
				  		<div class="col-md-12">
				    		<div class="form-group">
					         	<?php echo Form::select("merge_category",\App\VendorCategory::pluck("title","id")->toArray(),null,["class" => "form-control select2-vendor-category merge-category"]); ?>
					         </div>
				        </div> 
				        <div class="col-md-12">
					    	<div class="form-group">
					      		<button class="btn btn-secondary merge-category-btn">Merge and Delete</button>
					    	</div>
				    	</div>
				  	</div>
				</form>
			</div>
		</div>
	</div>
</div>
</script>