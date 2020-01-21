<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th><input type="checkbox" class="select-all-records"></th>
		      	<th>Id</th>
		        <th>Number To</th>
		        <th>Number From</th>
		        <th>Message</th>
		        <th>Created At</th>
		        <th>Attached</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td><input class="select-id-input" type="checkbox" name="ids[]" value="{{:prop.id}}"></td>
			      	<td>{{:prop.id}}</td>
			        <td>{{:prop.phone}}</td>
			        <td>{{:prop.whatsapp_number}}</td>
			        <td>{{:prop.message}}</td>
			        <td>{{:prop.created_at}}</td>
			        <td>
			        	{{props prop.mediaList}}
			        		<img width="75px" heigh="75px" src="{{>prop}}">
			        	{{/props}}
			        </td>
			        <td><button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
