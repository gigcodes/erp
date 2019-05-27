@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Suppliers List</h2>
            <div class="pull-left">
              {{-- <form class="form-inline" action="/order/" method="GET">
                <div class="form-group">
                  <input name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>

                <div class="form-group">

                </div>
                <button hidden type="submit" class="btn btn-primary">Submit</button>
              </form> --}}
            </div>

            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Emai</a>
              <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#supplierCreateModal">+</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    @include('purchase.partials.modal-email')
    @include('suppliers.partials.modal-emailToAll')

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="15%">Name</th>
            <th width="15%">Address</th>
            <th width="10%">Social handle</th>
            {{-- <th>Agents</th> --}}
            {{-- <th width="5%">GST</th> --}}
            <th width="10%">Order</th>
            <th width="20%">Emails</th>
            <th width="15%">Communication</th>
            <th width="10%">Action</th>
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

                <br>
                <span class="text-muted">
                  {{ $supplier->phone }}
                  <br>
                  <a href="#" class="send-supplier-email" data-toggle="modal" data-target="#emailSendModal" data-id="{{ $supplier->id }}">{{ $supplier->email }}</a>
                </span>
              </td>
              <td>{{ $supplier->address }}</td>
              <td style="word-break: break-all;">{{ $supplier->social_handle }}</td>
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
              <td class="{{ $supplier->email_seen == 0 ? 'text-danger' : '' }}"  style="word-break: break-all;">
                {{ strlen(strip_tags($supplier->email_message)) > 200 ? substr(strip_tags($supplier->email_message), 0, 200) . '...' : strip_tags($supplier->email_message) }}
              </td>
              <td  style="word-break: break-all;">
                {{ $supplier->message }}

                @if ($supplier->message != '')
                  <br>
                  <button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $supplier->id }}">Load More</button>

                  <ul class="more-communication-container">

                  </ul>
                @endif
              </td>
              <td>
                <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>

                {{-- <button type="button" class="btn btn-xs create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $supplier->id }}">Add Agent</button> --}}

                <button type="button" class="btn btn-image edit-supplier" data-toggle="modal" data-target="#supplierEditModal" data-supplier="{{ json_encode($supplier) }}"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['supplier.destroy', $supplier->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $suppliers->appends(Request::except('page'))->links() !!}

    @include('suppliers.partials.supplier-modals')
    {{-- @include('suppliers.partials.agent-modals') --}}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
       $(".select-multiple").multiselect({
         buttonWidth: '100%',
         includeSelectAllOption: true
       });
    });

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
  </script>
@endsection
