@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Daily Cash Flow</h2>
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
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#cashCreateModal">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
      <div class="col text-right">
        <h3>Short Fall</h3>
      </div>

      <div class="col">
        <h3>{{ $short_fall }}</h3>
      </div>
    </div>

    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Received From</th>
            <th>Paid To</th>
            <th>Date</th>
            <th>Expected</th>
            <th>Received</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($cash_flows as $cash_flow)
            <tr>
              <td>{{ $cash_flow->received_from }}</td>
              <td>{{ $cash_flow->paid_to }}</td>
              <td>{{ \Carbon\Carbon::parse($cash_flow->date)->format('d-m H:i') }}</td>
              <td>{{ $cash_flow->expected }}</td>
              <td>{{ $cash_flow->received }}</td>
              <td>
                <button type="button" class="btn btn-image edit-cashflow" data-toggle="modal" data-target="#cashEditModal" data-cashflow="{{ $cash_flow }}"><img src="/images/edit.png" /></button>

                {!! Form::open(['method' => 'DELETE','route' => ['dailycashflow.destroy', $cash_flow->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $cash_flows->appends(Request::except('page'))->links() !!}

    <div id="cashCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('dailycashflow.store') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Store a Record</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <strong>Received From:</strong>
                <input type="text" name="received_from" class="form-control" value="{{ old('received_from') }}">

                @if ($errors->has('received_from'))
                  <div class="alert alert-danger">{{$errors->first('received_from')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Paid To:</strong>
                <input type="text" name="paid_to" class="form-control" value="{{ old('paid_to') }}">

                @if ($errors->has('paid_to'))
                  <div class="alert alert-danger">{{$errors->first('paid_to')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Date:</strong>
                <div class='input-group date' id='date-datetime'>
                  <input type='text' class="form-control" name="date" value="{{ date('Y-m-d H:i') }}" required />

                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Expected Amount:</strong>
                <input type="number" name="expected" class="form-control" value="{{ old('expected') }}">

                @if ($errors->has('expected'))
                  <div class="alert alert-danger">{{$errors->first('expected')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Received Amount:</strong>
                <input type="number" name="received" class="form-control" value="{{ old('received') }}">

                @if ($errors->has('received'))
                  <div class="alert alert-danger">{{$errors->first('received')}}</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
          </form>
        </div>

      </div>
    </div>

    <div id="cashEditModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-header">
              <h4 class="modal-title">Update a Record</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <strong>Received From:</strong>
                <input type="text" name="received_from" class="form-control" value="" id="received_from">

                @if ($errors->has('received_from'))
                  <div class="alert alert-danger">{{$errors->first('received_from')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Paid To:</strong>
                <input type="text" name="paid_to" class="form-control" value="" id="paid_to">

                @if ($errors->has('paid_to'))
                  <div class="alert alert-danger">{{$errors->first('paid_to')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Date:</strong>
                <div class='input-group date' id='date-datetime'>
                  <input type='text' class="form-control" name="date" value="" id="date" required />

                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Expected Amount:</strong>
                <input type="number" name="expected" class="form-control" value="" id="expected">

                @if ($errors->has('expected'))
                  <div class="alert alert-danger">{{$errors->first('expected')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Received Amount:</strong>
                <input type="number" name="received" class="form-control" value="" id="received">

                @if ($errors->has('received'))
                  <div class="alert alert-danger">{{$errors->first('received')}}</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Update</button>
            </div>
          </form>
        </div>

      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#date-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });
    });

    $(document).on('click', '.edit-cashflow', function() {
      var cashflow = $(this).data('cashflow');
      var url = "{{ url('dailycashflow') }}/" + cashflow.id;

      $('#cashEditModal form').attr('action', url);
      $('#received_from').val(cashflow.received_from);
      $('#paid_to').val(cashflow.paid_to);
      $('#date').val(cashflow.date);
      $('#expected').val(cashflow.expected);
      $('#received').val(cashflow.received);
    });
  </script>
@endsection
