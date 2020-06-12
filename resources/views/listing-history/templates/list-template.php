<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="2%"></th>
		      	<th width="10%">Product</th>
		        <th width="10%">Updated By</th>
		        <th width="10%">Key</th>
		        <th width="30%">Content</th>
		        <th width="10%">Created At</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.product_name}}</td>
			      	<td>{{:prop.user_name}}</td>
			      	<td>{{:prop.action}}</td>
			      	<td>{{:prop.content}}</td>
			        <td>{{:prop.created_at}}</td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>	