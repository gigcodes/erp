<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="10%">Date</th>
		        <th width="10%">Message</th>
		        <th width="10%">Sender</th>
		        <th width="15%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.created_at}}</td>
			      	<td>{{:prop.message}}</td>
			      	<td>{{:prop.sender}}</td>
			      	<td></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
	</div>
	{{:pagination}}
</script>


<script type="text/x-jsrender" id="template-load-data">
	<div class="modal-content">
	   <div class="modal-header">
	      
	      <h5 class="modal-title"></h5>
	      
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			{{:data}}
		</div>
	</div>			
</script>