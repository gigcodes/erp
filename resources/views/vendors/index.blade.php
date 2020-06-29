@extends('layouts.app')

@section('favicon' , 'vendor.png')

@section('title', 'Vendor Info')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style type="text/css">
    .numberSend {
          width: 160px;
          background-color: transparent;
          color: transparent;
          text-align: center;
          border-radius: 6px;
          position: absolute;
          z-index: 1;
          left: 19%;
          margin-left: -80px;
          display: none;
    }
 .input-sm{
    width: 60px;
    }

    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

  </style>
@endsection

@section('large_content')
     <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Vendor Info</h2>
            <div class="pull-left">
                <form class="form-inline" action="{{ route('vendors.index') }}" method="GET">
                    <div class="form-group" style="width: 441px; margin-right: 10px;">
                       <select name="term" type="text" class="form-control" placeholder="Search" id="vendor-search" data-allow-clear="true">
                            <?php
                                if (request()->get('term')) {
                                    echo '<option value="'.request()->get('term').'" selected>'.request()->get('term').'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <input type="checkbox" name="with_archived" id="with_archived" {{ Request::get('with_archived')=='on'? 'checked' : '' }}>
                        <label for="with_archived">Archived</label>
                    </div>
                    <div class="form-group ml-3" style="margin-left: 10px;">
                       <input placeholder="Communication History" type="text" name="communication_history" value="{{request()->get('communication_history')}}" class="form-control-sm form-control">
                    </div>
                    <div class="form-group ml-3">
                        <label for="with_archived">Status</label>
                        <?php echo Form::select("status",[
                            "" => "- Select -",
                            "0" => "De-Active",
                            "1" => "Active"
                        ],request('status'),["class"=> "form-control"]) ?>
                    </div>
                    <div class="form-group ml-3">
                        <label for="with_updated_by">Updated by</label>
                        <?php echo Form::select("updated_by",
                            ["" => "-- Select --"] +\App\User::pluck("name","id")->toArray(),
                            request('updated_by'),
                            ["class"=> "form-control"]
                        ); ?>
                    </div>
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form>
            </div>
        </div>
        <div class="col-lg-12 margin-tb">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#conferenceModal">Conference Call</button>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createVendorCategorytModal">Create Category</button>
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</button>
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
        <table class="table table-bordered" id="vendor-table">
            <thead>
            <tr>
                <th width="5%"><a href="/vendors{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'ASC') ? '&orderby=DESC' : '' }}">ID</a></th>
                <th width="5%"><a href="/vendors{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=category{{ ($orderby == 'ASC') ? '&orderby=DESC' : '' }}">Category</a></th>
                <th width="10%">Name</th>
                <th width="10%">Phone</th>
                <th width="10%">Email</th>
                <th width="10%">Address</th>
                {{-- <th width="10%">Social handle</th>
                <th width="10%">Website</th> --}}
                <th width="20%">Send</th>
                <th width="10%">Communication</th>
                <th width="5%">Created At</th>
                <th width="5%">Updated At</th>
                <th width="5%">Updated By</th>
                <th width="2%">Action</th>
            </tr>

            <tr>
                <th width="5%"><input type="text" id="id" class="search form-control" placeholder="Id"></th>
                <th width="5%"><input type="text" id="category" class="search form-control" placeholder="Category"></th>
                <th width="10%"><input type="text" id="name" class="search form-control" placeholder="Name" name="name"></th>
                <th width="10%"><input type="text" id="phone" class="search form-control" placeholder="Phone"></th>
                <th width="10%"><input type="text" id="email" class="search form-control" placeholder="Email"></th>
                <th width="10%"><input type="text" id="address" class="search form-control" placeholder="Address"></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </thead>

            <tbody id="vendor-body">

            @include('vendors.partials.data')

                     </tbody>
        </table>
    </div>

    {!! $vendors->render(); !!}
    @include('partials.modals.remarks')
    @include('vendors.partials.modal-emailToAll')
    @include('vendors.partials.vendor-modals')
    {{-- @include('vendors.partials.agent-modals') --}}
    @include('vendors.partials.vendor-category-modals')
    @include('vendors.partials.modal-conference')

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
                        <label for="frequency">Frequency</label>
                        <?php echo Form::select("frequency",drop_down_frequency(),null,["class" => "form-control", "id" => "frequency"]); ?>
                    </div>
                    <div class="form-group">
                        <label for="frequency">Reminder Start From</label>
                        <input type="text" name="reminder_from" id="reminder_from" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="reminder_message">Check Last Message?</label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply" name="reminder_last_reply" value="1" checked>Yes
                        </label>
                        <label class="radio-inline">
                          <input type="radio" id="reminder_last_reply_no" name="reminder_last_reply" value="0">No
                        </label>
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
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="email-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Email Communication</h4>
                    <input type="text" name="search_email_pop"  class="form-control search_email_pop" placeholder="Search Email" style="width: 200px;">
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
    <div id="forwardModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('whatsapp.forward') }}" method="POST">
            @csrf
            <input type="hidden" name="message_id" id="forward_message_id" value="">

            <div class="modal-header">
              <h4 class="modal-title">Forward Message</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                  <strong>Client:</strong>
                  <select class="selectpicker form-control" name="customer_id[]" title="Choose a Customer" required multiple></select>

                  @if ($errors->has('customer_id'))
                      <div class="alert alert-danger">{{$errors->first('customer_id')}}</div>
                  @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Forward Message</button>
            </div>
          </form>
        </div>

      </div>
    </div>

    <div class="modal fade" id="createUser" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
               
                    <div class="modal-body">
                        <div><button class="btn btn-secondary m-1" id="vendor_id">Create ERP User from Vendor</button></div>
                        <div><button class="btn btn-secondary m-1" onclick="inviteGithub()">Invite to Github</button></div>
                        <div><button class="btn btn-secondary m-1" onclick="inviteHubstaff()">Invite to Hubstaff</button></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{asset('js/zoom-meetings.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script type="text/javascript">

        <?php if(!empty($updatedProducts)) {
            foreach($updatedProducts as $updatedProduct) {
                echo "toastr['success']('".$updatedProduct['name']." has submitted ".$updatedProduct['total_records']." updated');";
            }
        } ?>


        $('.selectpicker').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: '/erp-leads/customer-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Customer by id, Name, No',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.loading) {
                    return customer.name;
                }

                if (customer.name) {
                    return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });
        var vendorToRemind = null;
        $('#vendor-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: '/vendor-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Vendor by name, address, phone, email, category, title',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {

                if (customer.name) {
                    return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });

        $(document).on('click', '.emailToAllModal', function () {
            var select_vendor = [];
            $('.select_vendor').each(function(){
                if ($(this).prop("checked")) {
                    select_vendor.push($(this).val());
                }
            });

            if (select_vendor.length === 0) {
                alert('Please Select vendors!!');
                return false;
            }

            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());

            $('#emailToAllModal').modal("show");

        });

        $(document).on('click', '.send-email-to-vender', function () {
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
            $('#emailToAllModal').modal("show");
        });

        $(document).on('click', '.set-reminder', function () {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');
            let reminder_from = $(this).data('reminder_from');
            let reminder_last_reply = $(this).data('reminder_last_reply');

            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            $("#reminderModal").find("#reminder_from").val(reminder_from);
            if(reminder_last_reply == 1) {
            	$("#reminderModal").find("#reminder_last_reply").prop("checked",true);
            }else{
            	$("#reminderModal").find("#reminder_last_reply_no").prop("checked",true);
            }
            vendorToRemind = vendorId;
        });

        $(document).on('click', '.save-reminder', function () {
        	var reminderModal = $("#reminderModal");
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();
            let reminder_from = reminderModal.find("#reminder_from").val();
            let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;

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
                    reminder_from: reminder_from,
                    reminder_last_reply: reminder_last_reply,
                    _token: "{{ csrf_token() }}"
                }
            });
        });

        $(document).on('click', '.edit-vendor', function () {
            var vendor = $(this).data('vendor');
            var url = "{{ url('vendors') }}/" + vendor.id;

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

        $(document).on('click', '.load-email-modal', function () {
            var id = $(this).data('id');
             $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('vendors.email') }}',
                data: {
                    id: id
                },
            }).done(function (response) {
                var html = '<div class="speech-wrapper">';
                response.forEach(function (message) {
                    var content = '';
                    content += 'To : '+message.to+'<br>';
                    content += 'From : '+message.from+'<br>';
                    if (message.cc) {
                        content += 'CC : '+message.cc+'<br>';
                    }
                    if (message.bcc) {
                        content += 'BCC : '+message.bcc+'<br>';
                    }
                    content += 'Subject : '+message.subject+'<br>';
                    content += 'Message : '+message.message+'<br>';
                    if (message.attachment.length) {
                        content += 'Attachment : ';
                    }
                    for (var i = 0; i < message.attachment.length; i++) {
                        var imageUrl = message.attachment[i];
                        imageUrl = imageUrl.trim();

                        // Set empty imgSrc
                        var imgSrc = '';

                        // Set image type
                        var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();

                        // Set correct icon/image
                        if (imageType == '.jpg' || imageType == 'jpeg') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.png') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.gif') {
                            imgSrc = imageUrl;
                        } else if (imageType == 'docx' || imageType == '.doc') {
                            imgSrc = '/images/icon-word.svg';
                        } else if (imageType == '.xlsx' || imageType == '.xls' || imageType == '.csv') {
                            imgSrc = '/images/icon-excel.svg';
                        } else if (imageType == '.pdf') {
                            imgSrc = '/images/icon-pdf.svg';
                        } else if (imageType == '.zip' || imageType == '.tgz' || imageType == 'r.gz') {
                            imgSrc = '/images/icon-zip.svg';
                        } else {
                            imgSrc = '/images/icon-file-unknown.svg';
                        }

                        // Set media
                        if (imgSrc != '') {
                            content += '<div class="col-4"><a href="' + message.attachment[i] + '" target="_blank"><label class="label-attached-img" for="cb1_' + i + '"><img src="' + imgSrc + '" style="max-width: 100%;"></label></a></div>';
                        }
                    }
                    if (message.inout == 'in') {
                        html += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        html += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
                    }
                });

                html += '</div>';

                $("#email-list-history").find(".modal-body").html(html);
                $("#email-list-history").modal("show");
            }).fail(function (response) {
                console.log(response);

                alert('Could not load email');
            });
        });
        $(document).on("keyup", '.search_email_pop', function() {
            var value = $(this).val().toLowerCase();
            $(".speech-wrapper .bubble").filter(function() {
                $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
            });
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
                        thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
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

        $(document).on('click', '.block-twilio', function () {
            var vendor_id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('vendors.block') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: vendor_id
                },
                beforeSend: function () {
                    $(thiss).text('Blocking...');
                }
            }).done(function (response) {
                if (response.is_blocked == 1) {
                    $(thiss).html('<img src="/images/blocked-twilio.png" />');
                } else {
                    $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                }
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');

                alert('Could not block customer!');

                console.log(response);
            });
        });

         $(document).on('click', '.call-select', function() {
        var id = $(this).data('id');
        $('#show'+id).toggle();
        console.log('#show'+id);
      });

          $(document).ready(function() {
              src = "{{ route('vendors.index') }}";
              $(".search").autocomplete({
                  source: function (request, response) {
                      id = $('#id').val();
                      name = $('#name').val();
                      email = $('#email').val();
                      phone = $('#phone').val();
                      address = $('#address').val();
                      category = $('#category').val();

                      $.ajax({
                          url: src,
                          dataType: "json",
                          data: {
                              id: id,
                              name: name,
                              phone: phone,
                              email: email,
                              address: address,
                              category: category,
                          },
                          beforeSend: function () {
                              $("#loading-image").show();
                          },

                      }).done(function (data) {
                          $("#loading-image").hide();
                          console.log(data);
                          $("#vendor-table tbody").empty().html(data.tbody);
                          if (data.links.length > 10) {
                              $('ul.pagination').replaceWith(data.links);
                          } else {
                              $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                          }
                          $(".select2-quick-reply").select2({});

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  },
                  minLength: 1,

              });


              $(document).ready(function () {
                  src = "{{ route('vendors.index') }}";
                  $("#search_id").autocomplete({
                      source: function (request, response) {
                          $.ajax({
                              url: src,
                              dataType: "json",
                              data: {
                                  term: request.term
                              },
                              beforeSend: function () {
                                  $("#loading-image").show();
                              },

                          }).done(function (data) {
                              $("#loading-image").hide();
                              $("#vendor-table tbody").empty().html(data.tbody);
                              if (data.links.length > 10) {
                                  $('ul.pagination').replaceWith(data.links);
                              } else {
                                  $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                              }
                              $(".select2-quick-reply").select2({});

                          }).fail(function (jqXHR, ajaxOptions, thrownError) {
                              alert('No response from server');
                          });
                      },
                      minLength: 1,

                  });
              });

              $(document).on("change", ".quickComment", function (e) {

                  var message = $(this).val();

                  if ($.isNumeric(message) == false) {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: "/vendors/reply/add",
                          dataType: "json",
                          method: "POST",
                          data: {reply: message}
                      }).done(function (data) {

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
                  $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());

              });

              $(".select2-quick-reply").select2({tags: true});

              $(document).on("click", ".delete_quick_comment", function (e) {
                  var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected").val();
                  if (typeof deleteAuto != "undefined") {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: "/vendors/reply/delete",
                          dataType: "json",
                          method: "GET",
                          data: {id: deleteAuto}
                      }).done(function (data) {
                          if (data.code == 200) {
                              $(".quickComment").empty();
                              $.each(data.data, function (k, v) {
                                  $(".quickComment").append("<option value='" + k + "'>" + v + "</option>");
                              });
                              $(".quickComment").select2({tags: true});
                          }

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
              });
          });

          function createUserFromVendor(id, email) {
                $('#vendor_id').attr('data-id', id);
                if (email) {
                    $('#createUser').attr('data-email', email);
                }
                $('#createUser').modal('show');
            }

            $('#createUser').on('hidden.bs.modal', function() {
                $('#createUser').removeAttr('data-email');
            })

         $(document).on("click", "#vendor_id", function () {
            $('#createUser').modal('hide');
            id = $(this).attr('data-id');
                $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: "/vendors/create-user",
                          dataType: "json",
                          method: "POST",
                          data: {id: id},
                          beforeSend: function () {
                              $("#loading-image").show();
                          },
                      }).done(function (data) {
                        $("#loading-image").hide();
                          if (data.code == 200) {
                              alert(data.data);
                          }

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          $("#loading-image").hide(); 
                          alert('No response from server');
                      });
          });

          function inviteGithub() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');

            $.ajax({
                type: "POST",
                url: "vendors/inviteGithub",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            });

            console.log(email);
        }

        function inviteHubstaff() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            console.log(email);

            $.ajax({
                type: "POST",
                url: "vendors/inviteHubstaff",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            })
        }

        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $(document).on("change",".vendor-update-status",function(){
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status : $this.prop('checked')
                }
            }).done(function(data){
                if(data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function(error) {
                
            })
        });

    </script>
@endsection
