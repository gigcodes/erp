<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
				<th>Name</th>
				<th>Code</th>
				<th>Sort Order</th>
				<th>Site</th>
				<th>Actions</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}">{{:prop.id}}</td>
			      	<td>{{:prop.name}}</td>
			        <td>{{:prop.code}}</td>
			        <td>{{:prop.sort_order}}</td>
			        <td>{{:prop.store_website_name}}</td>
			        <td>
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
			        		<i class="fa fa-upload" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Copy" data-id="{{>prop.id}}" class="btn btn-copy-template">
			        		<i class="fa fa-copy" aria-hidden="true"></i>
			        	</button>
			        	<a href="/store-website/website-stores?website_id={{>prop.id}}">
				        	<button type="button" title="View" data-id="{{>prop.id}}" class="btn">
				        		<i class="fa fa-eye" aria-hidden="true"></i>
				        	</button>
				        </a>
			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>