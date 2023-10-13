@php
$i = 1;
@endphp
@forelse($storeWebsiteAdminUrls as $storeWebsiteAdminUrl)
	<tr>
		<td >
			{{$i}}
		</td>	

		<td width="30%">
			<div style="display: flex">
				<select name="store_website_id[edit:{{$storeWebsiteAdminUrl->id}}]" id="website_mode" class="form-control websiteMode">					               
			       <option value="">-- Select a website--</option>
					@foreach($storeWebsites as $key => $storeWebsite)
						<option {{$storeWebsiteAdminUrl->store_website_id == $storeWebsite->id  ? 'selected' : ''}} value="{{ $storeWebsite->id }}">{{ $storeWebsite->title }}</option>
					@endforeach
				</select>
			</div>
		</td>	

		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="admin_url[edit:{{$storeWebsiteAdminUrl->id}}]" value="{{$storeWebsiteAdminUrl->admin_url}}">
				<button type="button" data-id="" class="btn btn-copy-api-token btn-sm" data-value="{{$storeWebsiteAdminUrl->admin_url}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="store_dir[edit:{{$storeWebsiteAdminUrl->id}}]" value="{{$storeWebsiteAdminUrl->store_dir}}">
				<button type="button" data-id="" class="btn btn-copy-server-ip btn-sm" data-value="{{$storeWebsiteAdminUrl->store_dir}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="server_ip_address[edit:{{$storeWebsiteAdminUrl->id}}]" value="{{$storeWebsiteAdminUrl->server_ip_address}}">
				<button type="button" data-id="" class="btn btn-copy-server-ip btn-sm" data-value="{{$storeWebsiteAdminUrl->server_ip_address}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td>
			<button type="button" data-id="{{$storeWebsiteAdminUrl->store_website_id}}" class="btn admin-url-history" style="padding:1px 0px;">
        		<i class="fa fa-info-circle" aria-hidden="true"></i>
        	</button>
		</td>
	</tr>
	@php
	$i++;
	@endphp
@empty
	<tr>
		<td colspan="4" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse