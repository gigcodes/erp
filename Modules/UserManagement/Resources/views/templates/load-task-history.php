<script type="text/x-jsrender" id="template-task-history">
<form name="template-create-goal" method="post">
		<div class="modal-content tasks_list_tbl">
		   <div class="modal-header">
		      <h5 class="modal-title">Task</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
           <table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Task</th>
				<th>Description</th>
		      	<th>Approximate time</th>
				<th>Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>
					  {{if prop.type == 'TASK'}}
					  #TASK-{{:prop.task_id}} => {{:prop.subject}}
					  {{else}}
					  #DEVTASK-{{:prop.task_id}} => {{:prop.subject}}
					  {{/if}}

					 
					  </td>
					<td>
						<div class="show_hide_description">Show Description</div>
						<div class="description_content" style="display:none">
							{{:prop.details}}
						</div>
					</td>
			      	<td>
					  <div class="form-group">
							<div class='input-group estimate_minutes'>
								<input style="min-width: 30px;" placeholder="E.minutes" value="{{:prop.approximate_time}}" type="text" class="form-control estimate-time-change" name="estimate_minutes_{{:prop.task_id}}" data-id="{{:prop.task_id}}" id="estimate_minutes_{{:prop.task_id}}" data-type={{:prop.type}}>

								<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{:prop.task_id}}" data-type={{:prop.type}}><i class="fa fa-info-circle"></i></button>
							</div>
						</div>
						<label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
                        <div class="d-flex">
                            <div class="form-group" style="padding-top:5px;">
                                <div class='input-group date due-datetime'>
									<input type="text" class="form-control input-sm due_date_cls" name="due_date" data-type={{:prop.type}} value="{{:prop.due_date}}"/>
									<span class="input-group-addon">
                            		<span class="glyphicon glyphicon-calendar"></span>
                        			</span>
								</div>
							</div>
                            <button class="btn btn-sm btn-image set-due-date" title="Set due date" data-taskid="{{:prop.task_id}}"><img style="padding: 0;margin-top: -14px;" src="/images/filled-sent.png"/></button>
                        </div>
					  </td>
					  <td></td>
				  </tr>
				  {{/props}}
		    </tbody>
		</table>
		   </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		   </div>
		</div>
	</form>
	$('.due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    }); 
	
</script>