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
			        <td>{{:prop.status_name}}</td>
			        <td>{{:prop.pickup_address}}</td>
			        <td>{{:prop.remarks}}</td>
			        <td>{{:prop.created_at}}</td>
			        <td>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"><img width="15px" src="/images/delete.png"></button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template"><img width="15px" src="/images/edit.png"></button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
<script type="text/x-jsrender" id="template-edit-block">
	<div id="return-exchange-edit-modal" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	           <form action="{{  route('return-exchange.update',[$id]) }}" method="POST" enctype="multipart/form-data" class="" id="return-exchange-update-form" data-reload='1'>
			    {{ csrf_field() }}
			    <div class="row">
			        <div class="col">
			            <div class="form-group">
			                <strong>Type&nbsp;:&nbsp;</strong>
			                <span><input type="radio" name="type" value="refund" />Refund</span>
			                <span><input type="radio" name="type" value="exchange" />Exchange</span>
			            </div>
			        </div>
			    </div>
			    <div class="row refund-section" style="display: none">
			        <div class="col">
			            <div class="form-group">
			                <strong>Reason for refund&nbsp;:&nbsp;</strong>
			                <input type="text" class="form-control" name="reason_for_refund"></textarea>
			            </div>
			        </div>
			        <div class="col">
			            <div class="form-group">
			                <strong>Refund Amount&nbsp;:&nbsp;</strong>
			                <input type="text" class="form-control" name="refund_amount"></textarea>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col">
			            <div class="form-group">
			                <strong>Status&nbsp;:&nbsp;</strong>
			                <select name="status" class="form-control select-multiple" style="width: 100%;">
			                    {{props status}}
			                        <option value="{{>key}}">{{>prop}}</option>
			                    {{/props}}
			                </select>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col">
			            <div class="form-group">
			                <strong>Pickup Address&nbsp;:&nbsp;</strong>
			                <textarea class="form-control" name="pickup_address"></textarea>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col">
			            <div class="form-group">
			                <strong>Remarks&nbsp;:&nbsp;</strong>
			                <textarea class="form-control" name="remarks"></textarea>
			            </div>
			        </div>
			    </div>
			    <div class="row">
			        <div class="col">
			            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			            <button type="submit" class="btn btn-secondary" id="btn-return-exchage-request">Submit</button>
			      	</div>
			    </div>
			</form> 	
	        </div>
	    </div>
	</div>
</script>