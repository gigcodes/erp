@php
        $isAdmin            = auth()->user()->isAdmin();
        $isHod              = auth()->user()->hasRole('HOD of CRM');
        $hasSiteDevelopment = auth()->user()->hasRole('Site-development');
        $userId             = auth()->user()->id;
    @endphp
    @foreach($subjects as $subject)
		<?php 
            if($isAdmin || $hasSiteDevelopment) {
        ?>
    	<tr>
    		<td>
    			@if($website) {{ $website->website }} @endif
    			<br>
    			{{ $subject->title }}
    			<br>
    		</td>
            <td>
            <input type="text" class="form-control save-item" data-subject="{{ $subject->id }}" data-type="description" value="" data-site="@if($website){{ $website->id }}@endif"></td>
    		<td>

      			<select style="margin-top: 5px;" class="form-control save-item-select" data-subject="{{ $subject->id }}" data-type="execution" data-site="@if($website){{ $website->id }}@endif" id="user-@if($website){{ $website->id }}@endif">
    				<option>Select Execution</option>
    				@foreach($users as $user)
    					<option value="{{ $user->id }}" @if($website && $website->execution == $user->id) selected @endif >{{ $user->name }}</option>
    				@endforeach
    			</select>
                <select style="margin-top: 5px;" name="designer_id" class="form-control save-item-select" data-category="{{ $subject->id }}" data-type="designer_id" data-site="@if($website) {{ $website->id }} @endif" id="user-@if($website){{ $website->id }}@endif">
                    <option>Select Designer</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"@if($website && $website->designer_id == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
    		</td>
            <td>
    			@if($website)
    				<div class="chat_messages expand-row table-hover-cell">
    					<button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="site_development" data-id="{{$website->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
    					<span class="chat-mini-container"> @if($website->lastChat) {{ $website->lastChat->message }} @endif</span>
    			     	<span class="chat-full-container hidden"></span>
    				</div>
    			@endif
    			<div class="d-flex">
                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="message-@if($website){{ $website->id }}@endif">
                    <button class="btn btn-sm btn-image send-message-site" data-id="@if($website){{ $website->id }}@endif"><img src="/images/filled-sent.png"/></button>
                    <br/>
                </div>
    		</td>
            <td></td>
    	</tr>
    <?php } ?>	
	@endforeach  
