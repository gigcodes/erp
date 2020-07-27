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

            <div class="pull-right">
              <a class="btn btn-secondary manual-payment-btn" href="#">Manual payment</a>
              <a class="btn btn-secondary" href="{{ route('voucher.payment.request') }}">Manual request</a>
              <!-- <a class="btn btn-secondary" href="{{ route('voucher.create') }}">+</a> -->
            </div>
        </div>
    </div>
    @include('partials.flash_messages')
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



    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
          <th width="10%">User</th>
          <th width="8%">Date</th>
          <th width="25%">Details</th>
          <th width="8%">Category</th>
          <th width="7%">Time Spent</th>
          <th width="7%">Amount</th>
          <th width="7%">Currency</th>
          <th width="8%">Amount Paid</th>
          <th width="10%">Balance</th>
          <th width="10%" colspan="2" class="text-center">Action</th>
        </tr>
          @foreach ($tasks as $task)
            <tr>
              <td>@if(isset($task->user)) {{  $task->user->name }} @endif </td>
              <td>{{ \Carbon\Carbon::parse($task->date)->format('d-m') }}</td>
              <td>{{ str_limit($task->details, $limit = 100, $end = '...') }}</td>
              <td>@if($task->task_id) Task @elseif($task->developer_task_id) Devtask @else Manual @endif </td>
              <td>{{ $task->estimate_minutes }}</td>
              <td>{{ $task->rate_estimated }}</td>
              <td>{{ $task->currency }}</td>
              <td>{{ $task->paid_amount }}</td>
              <td>{{ $task->balance }}</td>
              <td><a class="btn btn-secondary create-payment" data-id="{{$task->id}}">+</a></td>
            </tr>
          @endforeach
      </table>
      {{$tasks->links()}}
    </div>



    <div id="paymentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" id="payment-content">
                
            </div>
        </div>
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



    <div id="create-manual-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" id="create-manual-payment-content">
              
            </div>
        </div>
    </div>
    
    <div id="manualPayments" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="" method="POST" >
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Manual payment receipt</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">


                     
                        <div class="col-md-12 col-lg-12 @if($errors->has('reject_reason')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                {!! Form::label('user_id', 'User', ['class' => 'form-control-label']) !!}
                                <select class="form-control select-multiple" name="user_id" id="user-select" required>
                                  <option value="">Select User</option>
                                  @foreach($users as $key => $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                  @endforeach
                                </select>
                                    @if($errors->has('user_id'))
                                      <div class="form-control-feedback">{{$errors->first('user_id')}}</div>
                                    @endif
                            </div>


                            <div class="form-group">
                                {!! Form::label('billing_start_date', 'Billing start date', ['class' => 'form-control-label']) !!}
                                {!! Form::date('billing_start_date', null, ['class'=>'form-control  '.($errors->has('billing_start_date')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required']) !!}
                                    @if($errors->has('billing_start_date'))
                                      <div class="form-control-feedback">{{$errors->first('billing_start_date')}}</div>
                                    @endif
                            </div>

                            <div class="form-group">
                                {!! Form::label('billing_end_date', 'Billing end date', ['class' => 'form-control-label']) !!}
                                {!! Form::date('billing_end_date', null, ['class'=>'form-control  '.($errors->has('billing_end_date')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required']) !!}
                                    @if($errors->has('billing_end_date'))
                                      <div class="form-control-feedback">{{$errors->first('billing_end_date')}}</div>
                                    @endif
                            </div>



                            <div class="form-group">
                                {!! Form::label('worked_minutes', 'Time spent (In minutes)', ['class' => 'form-control-label']) !!}
                                {!! Form::number('worked_minutes', null, ['class'=>'form-control  '.($errors->has('worked_minutes')?'form-control-danger':(count($errors->all())>0?'form-control-success':''))]) !!}
                                    @if($errors->has('worked_minutes'))
                                      <div class="form-control-feedback">{{$errors->first('worked_minutes')}}</div>
                                    @endif
                            </div>

                            <div class="form-group">
                                {!! Form::label('rate_estimated', 'Amount', ['class' => 'form-control-label']) !!}
                                {!! Form::number('rate_estimated', null, ['class'=>'form-control  '.($errors->has('rate_estimated')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required']) !!}
                                    @if($errors->has('rate_estimated'))
                                      <div class="form-control-feedback">{{$errors->first('rate_estimated')}}</div>
                                    @endif
                            </div>

                            <div class="form-group">
                                {!! Form::label('remarks', 'Remarks', ['class' => 'form-control-label']) !!}
                                {!! Form::textarea('remarks', null, ['class'=>'form-control  '.($errors->has('remarks')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'rows'=>3]) !!}
                                    @if($errors->has('remarks'))
                                      <div class="form-control-feedback">{{$errors->first('remarks')}}</div>
                                    @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
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
    

    $(document).on('click', '.create-payment', function(e) {
      e.preventDefault();
      var thiss = $(this);
      var type = 'GET';
        $.ajax({
          url: '/voucher/payment/'+thiss.data('id'),
          type: type,
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
          $("#loading-image").hide();
          $('#paymentModal').modal('show');
          $('#payment-content').html(response);
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });


    $(document).on('click', '.manual-payment-btn', function(e) {
      e.preventDefault();
      var thiss = $(this);
      var type = 'GET';
        $.ajax({
          url: '/voucher/manual-payment',
          type: type,
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
          $("#loading-image").hide();
          $('#create-manual-payment').modal('show');
          $('#create-manual-payment-content').html(response);

          $('#date_of_payment').datetimepicker({
            format: 'YYYY-MM-DD'
          });
          $('.select-multiple').select2({width: '100%'});
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    

    // $(document).on('click', '.submit-manual-receipt', function(e) {
    //   e.preventDefault();
    //   var form = $(this).closest("form");
    //   var thiss = $(this);
    //   var type = 'POST';
    //     $.ajax({
    //       url: '/voucher/payment-request',
    //       type: type,
    //       dataType: 'json',
    //       data: form.serialize(),
    //       beforeSend: function() {
    //         $(thiss).text('Loading');
    //       }
    //     }).done( function(response) {
    //       // $(thiss).closest('tr').removeClass('row-highlight');
    //       // $(thiss).prev('span').text('Approved');
    //       // $(thiss).remove();
    //     }).fail(function(errObj) {
    //       alert("Could not change status");
    //     });
    // });

  </script>
@endsection
