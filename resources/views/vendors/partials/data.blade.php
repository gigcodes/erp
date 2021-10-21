@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM')

@endphp
@foreach ($vendors as $vendor)
<tr>
    <td>{{ $vendor->id }}</td>
    <td>
        <select class="form-control ui-autocomplete-input" id="whatsapp_number" data-vendor-id="{{ $vendor->id }}">
            <option>-- Select --</option>
            @foreach($whatsapp as $wp)
            
            <option value="{{ $wp->number }}" @if($vendor->whatsapp_number == $wp->number) selected=selected @endif>
                {{ $wp->number }}</option>
            @endforeach
        </select>
    </td>
    <td class="expand-row-msg" data-name="category" data-id="{{$vendor->id}}">
        <span class="show-short-category-{{$vendor->id}}">
            @if(isset($vendor->category->title))
            {{ str_limit($vendor->category->title, 7, '..')}}
            @endif
            {{ str_limit($vendor->category_name, 7, '..')}}
        </span>
        <span style="word-break:break-all;" class="show-full-category-{{$vendor->id}} hidden">
            @if(isset($vendor->category->title))
            {{$vendor->category->title}}
            @endif
            {{$vendor->category_name}}
        </span>
    </td>
    <td class="expand-row-msg" data-name="name" data-id="{{$vendor->id}}">
        <span class="show-short-name-{{$vendor->id}}">
            {{ str_limit($vendor->name, 5, '..')}}
        </span>
        <span style="word-break:break-all;" class="show-full-name-{{$vendor->id}} hidden">
            {{$vendor->name}}
        </span>
        @if($vendor->phone)
            <button type="button" class="btn btn-xs call-select popup" data-id="{{ $vendor->id }}"><i class="fa fa-mobile"></i></button>
            <div class="numberSend" id="show{{ $vendor->id }}">
                <select class="form-control call-twilio" data-context="vendors" data-id="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                <option disabled selected>Select Number</option>
                @foreach(\Config::get("twilio.caller_id") as $caller)
                <option value="{{ $caller }}">{{ $caller }}</option>
                @endforeach
                </select>
            </div>
            @if ($vendor->is_blocked == 1)
                <button type="button" class="btn btn-xs block-twilio" data-id="{{ $vendor->id }}"><i class="fa fa-phone" style="color: red;"></i></button>
            @else
                <button type="button" class="btn btn-xs block-twilio" data-id="{{ $vendor->id }}"><i class="fa fa-phone" style="color: green;"></i></button>
            @endif
        @endif

    </td>
    <td class="expand-row-msg" data-name="phone" data-id="{{$vendor->id}}">
        <span class="show-short-phone-{{$vendor->id}}">{{ str_limit($vendor->phone, 8, '..')}}</span>
        <span style="word-break:break-all;" class="show-full-phone-{{$vendor->id}} hidden">{{$vendor->phone}}</span>
        @if ($vendor->status == 1)
          <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="On" data-id="{{ $vendor->id }}" id="do_not_disturb"><i class="fa fa-ban"></i></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="false" />  
        @else
          <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="Off" data-id="{{ $vendor->id }}" id="do_not_disturb"><i class="fa fa-ban" style="color: red;"></i></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="true" />
        @endif
    </td>
    <td class="expand-row-msg" data-name="email" data-id="{{$vendor->id}}">
        <span class="show-short-email-{{$vendor->id}}">{{ str_limit($vendor->email, 10, '..')}}</span>
        <span style="word-break:break-all;" class="show-full-email-{{$vendor->id}} hidden">{{$vendor->email}}</span>
    </td>
    {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
    <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}

    <td class="table-hover-cell p-0 pt-1 pl-1 {{ $vendor->message_status == 0 ? 'text-danger' : '' }}">
        <div class="row">
            <div class="col-md-8 form-inline cls_remove_rightpadding">
                <div class="row cls_textarea_subbox">
                    <div class="col-md-10 cls_remove_rightpadding">
                        <textarea rows="1" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $vendor->id }}" name="message" placeholder="Message"></textarea>
                    </div>
                    <div class="col-md-2 cls_remove_allpadding">
                        <button class="btn btn-sm btn-xs send-message1 mt-1" data-vendorid="{{ $vendor->id }}"><i class="fa fa-paper-plane"></i></button>
                        <button type="button" class="btn btn-xs load-communication-modal m-0 mt-1" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-comments"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="communication-div-5">
                    <div class="row">
                        <div class="col-md-10 cls_remove_allpadding">
                            <div class="d-flex">
                                <?php
                                //echo "<pre>";print_r($replies);echo "</pre>"; 
                                ?>
                                <?php 
                                //echo Form::select("quickComment",["" => "Auto Reply"]+$replies, null, ["class" => "form-control quickComment select2-quick-reply","style" => "width:100%" ]);

                                ?>
                                <select class="form-control quickComment select2-quick-reply" name="quickComment" style="width: 100%;" >
                                    <option  data-vendorid="{{ $vendor->id }}"  value="">Auto Reply</option>
                                    <?php
                                    foreach ($replies as $key_r => $value_r) { ?>
                                        <option title="<?php echo $value_r;?>" data-vendorid="{{ $vendor->id }}" value="<?php echo $key_r;?>">
                                            <?php
                                            $reply_msg = strlen($value_r) > 12 ? substr($value_r, 0, 12) : $value_r;
                                            echo $reply_msg;
                                            ?>
                                        </option>
                                    <?php }
                                    ?>
                                </select>
                                <a class="btn btn-xs delete_quick_comment {{ $vendor->message_status == 0 ? 'text-danger' : 'text-secondary' }} mt-1"><i class="fa fa-trash"></i></a>
                            </div>
                        </div> 
                    </div>
                </div>        
            </div> 
        </div>
        <div class="row cls_mesg_box mt-1">
            <div class="col-md-12">
                <div class="col-md-12 expand-row">
                @if(isset($vendor->chat_messages[0]))
                    <span class="td-mini-container message-chat-txt" id="message-chat-txt-{{ $vendor->id }}">
                    {{ strlen($vendor->chat_messages[0]->message) > 30 ? substr($vendor->chat_messages[0]->message, 0, 30) . '...' : $vendor->chat_messages[0]->message }}
                    </span>
                    <span class="td-full-container hidden" id="message-chat-fulltxt-{{ $vendor->id }}">
                      {{ $vendor->chat_messages[0]->message }}
                    </span>
                @endif
                @if(isset($vendor->message))
                    <span class="td-mini-container message-chat-txt" id="message-chat-txt-{{ $vendor->id }}">
                    {{ strlen($vendor->message) > 30 ? substr($vendor->message, 0, 30) . '...' : $vendor->message }}
                    </span>
                    <span class="td-full-container hidden" id="message-chat-fulltxt-{{ $vendor->id }}">
                      {{ $vendor->message }}
                    </span>
                @else
                    <span class="td-mini-container message-chat-txt" id="message-chat-txt-{{ $vendor->id }}"></span>
                    <span class="td-full-container hidden" id="message-chat-fulltxt-{{ $vendor->id }}"></span>
                @endif
                </div>
            </div>
        </div>
    </td>



    <td>
        <div class="cls_action_btn">
            <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-image" href=""><img src="<?php echo $base_url;?>/images/view.png"/></a>
			
			@php 
			$iconReminderColor = '';
			if($vendor->frequency)
			{
				$iconReminderColor = 'red';
			}
			
			@endphp
            <button data-toggle="modal" data-target="#reminderModal" class="btn btn-image set-reminder"
             data-id="{{ $vendor->id }}"
             data-frequency="{{ $vendor->frequency ?? '0' }}"
             data-reminder_message="{{ $vendor->reminder_message }}"
             data-reminder_from="{{ $vendor->reminder_from }}"
             data-reminder_last_reply="{{ $vendor->reminder_last_reply }}"
             >
                <img src="{{ asset('images/alarm.png') }}" alt="" style="width: 18px; background-color:{{$iconReminderColor}};">
				
            </button>

            <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="<?php echo $base_url;?>/images/edit.png"/></button>
            <a href="{{route('vendors.payments', $vendor->id)}}" class="btn btn-sm" title="Vendor Payments" target="_blank"><i class="fa fa-money"></i> </a>
            <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/remark.png"/></button>
                <button data-toggle="modal" data-target="#zoomModal" class="btn btn-image set-meetings" data-title="Meeting with {{ $vendor->name }}" data-id="{{ $vendor->id }}" data-type="vendor"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
                {!! Form::open(['method' => 'DELETE','route' => ['vendors.destroy', $vendor->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="<?php echo $base_url;?>/images/delete.png"/></button>
            {!! Form::close() !!}
            <span class="btn">
                <input type="checkbox" class="select_vendor" name="select_vendor[]" value="{{$vendor->id}}" {{ request()->get('select_all') == 'true' ? 'checked' : '' }}>
            </span>
            
            <!-- <button type="button" class="btn send-email-to-vender" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button> -->
            <button type="button" class="btn send-email-common-btn" data-toemail="{{$vendor->email}}" data-object="vendor" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button>
            <button type="button" class="btn create-user-from-vender" onclick="createUserFromVendor('{{ $vendor->id }}', '{{ $vendor->email }}')"><i class="fa fa-user"></i></button>
            <button type="button" class="btn add-vendor-info" title="Add vendor info" data-id="{{$vendor->id}}"><i class="fa fa-info-circle" aria-hidden="true"></i></button>

            <button type="button" style="cursor:pointer" class="btn btn-image change-hubstaff-role" title="Change Hubstaff user role" data-id="{{$vendor->id}}"><img src="/images/role.png" alt="" style="cursor: nwse-resize;"></button>
            
        </div>
    </td>
</tr>
@endforeach
   
