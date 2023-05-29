@forelse($storeWebsites as $storeWebsite)
	<tr>
		<td class="text-center">
			<span class="td-mini-container">
				<input type="checkbox" class="selectedStoreWebsite" name="selectedStoreWebsite" value="{{$storeWebsite->id}}">
			</span>
		  </td>
		<td >
			{{$storeWebsite->id}}
		</td>
		<td width="15%">{{$storeWebsite->title}}</td>


		<td width="45%">
			<div style="display: flex">
				<input type="text" class="form-control" id="api_token_{{$storeWebsite->id}}" name="api_token[{{$storeWebsite->id}}]" value="{{$storeWebsite->api_token}}">
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
		<td width="30%">
			<div style="display: flex">
				<button title="Generate API Token" data-id="{{$storeWebsite->id}}"  type="button" class="btn generate-api-token" style="padding:1px 5px;" data-toggle="modal" data-target="#generate-api-token-modal">
					<a href="javascript:;" style="color:gray;"><i class="fa fa-refresh"></i></a>
				</button>
				<button title="API Token Logs" data-id="{{$storeWebsite->id}}"  type="button" class="btn api-token-logs" style="padding:1px 5px;">
					<a href="javascript:;" style="color:gray;"><i class="fa fa-history"></i></a>
				</button>
				<button title="Test API Token" data-id="{{$storeWebsite->id}}"  type="button" class="btn btn-test-api-token" style="padding:1px 5px;">
					<a href="javascript:;" style="color:gray;"><i class="fa fa-plane"></i></a>
				</button>
				<button title="Update Api Token" type="submit" class="btn" style="padding:1px 5px;"><a href="javascript:;" style="color:gray;"><i class="fa fa-save"></i></a></button>
			</div>
		</td>
	</tr>
@empty
	<tr>
		<td colspan="4" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse