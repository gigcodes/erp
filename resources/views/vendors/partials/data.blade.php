@foreach ($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->id }}</td>
                    <td class="expand-row table-hover-cell">
                <span class="td-mini-container">
                 @if(isset($vendor->category->title)) {{ strlen($vendor->category->title ) > 7 ? substr($vendor->category->title , 0, 7) : $vendor->category->title  }} @endif
                </span>
                       {{ strlen($vendor->category_name) > 7 ? substr($vendor->category_name, 0, 7) : $vendor->category_name }}
                </span>
                    </td>
                    <td style="word-break: break-all;">{{ $vendor->name }}
                    @if($vendor->phone)
                        <div>
                            <button type="button" class="btn btn-image call-select popup" data-id="{{ $vendor->id }}"><img src="/images/call.png"/></button>
                              <div class="numberSend" id="show{{ $vendor->id }}">
                              <select class="form-control call-twilio" data-context="vendors" data-id="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                                <option disabled selected>Select Number</option>
                                @foreach(\Config::get("twilio.caller_id") as $caller)
                                <option value="{{ $caller }}">{{ $caller }}</option>
                                @endforeach
                              </select>
                              </div>

                        @if ($vendor->is_blocked == 1)
                                <button type="button" class="btn btn-image block-twilio" data-id="{{ $vendor->id }}"><img src="/images/blocked-twilio.png"/></button>
                            @else
                                <button type="button" class="btn btn-image block-twilio" data-id="{{ $vendor->id }}"><img src="/images/unblocked-twilio.png"/></button>
                            @endif
                        </div>
                    @endif
                    </td>
                    <td>{{ $vendor->phone }}</td>
                    <td class="expand-row table-hover-cell" style="word-break: break-all;">
                <span class="td-mini-container">
                  {{ strlen($vendor->email) > 10 ? substr($vendor->email, 0, 10) : $vendor->email }}
                </span>

                        <span class="td-full-container hidden">
                  {{ $vendor->email }}
                </span>
                    </td>
                    <td style="word-break: break-all;">{{ $vendor->address }}</td>

                    {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
                    <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}
                    <td>
                        <div class="d-flex">
                            <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                            <button class="btn btn-sm btn-image send-message" data-vendorid="{{ $vendor->id }}"><img src="/images/filled-sent.png"/></button>
                        </div>
                    </td>
                    <td class="table-hover-cell {{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                        <span class="td-full-container">
                            <div class="chat_messages">
                                @if(isset($vendor->chat_messages[0])) {{ $vendor->chat_messages[0]->message }} @endif
                                @if(isset($vendor->message)) {{ $vendor->message }} @endif    
                            </div>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="images" data-all="1" title="Load Auto Images attacheds"><img src="/images/archive.png" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ Auth::user()->hasRole('Admin') }}" data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="vendor" data-id="{{$vendor->id}}" data-attached="1" data-load-type="pdf" data-all="1" title="Load PDF"><img src="/images/icon-pdf.svg" alt=""></button>
                            <button type="button" class="btn btn-xs btn-image load-email-modal" title="Load Email" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button>
                        </span>
                    </td>
                    <td>
                        <div style="width: 233px;">
                            <a href="{{ route('vendor.show', $vendor->id) }}" class="btn btn-image" href=""><img src="/images/view.png"/></a>

                            <button data-toggle="modal" data-target="#reminderModal" class="btn btn-image set-reminder" data-id="{{ $vendor->id }}" data-frequency="{{ $vendor->frequency ?? '0' }}" data-reminder_message="{{ $vendor->reminder_message }}">
                                <img src="{{ asset('images/alarm.png') }}" alt="" style="width: 18px;">
                            </button>

                            <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="/images/edit.png"/></button>
                            <a href="{{route('vendor.payments', $vendor->id)}}" class="btn btn-sm" title="Vendor Payments" target="_blank"><i class="fa fa-money"></i> </a>
                            <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $vendor->id }}"><img src="/images/remark.png"/></a>
                                <button data-toggle="modal" data-target="#zoomModal" class="btn btn-image set-meetings" data-id="{{ $vendor->id }}" data-type="vendor"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
                                {!! Form::open(['method' => 'DELETE','route' => ['vendor.destroy', $vendor->id],'style'=>'display:inline']) !!}
                                <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                            {!! Form::close() !!}
                            <input type="checkbox" class="select_vendor" name="select_vendor[]" value="{{$vendor->id}}" {{ request()->get('select_all') == 'true' ? 'checked' : '' }}>
                            <button type="button" class="btn send-email-to-vender" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button>
                        </div>
                    </td>
                </tr>
            @endforeach
   
