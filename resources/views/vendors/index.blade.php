@extends('layouts.app')

@section('title', 'Vendor Info')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

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
                <option value="has_error" {{ isset($type) && $type == 'has_error' ? 'selected' : '' }}>Has Error</option>
              </select>
            </div> --}}

            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
          </form>
        </div>
        <div class="pull-right">
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</a>
        </div>
      </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Address</th>
            <th>Social handle</th>
            <th>Communication</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($vendors as $vendor)
            <tr>
              <td>{{ $vendor->id }}</td>
              <td>
                {{ $vendor->name }}

                {{-- @if ($supplier->is_flagged == 1)
                  <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/flagged.png" /></button>
                @else
                  <button type="button" class="btn btn-image flag-supplier" data-id="{{ $supplier->id }}"><img src="/images/unflagged.png" /></button>
                @endif --}}

                <br>
                <span class="text-muted">
                  {{ $vendor->phone }}
                  <br>
                  {{ $vendor->email }}
                  {{-- <a href="#" class="send-supplier-email" data-toggle="modal" data-target="#emailSendModal" data-id="{{ $supplier->id }}">{{ $supplier->email }}</a> --}}
                  {{-- @if ($supplier->has_error == 1)
                    <span class="text-danger">!!!</span>
                  @endif --}}
                </span>
              </td>
              <td>{{ $vendor->address }}</td>

              <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
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
              <td class="{{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;">
                  {{ $vendor->message }}

                  {{-- @if ($supplier->message != '')
                    <br>
                    <button type="button" class="btn btn-xs btn-secondary load-more-communication" data-id="{{ $supplier->id }}">Load More</button>

                    <ul class="more-communication-container">

                    </ul>
                  @endif --}}
              </td>
              <td>
                <a href="{{ route('vendor.show', $vendor->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>


                <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="/images/edit.png" /></button>
                <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $vendor->id }}"><img src="/images/remark.png" /></a>

                {!! Form::open(['method' => 'DELETE','route' => ['vendor.destroy', $vendor->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $vendors->appends(Request::except('page'))->links() !!}

    @include('partials.modals.remarks')

    @include('vendors.partials.vendor-modals')
    @include('vendors.partials.agent-modals')

@endsection

@section('scripts')
  <script type="text/javascript">
    $(document).on('click', '.edit-vendor', function() {
      var vendor = $(this).data('vendor');
      var url = "{{ url('vendor') }}/" + vendor.id;

      $('#vendorEditModal form').attr('action', url);
      $('#vendor_name').val(vendor.name);
      $('#vendor_address').val(vendor.address);
      $('#vendor_phone').val(vendor.phone);
      $('#vendor_email').val(vendor.email);
      $('#vendor_social_handle').val(vendor.social_handle);
      $('#vendor_gst').val(vendor.gst);
    });

    $(document).on('click', '.create-agent', function() {
      var id = $(this).data('id');

      $('#agent_vendor_id').val(id);
    });

    $(document).on('click', '.edit-agent-button', function() {
      var agent = $(this).data('agent');
      var url = "{{ url('agent') }}/" + agent.id;

      $('#editAgentModal form').attr('action', url);
      $('#agent_name').val(agent.name);
      $('#agent_address').val(agent.address);
      $('#agent_phone').val(agent.phone);
      $('#agent_email').val(agent.email);
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
            module_type: "vendor"
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
            module_type: 'vendor'
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
  </script>
@endsection
