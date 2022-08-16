<script type="text/x-jsrender" id="template-result-block">
	<style>
		table#reply_history_div {
			table-layout: fixed;
			border-collapse: collapse;
			width: 100%;
		}
		table#reply_history_div td {
			border: 1px solid #000;
			width: 150px;
			word-break: break-all;
		}
		table#reply_history_div td+td {
			width: auto;
		}
		body{
			font-size:13px;
		}
		.btn-image,.send-email-common-btn{
			padding:0;
		}
		a {
				color: #333;
		}
	</style>
	<div class="row">
		<table class="table table-bordered"style="table-layout:fixed;" id="reply_history_div">
			<thead>
				<tr>
					<th style="width:2%" >ID</th> 
					<th style="width:3%">User</th>
					<th style="width:2%">Rate</th>
					<th style="width:3%">S/F PX</th>
					<th style="width:2%">S N</th> 
					<th style="width:3%">Tsk</th>
					<th style="width:2%">Y h</th>
					<th style="width:3%">N T e.</th>
					<th style="width:3%">Ov tsk</th> 
					<th style="width:3%">Las s</th>
					<th style="width:3%">P Due</th>
					<th style="width:3%">Due d</th> 			
					<th style="width:3%">Pai on</th>
					<th style="width:1%">S</th>
					<?php if (Auth::user()->isAdmin()) { ?>
					<th style="width:3%">Send</th>
					<th style="width:3%">Reply</th>
					<?php } ?>
					<th style="width:19%">Action</th>
				</tr>
			</thead>
		    <tbody>
		    	{{props data}}
						<tr>
							<td class="Website-task"title="{{:prop.id}}">{{:prop.id}}</td>
							<td class="Website-task "><a class="Website-task btn-image load-userdetail-modal" data-id="{{:prop.id}}">{{:prop.name}}</a></td>
							<td class="Website-task"title="{{:prop.hourly_rate}} {{:prop.currency}}"> {{:prop.hourly_rate}} {{:prop.currency}}</td>
							<td class="Website-task"title="Fixed price Job">{{if prop.fixed_price_user_or_job == 1}} Fixed price Job {{else prop.fixed_price_user_or_job == 2}} Hourly Per Task {{else prop.fixed_price_user_or_job == 3}} Salaried  {{/if}}</td>
							<td class="number">
								<select class="form-control ui-autocomplete-input whatsapp_number" data-user-id="{{:prop.id}}">
									<option>-- Select --</option>
									<?php foreach (($whatsapp ?? []) as $key => $value) { ?>
										<option {{if prop.whatsapp_number == "<?php echo $value->number; ?>" }} selected='selected' {{/if}} value="<?php echo $value->number; ?>"><?php echo $value->number; ?></option>
									<?php } ?>
								</select>
							</td>
							<td class="Website-task"title="{{:prop.pending_tasks}}/{{:prop.total_tasks}}"><a href="#" class="load-task-modal" data-id="{{:prop.id}}">{{:prop.pending_tasks}}/{{:prop.total_tasks}}</a></td>
							<td class="Website-task" title="{{:prop.yesterday_hrs}}">{{:prop.yesterday_hrs}}</td>
							<td class="Website-task"title="{{:prop.no_time_estimate}}">{{:prop.no_time_estimate}}</td>
							<td>{{:prop.overdue_task}}</td>
							<td class="Website-task"title="{{:prop.online_now}}"> <span class="today-history" data-id="{{:prop.id}}"> {{:prop.online_now}} </span> </td>
							<td class="Website-task" title="{{:prop.previousDue}} {{:prop.currency}}"> {{:prop.previousDue}} {{:prop.currency}}</td>
							<td class="Website-task" title="{{:prop.nextDue}}">{{:prop.nextDue}}</td>
							<td class="Website-task"title="{{:prop.lastPaidOn}}">{{:prop.lastPaidOn}}</td>
							<td class="Website-task"title=""><span class="user-status {{if prop.is_online}} is-online {{/if}}"></span></td>
							<?php if (Auth::user()->isAdmin()) { ?>
							<td class="Website-task" title="">
								<div class="row">
									<div class="col-md-12">
										<div class="d-flex">
											<input  type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
											<button class="btn btn-sm btn-image send-message" data-userid="{{:prop.id}}"><img src="/images/filled-sent.png"/></button>
										</div>
									</div>
								</div>
							</td>
							<td>					
								<div style="padding-left:0;" class="col-md-12 pr-0">
									<div class="d-flex">
										<select name="quickComment" class="form-control quickComment select2-quick-reply" "style" => "width:100px !important;">
											<option value="">--Auto Reply--</option>
											{{props replies}}
												<option value="">{{>prop}}</option>
											{{/props}}
										</select>
										<a  class="btn mt-2 btn-image delete_quick_comment"><img src="/images/delete.png" style="cursor: default; width: 16px;color:gray;"></a>
									</div>
								</div> 
							</td>
							<?php } ?>
							<td>
								<div>
									<?php if (Auth::user()->isAdmin()) { ?>
										<button data-toggle="tooltip" type="button" class="btn btn-xs btn-image load-communication-modal" data-object='user' data-id="{{:prop.id}}" title="Load messages" style="padding: 0px 1px;">
											<img src="/images/chat.png" data-is_admin="<?php echo Auth::user()->hasRole('Admin'); ?>" data-is_hod_crm="<?php echo Auth::user()->hasRole('HOD of CRM'); ?>" alt="">
										</button>
										{{if prop.id == <?php echo Auth::id(); ?>}}
											<a class="btn btn-image" href="#"><img src="/images/view.png"/style="padding: 0px 1px;"></a>
										{{else}}
											<a class="btn btn-image" onclick="editUser({{>prop.id}})"style="padding: 0px 1px;"><img src="/images/edit.png"/></a>
										{{/if}}
										<a href="/user-management/track/{{>prop.id}}"style="padding: 0px 1px;">Info</a>
										<a title="Payments" class="btn btn-image" onclick="payuser({{>prop.id}})"><span class="glyphicon glyphicon-usd"style="padding: 0px 1px;"></span></a>
										<a title="Add role" class="btn btn-image load-role-modal" data-id="{{:prop.id}}"><img src="/images/role.png" alt=""style="padding: 0px 1px;"></a>
										<a title="Add Permission" class="btn btn-image load-permission-modal" data-id="{{:prop.id}}"style="padding: 0px 1px;"><i class="fa fa-lock" aria-hidden="true"></i></a>
									<?php } ?>
									<!-- Pawan added for UserAvaibility -->
									<!-- <a title="View Avaibility" class="btn btn-image load-time-modal-view" data-id="{{:prop.id}}"style="padding: 0px 1px;"><i class="fa fa-eye" aria-hidden="true"></i></a> -->
									<!-- end -->
									<!-- <a title="Add Avaibility" class="btn btn-image load-time-modal" data-id="{{:prop.id}}"style="padding: 0px 1px;"><i class="fa fa-clock-o" aria-hidden="true"></i></a> -->
									<a title="Task Hours" class="btn btn-image load-tasktime-modal" data-id="{{:prop.id}}"style="padding: 0px 1px;"><i class="fa fa-tasks" aria-hidden="true"></i></a>
									<button type="button" class="btn send-email-common-btn" data-toemail="{{:prop.email}}" data-object="user" data-id="{{:prop.id}}" style="padding: 0px 1px;"><i class="fa fa-envelope-square"></i></button>
									{{if prop.team}}
										<span class="expand-row">
											<span class="div-team-mini">
												<span><span><strong> {{if prop.team.name}} {{:prop.team.name}} {{else}} 'Team' {{/if}} :</strong> ({{:prop.team_leads}})</span></span>
											</span>
											<span class="div-team-max hidden">
												{{if prop.name}}
													{{props prop.team_members}}
														<p style="margin:0px; "padding: 0px 1px;" class="search-team-member" data-keyword="{{:prop.name}}"> {{:prop.name}}</p>
													{{/props}}
												{{/if}}
											</span>
										</span>
										<br>
									{{/if}}
									<?php if (Auth::user()->isAdmin()) { ?>
										{{if prop.is_active == 1}}
											<button title="Deactive user" type="button" class="btn btn-image change-activation pd-5" data-id="{{:prop.id}}" style="padding: 0px 1px;"><img src="/images/do-disturb.png" /></button>
										{{else}}
											<button title="Activate user" type="button" class="btn btn-image change-activation pd-5" data-id="{{:prop.id}}" style="padding: 0px 1px;"><img src="/images/do-not-disturb.png" /></button>
										{{/if}}
										{{if !prop.user_in_team}}
											<button type="button" class="btn btn-image load-team-add-modal pd-5" data-id="{{:prop.id}}"><img src="/images/add.png" / style="padding: 0px 1px;"></button>
										{{/if}}
										{{if prop.team}}
											<button title="Edit Team" type="button" class="btn btn-image load-team-modal pd-5" data-id="{{:prop.id}}"><img src="/images/edit.png" / style="padding: 0px 1px;"></button>
										{{/if}}
									<?php } ?>
									<button title="View user avaibility" type="button" class="btn btn-image load-avaibility-modal pd-5" data-id="{{:prop.id}}" style="padding: 0px 1px;"> <i class="fa fa-check" aria-hidden="true"></i></button>
									{{if !prop.already_approved}}
										<button title="Approve user for the day" type="button" class="btn approve-user pd-5" data-id="{{:prop.id}}"style="padding: 0px 1px;"> <i class="fa fa-check-circle" aria-hidden="true"></i></button>
									{{/if}}
									<?php if (Auth::user()->isAdmin()) { ?>
										<button title="Create database" type="button" class="btn btn-create-database pd-5" data-id="{{:prop.id}}"style="padding: 0px 1px;"> <i class="fa fa-database" aria-hidden="true"></i></button>
									<?php } ?>
									<button title="Task acitivity" type="button" class="btn task-activity pd-5" data-id="{{:prop.id}}"><i class="fa fa-history"style="padding: 0px 1px;"></i></button>
									<?php if (Auth::user()->isAdmin()) { ?>
										<button title="generate pem file" class="btn user-generate-pem-file pd-5" data-userid="{{:prop.id}}"> <i class="fa fa-file" aria-hidden="true" style="padding: 0px 1px;"></i></button>
										<button title="Pem file History" class="btn user-pem-file-history pd-5" data-userid="{{:prop.id}}"> <i class="fa fa-info-circle" aria-hidden="true" style="padding: 0px 1px;"></i></button>
									<?php } ?>
									<button title="user feedback" id="exampleModal" data-user_id="{{:prop.id}}" class=" btn fa fa-comment feedback_btn user-feedback-modal" data-bs-target="#exampleModal" aria-hidden="true" style="padding: 0px 1px;"><i class="fa fa comment" aria-hidden="true"></i></button>
									<button type="button" title="Flagged for Plan Task" data-user_id="{{:prop.id}}" data-is_task_planned="{{:prop.is_task_planned}}" onclick="updateUserFlagForTaskPlan(this)" class="btn" style="padding: 0px 1px;">
										{{if prop.is_task_planned}}
											<i class="fa fas fa-toggle-on"></i>
										{{/if}}
										{{if !prop.is_task_planned}}
											<i class="fa fas fa-toggle-off"></i>
										{{/if}}
									</button>

									<div class="dropdown dropleft">
										<a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{:prop.id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												Actions
										</a>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{:prop.id}}">
												<a class="dropdown-item" href="javascript:void(0);" onclick="funUserAvailabilityList(this, '{{:prop.id}}')">User Availabilities</a>
										</div>
								</div>
								</div>
							</td>
						</tr>
					{{/props}}  
				</tbody>
		</table>
		{{:pagination}}
	</div>
</script>

<script type="text/x-jsrender" id="user-template-generate-file">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">User Generate PEM file</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form action="/user-management/user-generate-file-store" method="post">
						<?php echo csrf_field(); ?>
						<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">

					    				<input type="hidden" value={{:userid}} name="userid" id="user_id-pemfile">

						         	<label for="meta_title">Server List</label>
						         	<select class="form-control select2" name="for_server">
						         		<option value="Erp-Server">Erp-Server</option>
						         		<option value="s01">Scrap-Server-s01</option>
						         		<option value="s02">Scrap-Server-s02</option>
						         		<option value="s03">Scrap-Server-s03</option>
						         		<option value="s04">Scrap-Server-s04</option>
						         		<option value="s05">Scrap-Server-s05</option>
						         		<option value="s06">Scrap-Server-s06</option>
						         		<option value="s07">Scrap-Server-s07</option>
						         		<option value="s08">Scrap-Server-s08</option>
						         		<option value="s09">Scrap-Server-s09</option>
						         		<option value="s10">Scrap-Server-s10</option>
						         		<option value="s11">Scrap-Server-s11</option>
						         		<option value="s12">Scrap-Server-s12</option>
						         		<option value="s13">Scrap-Server-s13</option>
						         		<option value="s14">Scrap-Server-s14</option>
						         		<option value="s15">Scrap-Server-s15</option>
						         		<option value="Cropper-Server">Cropper-Server</option>
						         		<option value="BRANDS">BRANDS</option>
						         		<option value="AVOIRCHIC">AVOIRCHIC</option>
						         		<option value="OLABELS">OLABELS</option>
						         		<option value="SOLOLUXURY">SOLOLUXURY</option>
						         		<option value="SUVANDNAT">SUVANDNAT</option>
						         		<option value="THEFITEDIT">THEFITEDIT</option>
						         		<option value="THESHADESSHOP">THESHADESSHOP</option>
						         		<option value="UPEAU">UPEAU</option>
						         		<option value="VERALUSSO">VERALUSSO</option>
						         	</select>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button type="submit" class="btn btn-secondary submit-generete-file-btn">Generate</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/x-jsrender" id="pem-file-user-history-lising">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">User Listing</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>User Id</th>
					        <th>Server</th>
					        <th>Username</th>
					        <th>Event</th>
					        <th>Created Date</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr class='subMagentoUser'>

						      	<td>
						      		<input type="hidden" class="pem_history_id"  value="{{:prop.id}}"/>
						      		
						      		{{:prop.user_id}}

						      	</td>
						        <td>
						        	{{:prop.server_name}}
						        </td>

						        <td>
						        	{{:prop.username}}
						        </td>

						        <td>
						        	{{:prop.action}}
						        </td>

						        <td>
						        	{{:prop.created_at}}
						        </td>

						        <td><button title="Delete user" type="button" class="btn btn-image delete-pem-user pd-5" data-id="{{:prop.id}}"><i class="fa fa-trash"></i></button></td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
				</div>	
			</div>
		</div>
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
<script type="text/x-jsrender" id="template-create-database">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Create Database</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="database_user_id" id="database-user-id" value="{{:data.user_id}}">
					  	<div class="row">
					  		<div class="col">
					      		<select class="form-control choose-db" name="connection">
					      			<?php foreach (\App\StoreWebsite::DB_CONNECTION as $k => $connection) { ?>
					      				<option {{if data.connection == "<?php echo $k; ?>" }} selected='selected' {{/if}} value="<?php echo $k; ?>"><?php echo $connection; ?></option>
					      			<?php } ?>		
					      		</select>
					      	</div>
					    	<div class="col">
					      		<input type="text" name="username" value="{{:data.user_name}}" class="form-control" placeholder="Enter username">
					      	</div>
					      	<div class="col">
					      		<input type="text" name="password" value="{{:data.password}}" class="form-control" placeholder="Enter password">
					      	</div>
					    	<div class="col">
					      		<button type="button" class="btn btn-secondary create-database-add" data-id="{{:data.user_id}}">ADD</button>
					      		{{if data.password}}
					      			<button type="button" class="btn btn-secondary delete-database-access" data-connection="{{:data.connection}}" data-id="{{:data.user_id}}">DELETE ACCESS</button>
					      		{{/if}}
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
			<div class="row mt-5">		
				<form>
					<?php echo csrf_field(); ?>
					<div class="col-lg-12">
						<div class="row">
					    	<div class="col">
					    		<input type="hidden" name="connection"  value="{{:data.connection}}">
					      		<input type="text" name="search" class="form-control search-table" placeholder="Search Table name">
					      	</div>
					      	<div class="col">
					      		<div class="form-group col-md-5">
					      			<select class="form-control assign-permission-type" name="assign_permission">
					      				<option value="read">Read</option>
					      				<option value="write">Write</option>
					      			</select>
					      		</div>
					      		<button type="button" class="btn btn-secondary assign-permission" data-id="{{:data.user_id}}">Assign Permission</button>
					    	</div>
					  	</div>	
					</div>	
					<div class="col-lg-12 mt-2">
						<table class="table table-bordered" id="database-table-list">
						    <thead>
						      <tr>
						      	<th width="5%"></th>
						      	<th width="95%">Table name</th>
						      </tr>
						    </thead>
						    <tbody>
						    	{{props data.tables}}
							      <tr>
							      	<td><input {{if prop.checked== true}} checked="checked" {{/if}} type="checkbox" name="tables[]" value="{{:prop.table}}"></td>
							      	<td>{{:prop.table}}</td>
							      </tr>
							    {{/props}}  
						    </tbody>
						</table>
					</div>	
				</form>
			</div>
		</div>
	</div>
</script>