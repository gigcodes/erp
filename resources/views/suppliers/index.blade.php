@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Suppliers List</h2>
            <div class="pull-left">
              {{-- <form action="/order/" method="GET">
                  <div class="form-group">
                      <div class="row">
                          <div class="col-md-12">
                              <input name="term" type="text" class="form-control"
                                     value="{{ isset($term) ? $term : '' }}"
                                     placeholder="Search">
                          </div>
                          <div class="col-md-4">
                              <button hidden type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </div>
                  </div>
              </form> --}}
            </div>
            <div class="pull-right">
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#supplierCreateModal">+</a>
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
            <th>Agents</th>
            <th>GST</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($suppliers as $supplier)
            <tr>
              <td>{{ $supplier->id }}</td>
              <td>
                {{ $supplier->supplier }}
                <br>
                <span class="text-muted">
                  {{ $supplier->phone }}
                  <br>
                  {{ $supplier->email }}
                </span>
              </td>
              <td>{{ $supplier->address }}</td>
              <td>{{ $supplier->social_handle }}</td>
              <td>
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
              </td>
              <td>{{ $supplier->gst }}</td>
              <td>
                <button type="button" class="btn btn-xs create-agent" data-toggle="modal" data-target="#createAgentModal" data-id="{{ $supplier->id }}">Add Agent</button>
                <button type="button" class="btn btn-image edit-supplier" data-toggle="modal" data-target="#supplierEditModal" data-supplier="{{ $supplier }}"><img src="/images/edit.png" /></button>

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
    @include('suppliers.partials.agent-modals')

@endsection

@section('scripts')
  <script type="text/javascript">
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

    $(document).on('click', '.create-agent', function() {
      var id = $(this).data('id');

      $('#agent_supplier_id').val(id);
    });

    $(document).on('click', '.edit-agent-button', function() {
      var agent = $(this).data('agent');
      var url = "{{ url('agent') }}/" + agent.id;
      $('#agent_whatsapp_number option[value=""]').prop('selected', 'selected');

      $('#editAgentModal form').attr('action', url);
      $('#agent_name').val(agent.name);
      $('#agent_address').val(agent.address);
      $('#agent_phone').val(agent.phone);
      $('#agent_whatsapp_number option[value="' + agent.whatsapp_number + '"]').prop('selected', 'selected');
      $('#agent_email').val(agent.email);
    });
  </script>
@endsection
