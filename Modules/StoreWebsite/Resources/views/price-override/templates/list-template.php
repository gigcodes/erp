<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
		        <th>Brand</th>
		        <th>Category</th>
		        <th>Country</th>
		        <th>Price</th>
		        <th>Type</th>
		        <th>Calculated</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td>{{:prop.brand_name}}</td>
			        <td>{{:prop.category_name}}</td>
			        <td>{{:prop.country_name}}</td>
			        <td>{{:prop.value}}</td>
			        <td>{{:prop.type}}</td>
			        <td>{{:prop.calculated}}</td>
			        <td>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template"><img width="15px" title="Edit" src="/images/edit.png"></button>
			        	|<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><i class="fa fa-trash" aria-hidden="true"></i></button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>