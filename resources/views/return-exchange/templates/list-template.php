<script type="text/x-jsrender" id="template-result-block">
	<div class="row page-template-{{:page}}">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th><input type="checkbox" class="select-all-records"></th>
		      	<th>Id</th>
		        <th>Customer Name</th>
		        <th>Product Name</th>
		        <th>Type</th>
		        <th>Refund amount</th>
		        <th>Reason for refund</th>
		        <th>Status</th>
		        <th>Pickup Address</th>
		        <th>Remarks</th>
		        <th>Created At</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td><input class="select-id-input" type="checkbox" name="ids[]" value="{{:prop.id}}"></td>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.customer_name}}</td>
			      	<td>{{:prop.name}}</td>
			        <td>{{:prop.type}}</td>
			        <td>{{:prop.refund_amount}}</td>
			        <td>{{:prop.reason_for_refund}}</td>
			        <td>{{:prop.status}}</td>
			        <td>{{:prop.pickup_address}}</td>
			        <td>{{:prop.remarks}}</td>
			        <td>{{:prop.created_at}}</td>
			        <td><button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button></td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>