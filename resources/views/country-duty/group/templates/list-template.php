<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
              	<th width="2%"></th>
		      	<th width="2%">HsCode</th>
		      	<th width="2%">Origin</th>
		      	<th width="2%">Destination</th>
		        <th width="5%">Value</th>
		        <th width="5%">VAT</th>
		        <th width="5%">Duty</th>
		        <th width="5%">Total</th>
		        <th width="5%">CurrencyType origin</th>
		        <th width="5%">CurrencyType Destination</th>
		        <th width="5%">Duty Rate</th>
		        <th width="5%">Vat Rate</th>
		        <th width="5%">Group Name</th>
		        <th width="5%">Group Duty</th>
		        <th width="5%">Group Vat</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>
			      		<input type="checkbox" class="duty-rate-ckbx" 
				      		name="duty_country[]" 
				      		value="{{:prop.hs_code}}"
				      		data-hs-code="{{:prop.hs_code}}"
				      		data-origin="{{:prop.origin}}"
				      		data-destination="{{:prop.destination}}"
				      		data-vat-rate="{{:prop.vat_percentage}}"
				      		data-duty-rate="{{:prop.duty_percentage}}"
				      		data-vat-val="{{:prop.vat}}"
				      		data-duty-val="{{:prop.duty}}"
				      		data-total="{{:prop.price}}"
				      		data-currency-origin="{{:prop.currency}}"
				      		data-currency-destination="{{:prop.currency}}"
			      		/>
			      	</td>
			      	<td>{{:prop.hs_code}}</td>
			      	<td>{{:prop.origin}}</td>
			      	<td>{{:prop.destination}}</td>
			      	<td>{{:prop.price}}</td>
			      	<td>{{:prop.vat}}</td>
			      	<td>{{:prop.duty}}</td>
			      	<td>{{:prop.price}}</td>
			      	<td>{{:prop.currency}}</td>
			      	<td>{{:prop.currency}}</td>
			      	<td>{{:prop.duty_percentage}}</td>
			      	<td>{{:prop.vat_percentage}}</td>
			      	<td>{{:prop.group_name}}</td>
			      	<td>{{:prop.group_duty}}</td>
			      	<td>{{:prop.group_vat}}</td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>


<script type="text/x-jsrender" id="template-create-country-group-form">
<div class="modal-content">
   <div class="modal-header">
      <h5 class="modal-title">Create Country Group</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      	<span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
		<div class="row">
			<div class="col-lg-12">
				<form>
					<?php echo csrf_field(); ?>
					<div class="row">
				  		<div class="col-md-12">
				    		<div class="form-group">
					         	<?php echo Form::text("name",null,["class" => "form-control group-name"]); ?>
					         </div>
				        </div> 
				        <div class="col-md-12">
					    	<div class="form-group">
					      		<button class="btn btn-secondary create-country-group-btn">Create Country Group</button>
					    	</div>
				    	</div>
				  	</div>
				</form>
			</div>
		</div>
	</div>
</div>
</script>