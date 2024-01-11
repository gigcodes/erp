@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM')

@endphp
@foreach ($vendors as $vendor)

@php
    $status_color = \App\VendorStatus::where('id',$vendor->status)->first();
    if ($status_color == null) {
        $status_color = new stdClass();
    }

    $vendor_frameworks = [];
    $vendor_frameworksv = '';
    if(!empty($vendor->framework)){

        $vendor_frameworks = \App\Models\VendorFrameworks::whereIn('id', explode(",",$vendor->framework))->pluck('name', 'id');

        $vendor_frameworksv = collect($vendor_frameworks)->implode(', ');

    }

@endphp
@if(!empty($dynamicColumnsToShowVendors))
    <tr style="background-color: {{$status_color->color ?? ""}}!important;">
        @if (!in_array('ID', $dynamicColumnsToShowVendors))
            <td>{{ $vendor->id }}</td>
        @endif

        @if (!in_array('WhatsApp', $dynamicColumnsToShowVendors))
            <td>
                <select class="form-control ui-autocomplete-input" id="whatsapp_number" data-vendor-id="{{ $vendor->id }}">
                    <option>-- Select --</option>
                    @foreach($whatsapp as $wp)
                    <option value="{{ $wp->number }}" @if($vendor->whatsapp_number == $wp->number) selected=selected @endif>
                        {{ $wp->number }}</option>
                    @endforeach
                </select>
            </td>
        @endif

        @if (!in_array('Category', $dynamicColumnsToShowVendors))
            <td class="expand-row-msg Website-task" data-name="category" data-id="{{$vendor->id}}">
                <span class="show-short-category-{{$vendor->id}}">
                    @if(isset($vendor->category->title))
                    {{ Str::limit($vendor->category->title, 7, '..')}}
                    @endif
                    {{ Str::limit($vendor->category_name, 7, '..')}}
                </span>
                <span style="word-break:break-all;" class="show-full-category-{{$vendor->id}} hidden">
                    @if(isset($vendor->category->title))
                    {{$vendor->category->title}}
                    @endif
                    {{$vendor->category_name}}
                </span>
            </td>
        @endif

        @if (!in_array('Status', $dynamicColumnsToShowVendors))
            <td class="expand-row-msg position-relative" data-name="status" data-id="{{$vendor->id}}">
            <?php echo Form::select("vendor_status",[null=>'Select Status']+$statusList,$vendor->vendor_status,["class" => "form-control select-width","onchange" => "updateVendorStatus(this, ".$vendor->id.")"]); ?>
            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$vendor->id}}">
                        <i class="fa fa-info-circle i-vendor-status-history"></i>
            </button>
            </td>
        @endif

        @if (!in_array('Name', $dynamicColumnsToShowVendors))
            <td class="expand-row-msg" data-name="name" data-id="{{$vendor->id}}">
                <span class="show-short-name-{{$vendor->id}}">
                    {{ Str::limit($vendor->name, 5, '..')}}
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
        @endif

        @if (!in_array('Phone', $dynamicColumnsToShowVendors))
            <td class="expand-row-msg" data-name="phone" data-id="{{$vendor->id}}">
                <div class="d-flex">
                    <span class="show-short-phone-{{$vendor->id}} Website-task">{{ Str::limit($vendor->phone, 8, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-phone-{{$vendor->id}} Website-task hidden" >{{$vendor->phone}}</span>
                    @if ($vendor->status == 1)
                      <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="On" data-id="{{ $vendor->id }}" id="do_not_disturb" style="margin-top: -2px;"><i class="fa fa-ban"></i></button>
                      <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="false" />  
                    @else
                      <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="Off" data-id="{{ $vendor->id }}" id="do_not_disturb" style="margin-top: -2px;"><i class="fa fa-ban"></i></button>
                      <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="true" />
                    @endif
                </div>
            </td>
        @endif

        @if (!in_array('Email', $dynamicColumnsToShowVendors))
            <td class="expand-row-msg Website-task" data-name="email" data-id="{{$vendor->id}}">
                <span class="show-short-email-{{$vendor->id}}">{{ Str::limit($vendor->email, 10, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-email-{{$vendor->id}} hidden">{{$vendor->email}}</span>
            </td>
        @endif

        {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
        <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}

        @if (!in_array('Communication', $dynamicColumnsToShowVendors))
            <td class="table-hover-cell p-0 pt-1 pl-1 {{ $vendor->message_status == 0 ? 'text-danger' : '' }}">
                <div class="row">
                    <div class="col-md-8 form-inline cls_remove_rightpadding">
                        <div class="d-flex cls_textarea_subbox" style="justify-content: space-between;">
                            
                                <textarea rows="1" class="form-control quick-message-field cls_quick_message mr-1" id="messageid_{{ $vendor->id }}" name="message" placeholder="Message"></textarea>
                         
                           
                                <button class="btn btn-sm btn-xs send-message1 mr-1" data-vendorid="{{ $vendor->id }}"><i class="fa fa-paper-plane"></i></button>
                                <button type="button" class="btn btn-xs load-communication-modal m-0 mr-1" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-comments"></i></button>
                            
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
                                        <a class="btn btn-xs delete_quick_comment text-secondary ml-1"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div> 
                            </div>
                        </div>        
                    </div> 
                </div>
                
            </td>
        @endif

        @if (!in_array('Remarks', $dynamicColumnsToShowVendors))
            <td>
                <div class="row">
                    <div class="col-md-11 form-inline cls_remove_rightpadding">
                        <div class="d-flex cls_textarea_subbox" style="justify-content: space-between;">
                            
                            <textarea rows="1" class="form-control mr-1" id="remarks_{{ $vendor->id }}" name="remarks" placeholder="Message"></textarea>
                     
                            <button class="btn btn-sm btn-xs remarks-message mr-1" data-vendorid="{{ $vendor->id }}"><i class="fa fa-paper-plane"></i></button>

                            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-remarks-history" title="Show Status History" data-id="{{$vendor->id}}">
                                <i class="fa fa-info-circle i-vendor-remarks-history"></i>
                            </button>
                            
                        </div>
                    </div>
                </div>                
            </td>
        @endif

        @if (!in_array('Type', $dynamicColumnsToShowVendors))
            <td>{{ $vendor->type }}</td>
        @endif

        @if (!in_array('Framework', $dynamicColumnsToShowVendors))
            <td>{{ $vendor_frameworksv }}</td>
        @endif

        @if (!in_array('Created Date', $dynamicColumnsToShowVendors))
            <td>{{ $vendor->created_at }}</td>
        @endif

        @if (!in_array('Action', $dynamicColumnsToShowVendors))
            <td>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$vendor->id}}')"><i class="fa fa-arrow-down"></i></button>
            </td>
        @endif
    </tr>
    @if (!in_array('Action', $dynamicColumnsToShowVendors))
        <tr class="action-btn-tr-{{$vendor->id}} d-none">
            <td>Action</td>
            <td colspan="8">
                <div class="cls_action_btn">
                    @if($isAdmin)
                    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-image" href=""><img src="<?php echo $base_url;?>/images/view.png"/style="color: gray;"></a>
                    
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
                        <img src="{{ asset('images/alarm.png') }}" alt="" style="width: 18px;">
                        
                    </button>
                    @endif
                    <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="<?php echo $base_url;?>/images/edit.png"/></button>
                    @if($isAdmin)
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
                    @endif
                    {{-- <button type="button" style="cursor:pointer" class="btn btn-image create-cv" title="Create CV" data-id="{{$vendor->id}}"><i class="fa fa-file" aria-hidden="true"></i></button> --}}
                    <a href="{{route('vendors.create.cv', $vendor->id)}}" class="btn btn-sm" title="Vendor Create" target="_blank"><i class="fa fa-file"></i> </a>
                    <a href="javascript:void(0)" class="btn btn-sm update-flowchart-date" data-id="{{$vendor->id}}" title="Vendor Flow Chart" @if($vendor->fc_status==1) style="color: #0062cc;" @endif>
                        <i class="fa fa-line-chart" aria-hidden="true"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endif
@else
    <tr style="background-color: {{$status_color->color ?? ""}}!important;">
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
        <td class="expand-row-msg Website-task" data-name="category" data-id="{{$vendor->id}}">
            <span class="show-short-category-{{$vendor->id}}">
                @if(isset($vendor->category->title))
                {{ Str::limit($vendor->category->title, 7, '..')}}
                @endif
                {{ Str::limit($vendor->category_name, 7, '..')}}
            </span>
            <span style="word-break:break-all;" class="show-full-category-{{$vendor->id}} hidden">
                @if(isset($vendor->category->title))
                {{$vendor->category->title}}
                @endif
                {{$vendor->category_name}}
            </span>
        </td>
        <td class="expand-row-msg position-relative" data-name="status" data-id="{{$vendor->id}}">
        <?php echo Form::select("vendor_status",[null=>'Select Status']+$statusList,$vendor->vendor_status,["class" => "form-control select-width","onchange" => "updateVendorStatus(this, ".$vendor->id.")"]); ?>
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$vendor->id}}">
                    <i class="fa fa-info-circle i-vendor-status-history"></i>
        </button>
        </td>
        <td class="expand-row-msg" data-name="name" data-id="{{$vendor->id}}">
            <span class="show-short-name-{{$vendor->id}}">
                {{ Str::limit($vendor->name, 5, '..')}}
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
            <div class="d-flex">
                <span class="show-short-phone-{{$vendor->id}} Website-task">{{ Str::limit($vendor->phone, 8, '..')}}</span>
                <span style="word-break:break-all;" class="show-full-phone-{{$vendor->id}} Website-task hidden" >{{$vendor->phone}}</span>
                @if ($vendor->status == 1)
                  <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="On" data-id="{{ $vendor->id }}" id="do_not_disturb" style="margin-top: -2px;"><i class="fa fa-ban"></i></button>
                  <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="false" />  
                @else
                  <button type="button" class="btn btn-xs vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="Off" data-id="{{ $vendor->id }}" id="do_not_disturb" style="margin-top: -2px;"><i class="fa fa-ban"></i></button>
                  <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="true" />
                @endif
            </div>
        </td>
        <td class="expand-row-msg Website-task" data-name="email" data-id="{{$vendor->id}}">
            <span class="show-short-email-{{$vendor->id}}">{{ Str::limit($vendor->email, 10, '..')}}</span>
            <span style="word-break:break-all;" class="show-full-email-{{$vendor->id}} hidden">{{$vendor->email}}</span>
        </td>
        {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
        <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}

        <td class="table-hover-cell p-0 pt-1 pl-1 {{ $vendor->message_status == 0 ? 'text-danger' : '' }}">
            <div class="row">
                <div class="col-md-8 form-inline cls_remove_rightpadding">
                    <div class="d-flex cls_textarea_subbox" style="justify-content: space-between;">
                        
                            <textarea rows="1" class="form-control quick-message-field cls_quick_message mr-1" id="messageid_{{ $vendor->id }}" name="message" placeholder="Message"></textarea>
                     
                       
                            <button class="btn btn-sm btn-xs send-message1 mr-1" data-vendorid="{{ $vendor->id }}"><i class="fa fa-paper-plane"></i></button>
                            <button type="button" class="btn btn-xs load-communication-modal m-0 mr-1" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><i class="fa fa-comments"></i></button>
                        
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
                                    <a class="btn btn-xs delete_quick_comment text-secondary ml-1"><i class="fa fa-trash"></i></a>
                                </div>
                            </div> 
                        </div>
                    </div>        
                </div> 
            </div>
            
        </td>

        <td>
            <div class="row">
                <div class="col-md-11 form-inline cls_remove_rightpadding">
                    <div class="d-flex cls_textarea_subbox" style="justify-content: space-between;">
                        
                        <textarea rows="1" class="form-control mr-1" id="remarks_{{ $vendor->id }}" name="remarks" placeholder="Message"></textarea>
                 
                        <button class="btn btn-sm btn-xs remarks-message mr-1" data-vendorid="{{ $vendor->id }}"><i class="fa fa-paper-plane"></i></button>

                        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-remarks-history" title="Show Status History" data-id="{{$vendor->id}}">
                            <i class="fa fa-info-circle i-vendor-remarks-history"></i>
                        </button>
                        
                    </div>
                </div>
            </div>                
        </td>

        <td>{{ $vendor->type }}</td>

        <td>{{ $vendor_frameworksv }}</td>

        <td>{{ $vendor->created_at }}</td>

        <td>
            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$vendor->id}}')"><i class="fa fa-arrow-down"></i></button>
        </td>
    </tr>
    <tr class="action-btn-tr-{{$vendor->id}} d-none">
        <td>Action</td>
        <td colspan="8">
            <div class="cls_action_btn">
                @if($isAdmin)
                <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-image" href=""><img src="<?php echo $base_url;?>/images/view.png"/style="color: gray;"></a>
    			
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
                    <img src="{{ asset('images/alarm.png') }}" alt="" style="width: 18px;">
    				
                </button>
                @endif
                <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="<?php echo $base_url;?>/images/edit.png"/></button>
                @if($isAdmin)
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
                @endif
                {{-- <button type="button" style="cursor:pointer" class="btn btn-image create-cv" title="Create CV" data-id="{{$vendor->id}}"><i class="fa fa-file" aria-hidden="true"></i></button> --}}
                <a href="{{route('vendors.create.cv', $vendor->id)}}" class="btn btn-sm" title="Vendor Create" target="_blank"><i class="fa fa-file"></i> </a>
                <a href="javascript:void(0)" class="btn btn-sm update-flowchart-date" data-id="{{$vendor->id}}" title="Vendor Flow Chart" @if($vendor->fc_status==1) style="color: #0062cc;" @endif>
                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                </a>
            </div>
        </td>
    </tr>
@endif
@endforeach

<script>
    function updateVendorStatus(obj, task_id) {
        let id = task_id;
        let status = $(obj).val();
        let self = this;

        console.log(status, id)
        if(status!=''){
            $.ajax({
                url: "{{ route('vendor.status.update')}}",
                method: "POST",
                data: {
                    id: id,
                    status: status,
                    user_id:<?php echo Auth::user()->id;?>
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    toastr["success"]("Status updated!", "Message")
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }
    }

    $(document).on('click', '.show-status-history', function() {
        var data = $(this).data('history');
        var issueId = $(this).data('id');
        $('#status_history_modal table tbody').html('');
        $.ajax({
            url: "{{ route('vendor.status.history.get') }}",
            data: {id: issueId},
            success: function (data) {
                if(data.data.length > 0) {
                    $.each(data.data, function(i, item) {
                        if(item['is_approved'] == 1) {
                            var checked = 'checked';
                        }
                        else {
                            var checked = ''; 
                        }
                        $('#status_history_modal table tbody').append(
                            '<tr>\
                                <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                                <td>'+item['status']+'</td>\
                                <td>'+item['user']['name']+'</td>\
                            </tr>'
                        );
                    });
                }
            }
        });
        $('#status_history_modal').modal('show');
    });

    $(document).on('click', '.show-remarks-history', function() {
        var data = $(this).data('history');
        var issueId = $(this).data('id');
        $('#remark_history_modal table tbody').html('');
        $.ajax({
            url: "{{ route('vendor.remark.history.get') }}",
            data: {id: issueId},
            success: function (data) {
                if(data.data.length > 0) {
                    $.each(data.data, function(i, item) {
                        $('#remark_history_modal table tbody').append(
                            '<tr>\
                                <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                                <td>'+item['remarks']+'</td>\
                                <td>'+item['user']['name']+'</td>\
                            </tr>'
                        );
                    });
                }
            }
        });
        $('#remark_history_modal').modal('show');
    });

    $(document).on('click', '.update-flowchart-date', function() {
        var vendor_id = $(this).data('id');

        var msg = "Are you sure you want to update flow chart status?";

        if (confirm(msg)) {

            $("#loading-image").show();
            $.ajax({
                url: "{{ route('vendors.flowchart')}}",
                method: "POST",
                data: {                    
                    id: vendor_id,
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    $("#loading-image").hide();
                    toastr["success"]("Vendor flow-chart status has been updated!", "Message")
                    window.location.reload();
                },
                error: function (error) {
                    $("#loading-image").hide();
                    toastr["error"](error.responseJSON.message);
                }
            });
        } else {
            return false;
        }
    });
</script>
   
