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
		      	<th>Name</th> 
		      	<th>Pending</th>
				<th>Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>{{:prop.name}}</td>
			      	<td>{{:prop.total}}</td>
			      	<td>
						  {{if prop.name == 'TASK'}}
						  <a href="/task">Task</a>
						  {{else}} 
						  <a href="/development/list/devtask">Devtask</a>
						  {{/if}}
					  </td>
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