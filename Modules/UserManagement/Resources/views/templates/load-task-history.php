<script type="text/x-jsrender" id="template-task-history">
<form name="template-create-goal" method="post">
		<div class="modal-content">
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
		      	<th>Approximate time</th>
				<th>Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>
					  {{if prop.type == 'TASK'}}
					  #TASK-{{:prop.task_id}} => {{:prop.subject}} : {{:prop.subject}}. {{:prop.details}}
					  {{else}}
					  #DEVTASK-{{:prop.task_id}} => {{:prop.subject}} : {{:prop.subject}}. {{:prop.details}}
					  {{/if}}

					 
					  </td>
			      	<td>
					  <div class="form-group">
							<div class='input-group estimate_minutes'>
								<input style="min-width: 30px;" placeholder="E.minutes" value="{{:prop.approximate_time}}" type="text" class="form-control estimate-time-change" name="estimate_minutes_{{:prop.task_id}}" data-id="{{:prop.task_id}}" id="estimate_minutes_{{:prop.task_id}}" data-type={{:prop.type}}>

								<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{:prop.task_id}}" data-type={{:prop.type}}><i class="fa fa-info-circle"></i></button>
							</div>
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
</script>