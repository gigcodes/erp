<script type="text/x-jsrender" id="product-templates-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
		        <th>Template no</th>
		        <th>Product Title</th>
		        <th>Brand</th>
		        <th>Currency</th>
		        <th>Price</th>
		        <th>Discounted price</th>
		        <th>Product</th>
		        <th>Is Processed</th>
		        <th>Created at</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props result.data}}
			      <tr>
			      	<td>{{>prop.id}}</td>
			        <td>{{>prop.template_no}}</td>
			        <td>{{>prop.product_title}}</td>
			        <td>{{>prop.brand_id}}</td>
			        <td>{{>prop.currency}}</td>
			        <td>{{>prop.price}}</td>
			        <td>{{>prop.discounted_price}}</td>
			        <td>{{>prop.product_id}}</td>
			        <td>{{>prop.is_processed}}</td>
			        <td>{{>prop.created_at}}</td>
			        <td><button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
