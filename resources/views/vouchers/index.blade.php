@extends('layouts.app')
@section('favicon' , 'vendor-payments.png')
@section('title', 'Vendor payments')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <h2 class="page-heading">Vendor payments</h2>

            <!-- <div class="pull-right">
              <a class="btn btn-secondary" href="{{ route('voucher.create') }}">+</a>
            </div> -->
        </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-12">
        <form action="{{ route('voucher.index') }}" method="GET" class="form-inline align-items-start" id="searchForm">
          <div class="row full-width" style="width: 100%;">
            @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
              <div class="col-md-4 col-sm-12">
              <select class="form-control select-multiple" name="user_id" id="user-select">
                  <option value="">Select User</option>
                  @foreach($users as $key => $user)
                    <option value="{{ $user->id }}" {{($selectedUser == $user->id) ? 'selected' : ''}}>{{ $user->name }}</option>
                  @endforeach
                </select>
              </div>
            @endif

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

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th width="10%">User</th>
          <th width="10%">Date</th>
          <th width="10%">Details</th>
          <th width="10%">Category</th>
          <th width="20%">Description</th>
          <th width="10%">Time Spent</th>
          <th width="10%">Amount</th>
          <th width="10%">Amount Paid</th>
          <th width="10%">Balance</th>
          <th width="10%" colspan="2" class="text-center">Action</th>
        </tr>
          @foreach ($tasks as $task)
            <tr>
            <td>@if(isset($task->assignedUser)) {{  $task->assignedUser->name }} @endif </td>
              <td>{{ \Carbon\Carbon::parse($task->end_time)->format('d-m') }}</td>
              <td>{{ str_limit($task->subject, $limit = 150, $end = '...') }}</td>
              <td>@if(isset($task->taskType)) {{  $task->taskType->name }} @endif </td>
              <td>{{ str_limit($task->task, $limit = 150, $end = '...') }}</td>
              <td>{{ $task->estimate_minutes }}</td>
              <td>{{ $task->price }}</td>
              <td>{{ $task->amount_paid }}</td>
              <td>{{ $task->balance }}</td>
              <td></td>
          @endforeach
      </table>
      {{$tasks->links()}}
    </div>

    <div id="rejectVoucherModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="#" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- reject_reason -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('reject_reason')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {!! Form::label('reject_reason', 'Reason', ['class' => 'form-control-label']) !!}
                                {!! Form::textarea('reject_reason', null, ['class'=>'form-control  '.($errors->has('reject_reason')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required','rows'=>3]) !!}
                                    @if($errors->has('reject_reason'))
                            <div class="form-control-feedback">{{$errors->first('reject_reason')}}</div>
                                        @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Reject Voucher</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript">
    // $(document).ready(function() {
    //    $(".select-multiple").multiselect({
    //     enableFiltering: true,
    //    });
    // });

    $('.select-multiple').select2({width: '100%'});

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

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
    $('#rejectVoucherModal').on('show.bs.modal', function (event) {
        var modal = $(this)
        var button = $(event.relatedTarget)
        var voucher = button.data('voucher')
        var url = "{{ url('voucher') }}/" + voucher.id + '/reject';
        modal.find('form').attr('action', url);
    })

  </script>
@endsection
