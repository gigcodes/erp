
	@foreach($categories as $category)
	<tr>
		<?php $site = $category->getDevelopment($category->id,$website->id); ?>
		<td>
			@if($website) {{ $website->website }} @endif
			<br>
			{{ $category->title }}
			<br>
			<button onclick="editCategory({{$category->id}})" style="background-color: transparent;border: 0;"><i class="fa fa-edit"></i></button>
		</td>
		<td>
			<input type="hidden" id="website_id" value="@if($website) {{ $website->id }} @endif">
			<input type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="title" value="@if($site) {{ $site->title }}@endif" data-site="@if($site) {{ $site->id }}@endif"></td>
		<td><input type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="description" value="@if($site) {{ $site->description }}@endif" data-site="@if($site) {{ $site->id }}@endif"></td>
		<td>
			<?php echo Form::select("status",["" => "-- Select --"] + $allStatus,($site) ? $site->status : 0,[
  				"class" => "form-control save-item-select" ,
  				"data-category" => $category->id,
  				"data-type" => "status",
  				"data-site" => ($site) ? $site->id : ""
  			]) ?>
  			<br>
			<select class="form-control save-item-select" data-category="{{ $category->id }}" data-type="developer" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
				<option>Select Developer</option>
				@foreach($users as $user)
					<option value="{{ $user->id }}" @if($site && $site->developer_id == $user->id) selected @endif >{{ $user->name }}</option>
				@endforeach
			</select>
		</td>
        <td>
			@if($site)
				<div class="chat_messages expand-row table-hover-cell">
					<button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="site_development" data-id="{{$site->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
					<span class="chat-mini-container"> @if($site->lastChat) {{ $site->lastChat->message }} @endif</span>
			     	<span class="chat-full-container hidden"></span>
				</div>
			@endif
			<div class="d-flex">
                <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="message-@if($site){{ $site->id }}@endif">
                <button class="btn btn-sm btn-image send-message-site" data-id="@if($site){{ $site->id }}@endif"><img src="/images/filled-sent.png"/></button>
                <br/>
            </div>
		</td>
		<td>@if($site) {{ $site->created_at }}@endif</td>
	</tr>
		@include("storewebsite::site-development.partials.edit-modal")	
	@endforeach  
