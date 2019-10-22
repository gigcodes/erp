@extends('layouts.app')

@section('title', 'Suppliers List')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Suppliers List</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('supplier.index') }}" method="GET">
                <div class="form-group">
                  <input name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>

                  <div class="form-group ml-3">
                      <input type="text" name="source" id="source" placeholder="Source..">
                  </div>

                <div class="form-group ml-3">
                  <select class="form-control" name="type">
                    <option value="">Select Type</option>
                    <option value="has_error" {{ isset($type) && $type == 'has_error' ? 'selected' : '' }}>Has Error</option>
                    <option value="not_updated" {{ isset($type) && $type == 'not_updated' ? 'selected' : '' }}>Not Updated</option>
                    <option value="updated" {{ isset($type) && $type == 'updated' ? 'selected' : '' }}>Updated</option>
                  </select>
                </div>

                  <div class="form-group ml-3">
                      <input type="checkbox" name="status" id="status" value="1" {{ request()->get('status') == '1' ? 'checked' : ''}}> Active
                  </div>
                  <div class="form-group ml-3">
                       {!!Form::select('supplier_status_id', ["" => "select supplier status"] + $supplierstatus,request()->get('supplier_status_id'), ['class' => 'form-control form-control-sm'])!!}
                  </div>
                  <div class="form-group ml-3">
                       {!!Form::select('supplier_category_id', ["" => "select category"] + $suppliercategory, request()->get('supplier_category_id'), ['class' => 'form-control form-control-sm'])!!}
                  </div>

{{--                  <div class="form-group ml-3">--}}
{{--                      <select name="status" id=""></select>--}}
{{--                  </div>--}}

                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
              </form>
            </div>

            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
                <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#supplierCreateModal">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    @include('purchase.partials.modal-email')
    @include('suppliers.partials.modal-emailToAll')

    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="10%">Name</th>
            <th width="10%">Address</th>
              <th>Source</th>
              <th>Designers</th>
            <th width="10%">Social handle</th>
            {{-- <th>Agents</th> --}}
            {{-- <th width="5%">GST</th> --}}
            <th width="20%">Order</th>
            {{-- <th width="20%">Emails</th> --}}
            <th width="25%">Communication</th>
            <th>Status</th>
            <th width="15%">Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($suppliers as $supplier)
            <tr>
              <td>{{ $supplier->id }}</td>
              <td>
                {{ $supplier->supplier }}

                @if ($supplier->is_flagged == 1)
                  <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/flagged.png" /></button>
                @else
                  <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/unflagged.png" /></button>
                @endif
                  @if($supplier->phone)
                  <button type="button" class="btn btn-image call-twilio" data-context="suppliers" data-id="{{ $supplier->id }}" data-phone="{{ $supplier->phone }}"><img src="/images/call.png"/></button>
                  @if ($supplier->is_blocked == 1)
                      <button type="button" class="btn btn-image block-twilio" data-id="{{ $supplier->id }}"><img src="/images/blocked-twilio.png"/></button>
                  @else
                      <button type="button" class="btn btn-image block-twilio" data-id="{{ $supplier->id }}"><img src="/images/unblocked-twilio.png"/></button>
                  @endif
                  @endif
                  <button data-toggle="modal" data-target="#reminderModal" class="btn btn-image set-reminder" data-id="{{ $supplier->id }}" data-frequency="{{ $supplier->frequency ?? '0' }}" data-reminder_message="{{ $supplier->reminder_message }}">
                      <img src="{{ asset('images/alarm.png') }}" alt=""  style="width: 18px;">
                  </button>

                <br>
                <span class="text-muted">
                  {{ $supplier->phone }}
                  <br>
                  <a href="#" class="send-supplier-email" data-toggle="modal" data-target="#emailSendModal" data-id="{{ $supplier->id }}">{{ $supplier->email }}</a>
                  @if ($supplier->has_error == 1)
                    <span class="text-danger">!!!</span>
                  @endif
                </span>
              </td>
              <td class="expand-row">
                  <div class="td-mini-container">
                      {{ strlen($supplier->address) > 10 ? substr($supplier->address, 0, 10).'...' : $supplier->address }}
                  </div>
                  <div class="td-full-container hidden">
                      {{ $supplier->address }}
                  </div>
              </td>
                <td>{{ $supplier->source }}</td>
                <td class="expand-row">
                    @if(strlen($supplier->brands) > 4)
                        @php
                            $dns = $supplier->brands;
                            $dns = str_replace('"[', '', $dns);
                            $dns = str_replace(']"', '', $dns);
                        @endphp

                        <div class="td-mini-container">
                            {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                        </div>
                        <div class="td-full-container hidden">
                            {{ $dns }}
                        </div>
                    @else
                        N/A
                    @endif
                </td>
              <td class="expand-row" style="word-break: break-all;">
                  <div class="td-mini-container">
                      {{ strlen($supplier->social_handle) > 10 ? substr($supplier->social_handle, 0, 10).'...' : $supplier->social_handle }}
                  </div>
                  <div class="td-full-container hidden">
                      {{ $supplier->social_handle }}
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
              <td>
                @if ($supplier->purchase_id != '')
                  <a href="{{ route('purchase.show', $supplier->purchase_id) }}" target="_blank">Purchase ID {{ $supplier->purchase_id }}</a>
                  <br>
                  {{ \Carbon\Carbon::parse($supplier->purchase_created_at)->format('H:m d-m') }}
                @endif
              </td>
              {{-- <td class="{{ $supplier->email_seen == 0 ? 'text-danger' : '' }}"  style="word-break: break-all;">
                {{ strlen(strip_tags($supplier->email_message)) > 0 ? 'Email' : '' }}
              </td> --}}
              <td class="expand-row {{ $supplier->last_type == "email" && $supplier->email_seen == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                  @if($supplier->phone)
                      <input type="text" name="message" id="message_{{$supplier->id}}" placeholder="whatsapp message..." class="form-control send-message" data-id="{{$supplier->id}}">
                  @endif
                @if ($supplier->last_type == "email")
                  Email
                @elseif ($supplier->last_type == "message")
                      <div class="td-mini-container">
                          {{ strlen($supplier->message) > 10 ? substr($supplier->message, 0, 10).'...' : $supplier->message }}
                      </div>
                      <div class="td-full-container hidden">
                          {{ $supplier->message }}
                      </div>

                  @if ($supplier->message != '')
                    <button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $supplier->id }}">Load More</button>

                    <ul class="more-communication-container">

                    </ul>
                  @endif
                @endif
              </td>
                <td>
                    {{ $supplier->status ? 'Active' : 'Inactive' }}
                </td>
              <td>
                  <div style="min-width: 100px;">
                      <a href="{{ route('supplier.show', $supplier->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a>

                      {{-- <button type="button" class="btn btn-xs create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $supplier->id }}">Add Agent</button> --}}
                      <button data-toggle="modal" data-target="#zoomModal" class="btn btn-image set-meetings" data-id="{{ $supplier->id }}" data-type="supplier"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
                      <button type="button" class="btn btn-image edit-supplier d-inline" data-toggle="modal" data-target="#supplierEditModal" data-supplier="{{ json_encode($supplier) }}"><img src="/images/edit.png" /></button>
                      <button type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $supplier->id }}"><img src="/images/remark.png" /></button>
                      
                      {!! Form::open(['method' => 'DELETE','route' => ['supplier.destroy', $supplier->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                      {!! Form::close() !!}
                  </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $suppliers->appends(Request::except('page'))->links() !!}

    @include('partials.modals.remarks')

    @include('suppliers.partials.supplier-modals')
    {{-- @include('suppliers.partials.agent-modals') --}}


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
@include('customers.zoomMeeting');
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="{{asset('js/zoom-meetings.js')}}"></script>
  <script type="text/javascript">

      var supplierToRemind = null;
        $(document).ready(function() {
           $(".select-multiple").multiselect({
             buttonWidth: '100%',
             includeSelectAllOption: true
           });
        });

    $(document).on('click', '.set-reminder', function() {
        let supplierId = $(this).data('id');
        let frequency = $(this).data('frequency');
        let message = $(this).data('reminder_message');

        $('#frequency').val(frequency);
        $('#reminder_message').val(message);
        supplierToRemind = supplierId;

    });

    $(document).on('click', '.save-reminder', function() {
        let frequency = $('#frequency').val();
        let message = $('#reminder_message').val();

        $.ajax({
            url: "{{action('SupplierController@updateReminder')}}",
            type: 'POST',
            success: function() {
                toastr['success']('Reminder updated successfully!');
            },
            data: {
                supplier_id: supplierToRemind,
                frequency: frequency,
                message: message,
                _token: "{{ csrf_token() }}"
            }
        });
    });

    // cc

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

    //

    $(document).on('click', '.edit-supplier', function() {
      var supplier = $(this).data('supplier');
      var url = "{{ url('supplier') }}/" + supplier.id;

      $('#supplierEditModal form').attr('action', url);
      $('#supplier_supplier').val(supplier.supplier);
      $('#supplier_address').val(supplier.address);
      $('#supplier_phone').val(supplier.phone);
      $('#supplier_email').val(supplier.email);
      $('#supplier_social_handle').val(supplier.social_handle);
      $('#supplier_gst').val(supplier.gst);
      $('#status').val(supplier.status);
      $('#supplier_status_id').val(supplier.supplier_status_id);
      $('#supplier_category_id').val(supplier.supplier_category_id);
    });

    $(document).on('click', '.send-supplier-email', function() {
      var id = $(this).data('id');

      $('#emailSendModal').find('input[name="supplier_id"]').val(id);
    });

    $(document).on('click', '.load-more-communication', function() {
      var thiss = $(this);
      var supplier_id = $(this).data('id');

      $.ajax({
        type: "GET",
        url: "{{ url('supplier') }}/" + supplier_id + '/loadMoreMessages',
        data: {
          supplier_id: supplier_id
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
        (response.messages).forEach(function(index) {
          var li = '<li>' + index + '</li>';

          $(thiss).closest('td').find('.more-communication-container').append(li);
        });

        $(thiss).remove();
      }).fail(function(response) {
        $(thiss).text('Load More');

        alert('Could not load more messages');

        console.log(response);
      });
    });

    // $(document).on('click', '.create-agent', function() {
    //   var id = $(this).data('id');
    //
    //   $('#agent_supplier_id').val(id);
    // });

    // $(document).on('click', '.edit-agent-button', function() {
    //   var agent = $(this).data('agent');
    //   var url = "{{ url('agent') }}/" + agent.id;
    //   $('#agent_whatsapp_number option[value=""]').prop('selected', 'selected');
    //
    //   $('#editAgentModal form').attr('action', url);
    //   $('#agent_name').val(agent.name);
    //   $('#agent_address').val(agent.address);
    //   $('#agent_phone').val(agent.phone);
    //   $('#agent_whatsapp_number option[value="' + agent.whatsapp_number + '"]').prop('selected', 'selected');
    //   $('#agent_email').val(agent.email);
    // });

    $(document).on('click', '.flag-supplier', function() {
      var supplier_id = $(this).data('id');
      var thiss = $(this);

      $.ajax({
        type: "POST",
        url: "{{ route('supplier.flag') }}",
        data: {
          _token: "{{ csrf_token() }}",
          supplier_id: supplier_id
        },
        beforeSend: function() {
          $(thiss).text('Flagging...');
        }
      }).done(function(response) {
        if (response.is_flagged == 1) {
          $(thiss).html('<img src="/images/flagged.png" />');
        } else {
          $(thiss).html('<img src="/images/unflagged.png" />');
        }

      }).fail(function(response) {
        $(thiss).html('<img src="/images/unflagged.png" />');

        alert('Could not flag supplier!');

        console.log(response);
      });
    });

    $(document).on('click', '.make-remark', function(e) {
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
            id:id,
            module_type: "supplier"
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'supplier'
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            // if ($(this).data('switch') == 0) {
            //   $(this).text($(this).data('details'));
            //   $(this).data('switch', 1);
            // } else {
            //   $(this).text($(this).data('subject'));
            //   $(this).data('switch', 0);
            // }
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('keyup', '.send-message', function(event) {
        if (event.keyCode != 13) {
            return;
        }

        let supplierId = $(this).attr('data-id');
        let message = $(this).val();
        let self = this;

        if (message == '') {
            return;
        }

        $.ajax({
            url: "{{action('WhatsAppController@sendMessage', 'supplier')}}",
            type: 'post',
            data: {
                message: message,
                supplier_id: supplierId,
                _token: "{{csrf_token()}}",
                status: 2
            },
            success: function() {
                $(self).removeAttr('disabled');
                $(self).val('');
                toastr['success']("Message sent successfully!", "Success");
            },
            beforeSend: function() {
                $(self).attr('disabled', true);
            },
            error: function() {
                $(self).removeAttr('disabled');
            }
        });

    });

      $(document).on('click', '.block-twilio', function () {
          var supplier_id = $(this).data('id');
          var thiss = $(this);

          $.ajax({
              type: "POST",
              url: "{{ route('supplier.block') }}",
              data: {
                  _token: "{{ csrf_token() }}",
                  supplier_id: supplier_id
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
  </script>
@endsection
