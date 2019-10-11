@extends('layouts.app')

@section('title', 'Vendor Info')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Vendor Info</h2>
            <div class="pull-left">
                <form class="form-inline" action="{{ route('vendor.index') }}" method="GET">
                    <div class="form-group">
                        <input name="term" type="text" class="form-control"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="Search">
                    </div>

                    {{-- <div class="form-group ml-3">
                      <select class="form-control" name="type">
                        <option value="">Select Type</option>
                        ndr<option value="has_error" {{ isset($type) && $type == 'has_error' ? 'selected' : '' }}>Has Error</option>
                      </select>
                    </div> --}}

                    <div class="form-group">
                        <input type="checkbox" name="with_archived" id="with_archived" {{ Request::get('with_archived')=='on'? 'checked' : '' }}>
                        <label for="with_archived">Archived</label>
                    </div>
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createVendorCategorytModal">Create Category</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Category Assignments</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>Category</th>
                                    <th>Responsible User</th>
                                </tr>
                                @foreach($vendor_categories as $cat)
                                    <tr>
                                        <td>{{ $cat->title }}</td>
                                        <td>
                                            <select class="form-control update-category-user" data-categoryId="{{$cat->id}}" name="user_id" id="user_id_{{$cat->id}}">
                                                <option value="">None</option>
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}" {{$user->id==$cat->user_id ? 'selected': ''}}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="5%"><a href="/vendor{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=category{{ ($orderby == 'ASC') ? '&orderby=DESC' : '' }}">Category</a></th>
                <th width="10%">Name</th>
                <th width="10%">Phone</th>
                <th width="10%">Email</th>
                <th width="10%">Address</th>
                {{-- <th width="10%">Social handle</th>
                <th width="10%">Website</th> --}}
                <th width="20%">Send</th>
                <th width="20%">Communication</th>
                <th width="10%">Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->id }}</td>
                    <td class="expand-row table-hover-cell">
                <span class="td-mini-container">
                  {{ strlen($vendor->category_name) > 7 ? substr($vendor->category_name, 0, 7) : $vendor->category_name }}
                </span>

                        <span class="td-full-container hidden">
                  {{ $vendor->category_name }}
                </span>
                    </td>
                    <td style="word-break: break-all;">{{ $vendor->name }}</td>
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
                    {{-- <td>
                      @if ($supplier->agents)
                        <ul>
                          @foreach ($supplier->agents as $agent)
                            <li>
                              <strong>{{ $agent->name }}</strong> <br>
                              {{ $agent->phone }} - {{ $agent->email }} <br>
                              <span class="text-muted">{{ $agent->address }}</span> <br>
                              <button type="button" class="btn btn-xs btn-secondary edit-agent-button" data-toggle="modal" data-target="#editAgentModal" data-agent="{{ $agent }}">Edit</button>
                            </li>
                          @endforeach
                        </ul>
                      @endif
                    </td> --}}

                    {{-- <td>{{ $supplier->gst }}</td> --}}
                    {{-- <td class="{{ $supplier->email_seen == 0 ? 'text-danger' : '' }}"  style="word-break: break-all;">
                      {{ strlen(strip_tags($supplier->email_message)) > 0 ? 'Email' : '' }}
                    </td> --}}
                    <td class="table-hover-cell {{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                        <span class="td-full-container">
                            {{ $vendor->message }}
                            <button data-toggle="tooltip" type="button" class="btn btn-xs btn-image load-more-communication" data-id="{{ $vendor->id }}" title="Load More..."><img src="/images/chat.png" alt=""></button>
                </span>

                        {{-- @if ($supplier->message != '')
                          <br>
                          <button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $supplier->id }}">Load More</button>

                          <ul class="more-communication-container">

                          </ul>
                        @endif --}}
                    </td>
                    <td>
                        <div class="d-flex">
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
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {!! $vendors->appends(Request::except('page'))->links() !!}

    @include('partials.modals.remarks')
    @include('vendors.partials.modal-emailToAll')
    @include('vendors.partials.vendor-modals')
    {{-- @include('vendors.partials.agent-modals') --}}
    @include('vendors.partials.vendor-category-modals')

    <div id="reminderModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Set/Edit Reminder</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="frequency">Frequency (in Minutes)</label>
                        <select class="form-control" name="frequency" id="frequency">
                            <option value="0">Disabled</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="45">45</option>
                            <option value="50">50</option>
                            <option value="55">55</option>
                            <option value="60">60</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Reminder Message</label>
                        <textarea name="reminder_message" id="reminder_message" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary save-reminder">Save</button>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication (Last 30)</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('customers.zoomMeeting');
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{asset('js/zoom-meetings.js')}}"></script>
    <script type="text/javascript">

        var vendorToRemind = null;

        $(document).on('click', '.set-reminder', function () {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');

            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            vendorToRemind = vendorId;

        });

        $(document).on('click', '.save-reminder', function () {
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();

            $.ajax({
                url: "{{action('VendorController@updateReminder')}}",
                type: 'POST',
                success: function () {
                    toastr['success']('Reminder updated successfully!');
                },
                data: {
                    vendor_id: vendorToRemind,
                    frequency: frequency,
                    message: message,
                    _token: "{{ csrf_token() }}"
                }
            });
        });

        $(document).on('click', '.edit-vendor', function () {
            var vendor = $(this).data('vendor');
            var url = "{{ url('vendor') }}/" + vendor.id;

            $('#vendorEditModal form').attr('action', url);
            $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
            $('#vendor_name').val(vendor.name);
            $('#vendor_address').val(vendor.address);
            $('#vendor_phone').val(vendor.phone);
            $('#vendor_email').val(vendor.email);
            $('#vendor_social_handle').val(vendor.social_handle);
            $('#vendor_website').val(vendor.website);
            $('#vendor_login').val(vendor.login);
            $('#vendor_password').val(vendor.password);
            $('#vendor_gst').val(vendor.gst);
            $('#vendor_account_name').val(vendor.account_name);
            $('#vendor_account_iban').val(vendor.account_iban);
            $('#vendor_account_swift').val(vendor.account_swift);
        });

        $(document).on('click', '.create-agent', function () {
            var id = $(this).data('id');

            $('#agent_vendor_id').val(id);
        });

        $(document).on('click', '.edit-agent-button', function () {
            var agent = $(this).data('agent');
            var url = "{{ url('agent') }}/" + agent.id;

            $('#editAgentModal form').attr('action', url);
            $('#agent_name').val(agent.name);
            $('#agent_address').val(agent.address);
            $('#agent_phone').val(agent.phone);
            $('#agent_email').val(agent.email);
        });

        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "vendor"
                },
            }).done(response => {
                var html = '';

                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'vendor'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';

                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $(this).siblings('input').val();

            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        $(thiss).siblings('input').val('');

                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

        $(document).on('change', '.update-category-user', function () {
            let catId = $(this).attr('data-categoryId');
            let userId = $(this).val();

            $.ajax({
                url: '{{ action('VendorController@assignUserToCategory') }}',
                data: {
                    user_id: userId,
                    category_id: catId
                },
                success: function (response) {
                    toastr['success']('User assigned to category completely!')
                }
            });

        });

        $(document).on('click', '.load-more-communication', function () {
            var thiss = $(this);
            var vendor_id = $(this).data('id');

            $.ajax({
                type: "GET",
                url: "{{ url('chat-messages') }}/vendor/" + vendor_id + "/loadMoreMessages",
                data: {
                    limit: 1000
                },
                beforeSend: function () {
                    //$(thiss).text('Loading...');
                }
            }).done(function (response) {
                var li = '<div class="speech-wrapper">';
                (response.messages).forEach(function (message) {
                    // Set empty image var
                    var media = '';
                    var imgSrc = '';

                    // Check for attached media (ERP attached media)
                    if (message.media.length > 0) {
                        for (i = 0; i < message.media.length; i++) {
                            // Set image type
                            var imageType = message.media[i].substr(-3).toLowerCase();
                            if (imageType == 'jpg') {
                                imgSrc = message.media[i];
                            } else if (imageType == 'png') {
                                imgSrc = message.media[i];
                            } else if (imageType == 'gif') {
                                imgSrc = message.media[i];
                            } else if (imageType == 'pdf') {
                                imgSrc = '/images/icon-pdf.svg';
                            } else if (imageType == 'zip') {
                                imgSrc = '/images/icon-zip.svg';
                            }

                            // Set media
                            if (imgSrc != '') {
                                media = media + '<div class="col-4"><a href="' + message.media[i] + '" target="_blank"><img src="' + imgSrc + '" style="max-width: 100%;"></a></div>';
                            }
                        }

                        // Do we have media?
                        if (media != '') {
                            media = '<div style="max-width: 100%;"><div class="row">' + media + '</div></div>';
                        }
                    }

                    // Check for media URL
                    if (message.media_url != null) {
                        media = '<a href="' + message.media_url + '" target="_blank"><img src="' + message.media_url + '" style="max-width: 100%;"></a>' + media;
                    }


                    if (message.inout == 'in') {
                        li += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + media + message.message + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        li += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + media + message.message + '</p><br/><span class="timestamp">' + message.datetime.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
                    } else {
                        li += '<div>' + index + '</div>';
                    }
                });

                li += '</div>';

                $("#chat-list-history").find(".modal-body").html(li);
                $(thiss).html("<img src='/images/chat.png' alt=''>");
                $("#chat-list-history").modal("show");

            }).fail(function (response) {
                $(thiss).text('Load More');

                alert('Could not load more messages');

                console.log(response);
            });
        });

        $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });
    </script>
@endsection
