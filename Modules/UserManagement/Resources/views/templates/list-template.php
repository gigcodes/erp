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
				<th>Salaried or fixed price</th>
				<th>TASKS</th>
				<th>Yesterday hours</th>
				<th>Online now</th> 
				<th>Payment frequency</th> 
				<th>Payment Due</th>
				<th>Due date</th> 
				<th>Paid on</th>
				<th>Action</th>
				
			</tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.name}} <br>
					  
						{{if prop.is_active == 1}}
						<a title="Add Permission" class="btn btn-secondary change-activation" data-id="{{:prop.id}}">Active</a>

						{{else}}
						<a title="Add Permission" class="btn btn-danger change-activation" data-id="{{:prop.id}}">Not Active</a>

						{{/if}}
					  </td>
			        <td>{{:prop.email}}</td>
			        <td>{{:prop.phone}}</td>
			        <td>{{:prop.hourly_rate}} {{:prop.currency}}</td>
			        <td> {{if prop.fixed_price_user_or_job == 1}} Salaried {{else prop.fixed_price_user_or_job == 2}} Fixed price Job {{/if}}</td>
			        <td>
						<p>Devtask: C-{{:prop.completedDevTask}}/P-{{:prop.pendingDevtask}} </p>
						<p>Task: C-{{:prop.completedTask}}/P-{{:prop.pendingTask}}</p>
						<p>Issue: C-{{:prop.completedIssue}}/P-{{:prop.pendingIssue}}</p>
					</td>
			        <td>{{:prop.yesterday_hrs}}</td>
			        <td></td>
			        <td>{{:prop.payment_frequency}}</td>
			        <td> {{:prop.previousDue}} {{:prop.currency}}</td>
			        <td>{{:prop.nextDue}}</td>
			        <td>
					
					</td>
			        <td>
					<button data-toggle="tooltip" type="button" class="btn btn-xs btn-image load-communication-modal" data-object='user' data-id="{{:prop.id}}" title="Load messages">
					<img src="/images/chat.png" data-is_admin="<?php echo Auth::user()->hasRole('Admin'); ?>" data-is_hod_crm="<?php echo Auth::user()->hasRole('HOD of CRM'); ?>" alt="">
					</button>
					{{if prop.id == <?php echo Auth::id(); ?>}}
					<a class="btn btn-image" href="#"><img src="/images/view.png"/></a>
					{{else}}
					<a class="btn btn-image" onclick="editUser({{>prop.id}})"><img src="/images/edit.png"/></a>
					{{/if}}
					<a href="/user-management/track/{{>prop.id}}">Info</a>
					<a title="Payments" class="btn btn-image" onclick="payuser({{>prop.id}})"><span class="glyphicon glyphicon-usd"></span></a>
					
					<a title="Add role" class="btn btn-image load-role-modal" data-id="{{:prop.id}}"><img src="/images/role.png" alt=""></a>
					<a title="Add Permission" class="btn btn-image load-permission-modal" data-id="{{:prop.id}}"><i class="fa fa-lock" aria-hidden="true"></i></a>
					</td>
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
