<script type="text/x-jsrender" id="template-result-block">
<style>
table {
  table-layout: fixed;
  border-collapse: collapse;
  width: 100%;
}
td {
  border: 1px solid #000;
  width: 150px;
  word-break: break-all;
}
td+td {
  width: auto;
}

</style>
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th style="width:50px;">User ID</th> 
		      	<th style="width:200px;">User</th> 
				<th>Rate per hour</th>
				<th>Salaried or fixed price</th>
				<th>TASKS</th>
				<th>Yesterday hours</th>
				<th>Online now</th> 
				<th>Payment frequency</th> 
				<th>Payment Due</th>
				<th>Due date</th> 
										
				<th>Paid on</th>
				<?php if(Auth::user()->isAdmin()) { ?>
				<th style="width:200px;">Send</th>
				<th style="width:200px;">Action</th>
				<?php } ?>
			</tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>
					  <div class="row">
							<div class="col-md-12">
								<div class="col-md-12 text-primary">
										<span>{{:prop.name}}</span><br>
										<span>{{:prop.email}}</span><br>
										<span>{{:prop.phone}}</span><br>

										{{if prop.team_leads}}
										{{props prop.team_leads}}
											{{if prop.name}}
											<span data-id="{{:prop.id}}" class="load-team-modal"><span><strong>Team :</strong> {{:prop.name}}</span></span><br>
											{{else}}
											<span data-id="{{:prop.id}}" class="load-team-modal"><span><strong>Team :</strong> No name</span></span><br>
											{{/if}}
										{{/props}}
										{{/if}}

									<?php if(Auth::user()->isAdmin()) { ?>
									{{if prop.is_active == 1}}
									<button type="button" class="btn btn-image change-activation" data-id="{{:prop.id}}"><img src="/images/do-disturb.png" /></button>
									{{else}}
									<button type="button" class="btn btn-image change-activation" data-id="{{:prop.id}}"><img src="/images/do-not-disturb.png" /></button>
									{{/if}}
									<button type="button" class="btn btn-image load-team-add-modal" data-id="{{:prop.id}}"><img src="/images/add.png" /></button>
									<?php } ?>
								</div> 
							</div>   
						</div>    	  
					  </td>
			        <td>{{:prop.hourly_rate}} {{:prop.currency}}</td>
			        <td> {{if prop.fixed_price_user_or_job == 1}} Salaried {{else prop.fixed_price_user_or_job == 2}} Fixed price Job {{/if}}</td>
			        <td>
						<a href="#" class="load-task-modal" data-id="{{:prop.id}}">Task</a>
					</td>
			        <td>{{:prop.yesterday_hrs}}</td>
			        <td></td>
			        <td>{{:prop.payment_frequency}}</td>
			        <td> {{:prop.previousDue}} {{:prop.currency}}</td>
			        <td>{{:prop.nextDue}}</td>
			        <td>
					{{:prop.lastPaidOn}}
					</td>
					<?php if(Auth::user()->isAdmin()) { ?>
					<td>
						<div class="row">
							<div class="col-md-12">
								<div class="d-flex">
									<input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
									<button class="btn btn-sm btn-image send-message" data-userid="{{:prop.id}}"><img src="/images/filled-sent.png"/></button>
								</div>
						</div>
						<div style="margin-top:5px;" class="col-md-12">
								<div class="d-flex">
									<select name="quickComment" class="form-control quickComment select2-quick-reply" "style" => "width:100%">
									<option value="">--Auto Reply--</option>
									{{props replies}}
										<option value="">{{>prop}}</option>
									{{/props}}
									</select>
									<a class="btn btn-image delete_quick_comment"><img src="/images/delete.png" style="cursor: default; width: 16px;"></a>
								</div>
							</div> 
						</div>  
						
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
					<?php } ?>
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
