@forelse($storeWebsites as $storeWebsite)
	<tr>
		<td >
			{{$storeWebsite->id}}
		</td>
		<td width="15%">{{$storeWebsite->title}}</td>


		<td width="45%">
			<div style="display: flex">
				<input type="text" class="form-control" name="api_token[{{$storeWebsite->id}}]" value="{{$storeWebsite->api_token}}">
				<button type="button" data-id="" class="btn btn-copy-api-token btn-sm" data-value="{{$storeWebsite->api_token}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="server_ip[{{$storeWebsite->id}}]" value="{{$storeWebsite->server_ip}}">
				<button type="button" data-id="" class="btn btn-copy-server-ip btn-sm" data-value="{{$storeWebsite->server_ip}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
	</tr>
@empty
	<tr>
		<td colspan="4" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse