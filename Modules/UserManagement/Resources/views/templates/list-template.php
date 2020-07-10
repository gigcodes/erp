<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>User ID</th> 
		      	<th>User</th> 
				<th>Email</th> 
				<th>Phone</th> 
				<th>Rate per hour</th>
				<th>Current task</th>
				<th>Next task</th> 
				<th>Yesterday hours</th>
				<th>Online now</th> 
				<th>Payment frequency</th> 
				<th>Last payment</th>
				<th>Due date</th> 
				<th>Paid on</th>
				
			</tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.name}}</td>
			        <td>{{:prop.email}}</td>
			        <td>{{:prop.phone}}</td>
			        <td>{{:prop.hourly_rate}} {{:prop.currency}}</td>
			        <td>{{:prop.current_task}}</td>
			        <td>{{:prop.next_task}}</td>
			        <td>{{:prop.yesterday_hrs}}</td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>

<script type="text/x-jsrender" id="template-attached-remarks">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Remark</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="goal_id" value="{{:data.goal_id}}">
					  	<div class="row">
					    	<div class="col">
					      		<textarea class="form-control" name="remark"></textarea>
					      	</div>
					    	<div class="col">
					      		<button class="btn btn-secondary add-attached-remark">ADD</button>
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
					        <th>Remark</th>
					        <th>Created At</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data.remarks}}
						      <tr>
						      	<td>{{:prop.id}}</td>
						        <td>{{:prop.remark}}</td>
						        <td>{{:prop.created_at}}</td>
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
