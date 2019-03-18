@extends('layouts.app')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <h2 class="page-heading">Cash Vouchers</h2>

            <div class="pull-right">
              <a class="btn btn-secondary" href="{{ route('voucher.create') }}">+</a>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
      <div class="row mb-3">
        <div class="col-sm-12">
          <form action="{{ route('voucher.index') }}" method="GET" class="form-inline align-items-start" id="searchForm">
            <div class="row full-width" style="width: 100%;">
              <div class="col-md-4 col-sm-12">
                <div class="form-group mr-3">
                  <select class="form-control select-multiple" name="user[]" multiple>
                    @foreach ($users_array as $index => $name)
                      <option value="{{ $index }}" {{ isset($user) && in_array($index, $user) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-sm-12 col-md-4">
                <div class="form-group mr-3">
                  <input type="text" value="" name="range_start" hidden/>
                  <input type="text" value="" name="range_end" hidden/>
                  <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>
                </div>
              </div>

              <div class="col-md-2"><button type="submit" class="btn btn-image"><img src="/images/search.png" /></button></div>
            </div>
          </form>
        </div>
      </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th>User Name</th>
          <th>Description</th>
          <th>Travel Type</th>
          <th>Amount</th>
          <th>Paid</th>
          <th>Credit</th>
          <th>Date</th>
          <th colspan="3" class="text-center">Action</th>
        </tr>
        @foreach ($vouchers as $voucher)
            <tr>
              <td>{{ $voucher->user->name }}</td>
              <td>{{ $voucher->description }}</td>
              <td>{{ ucwords($voucher->travel_type) }}</td>
              <td>{{ $voucher->amount }}</td>
              <td>{{ $voucher->paid }}</td>
              <td>
                {{ ($voucher->amount - $voucher->paid) * -1 }}
              </td>
              <td>{{ \Carbon\Carbon::parse($voucher->date)->format('d-m') }}</td>
              <td>
                @if ($voucher->approved > 0)
                  Approved
                @else
                  @if (Auth::id() == 49 || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                    <form class="form-inline" action="{{ route('voucher.approve', $voucher->id) }}" method="POST">
                      @csrf

                      <button type="submit" class="btn btn-xs btn-secondary">Approve</button>
                    </form>
                  @else
                    Not approved
                  @endif
                @endif
              </td>
              <td>
                @if ($voucher->approved == 2)
                  Approved
                @else
                  @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                    <form class="form-inline" action="{{ route('voucher.approve', $voucher->id) }}" method="POST">
                      @csrf

                      <button type="submit" class="btn btn-xs btn-secondary">Approve</button>
                    </form>
                  @else
                    Not approved
                  @endif
                @endif
              </td>
              <td>
                <a class="btn btn-image" href="{{ route('voucher.edit', $voucher->id) }}"><img src="/images/edit.png" /></a>

                {!! Form::open(['method' => 'DELETE','route' => ['voucher.destroy', $voucher->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
        @endforeach
      </table>
    </div>

    {!! $vouchers->appends(Request::except('page'))->links() !!}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });

    let r_s = '';
    let r_e = '{{ date('y-m-d') }}';

    let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(6, 'days');
    let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

    jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
    jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

    });
  </script>
@endsection
