<script type="text/x-jsrender" id="product-templates-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
		        <th>Name</th>
		        <th>Image</th>
		        <th>No Of Images</th>
		        <th>Created At</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props result.data}}
			      <tr>
			      	<td>{{>prop.id}}</td>
			      	<td>{{>prop.name}}</td>
			      	<td><img src="{{>prop.image}}" width="50px" height="50px"></td>
			      	<td>{{>prop.no_of_images}}</td>
			        <td>{{>prop.created_at}}</td>
			        <td><button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
