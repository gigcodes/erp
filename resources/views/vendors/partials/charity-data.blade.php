@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM')
@endphp
@foreach ($vendors as $vendor)
<tr>
    <td>{{ $vendor->id }}</td> 
    <td style="word-break: break-all;" class="expand-row">
        <div class="row">
            <div class="col-md-6 cls_remove_rightpadding">
                <span class="td-mini-container">
                  {{ strlen($vendor->name) > 7 ? substr($vendor->name, 0, 7) : $vendor->name }}
                </span>
                <span class="td-full-container hidden">
                  {{ $vendor->name }}
                </span>
            </div> 
        </div>
    </td>
    <td>{{ $vendor->phone }} 
        @if ($vendor->status == 1)
          <button type="button" class="btn btn-image vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="On" data-id="{{ $vendor->id }}" id="do_not_disturb"><img src="{{asset('images/do-disturb.png')}}" /></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="false" />  
        @else
          <button type="button" class="btn btn-image vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="Off" data-id="{{ $vendor->id }}" id="do_not_disturb"><img src="{{asset('images/do-not-disturb.png')}}" /></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="true" />
        @endif
    </td>
    <td class="expand-row table-hover-cell" style="word-break: break-all;">
        {{ $vendor->email }}
    </td>
    <td class="expand-row table-hover-cell" style="word-break: break-all;">
        {{ $vendor->store_websites_name }}
    </td>
    <th width="7%">Email</th>
    {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
    <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}
    <td class="table-hover-cell {{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;padding: 5px;"> 
   

        <div class="row">
            <div class="col-md-8 form-inline cls_remove_rightpadding">
                <div class="row cls_textarea_subbox">
                    <div class="col-md-11 cls_remove_rightpadding">
                        <textarea rows="1" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $vendor->id }}" name="message" placeholder="Message"></textarea>
                    </div>
                    <div class="col-md-1 cls_remove_allpadding">
                        <button class="btn btn-sm btn-image send-message1" data-vendorid="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/filled-sent.png"/></button>
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="charity" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
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
                                <a class="btn btn-image delete_quick_comment"><img src="<?php echo $base_url;?>/images/delete.png" style="cursor: default; width: 16px;"></a>
                            </div>
                        </div> 
                    </div>
                </div>        
            </div> 
        </div>
        <div class="row cls_mesg_box">
            <div class="col-md-12">
                <div class="col-md-12 expand-row" style="padding: 3px;">
                    
                </div>
            </div>
        </div>
    </td>

    <td>
        <div class="cls_action_btn"> 
            <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#charityEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="<?php echo $base_url;?>/images/edit.png"/></button>
            {!! Form::open(['method' => 'DELETE','route' => ['customer.charity.delete', $vendor->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="<?php echo $base_url;?>/images/delete.png"/></button>
            {!! Form::close() !!}
            @if($vendor->product_id)
                <button type="submit" class="btn btn-image upload-single" title="push to magento" data-product-id="{{$vendor->product_id}}"  data-id="{{$vendor->id}}"><img src="<?php echo $base_url;?>/images/upload.png"/></button>
            @endif
            @if($vendor->email)
                <button type="button" class="btn send-email-common-btn" data-toemail="{{ $vendor->email }}" data-object="charity" data-id="{{ $vendor->id }}"><i class="fa fa-envelope-square"></i></button>
            @endif
            <button type="button" class="btn add-charity-country" title="add charity-price country-wise" data-product-id="{{ $vendor->product_id }}" data-id="{{ $vendor->id }}"><i class="fa fa-list"></i></button>
            <button type="button" onclick="addwebsite('{{ $vendor->id }}');" class="btn add-website" title="add edit website" data-product-id="{{ $vendor->product_id }}" data-id="{{ $vendor->id }}"><i class="fa fa-list"></i></button>
        </div>
    </td>
</tr>
@endforeach
