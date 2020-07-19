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
		      	<th>Task ID</th> 
		      	<th>Task Type</th> 
		      	<th>Subject</th> 
				<th>Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>{{:prop.task_id}}</td>
			      	<td>{{:prop.type}}</td>
			      	<td>{{:prop.subject.substring(0, 150)}}...</td>
			      	<td><a href="#">link</a></td>
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