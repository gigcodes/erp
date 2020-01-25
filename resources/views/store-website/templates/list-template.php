<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
		        <th>Title</th>
		        <th>Website</th>
		        <th>Description</th>
		        <th>Published</th>
		        <th>Created At</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td>{{:prop.title}}</td>
			        <td>{{:prop.website}}</td>
			        <td>{{:prop.description}}</td>
			        <td>{{:prop.is_published}}</td>
			        <td>{{:prop.created_at}}</td>
			        <td>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template"><img width="15px" src="/images/edit.png"></button>
			        	|<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
