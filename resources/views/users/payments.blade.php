@extends('layouts.app')

<!-- Stylesheets -->
@section('link-css')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.rawgit.com/pingcheng/bootstrap4-datetimepicker/master/build/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection


@section('content')

<style>
    #payment-table_filter {
        text-align: right;
    }

    .activity-container {
        margin-top: 3px;
    }

    .elastic {
        transition: height 0.5s;
    }

    .activity-table-wrapper {
        position: absolute;
        width: calc(100% - 50px);
        max-height: 500px;
        overflow-y: auto;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Payments</h2>
    </div>
</div>
<div style="position:relative;">
    <div id="weekpicker1" style="display: inline-block; padding-left: 8px; padding-right: 8px;"></div>
</div>
<div class="container">

    <table id="payment-table" class="table table-bordered" style="visibility: hidden;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Hours Worked</th>
                <th>Rate</th>
                <th>Currency</th>
                <th>Total</th>
                <th>Balance</th>
                <th>Pay</th>

            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="position: relative">
                <td onclick="toggle('{{$user->id}}')" style="cursor: pointer;">
                    <div style="margin-bottom: 20px;">
                        <span href="#{{$user->id}}-expandable">{{$user->name}}</span>
                    </div>
                    <div id="elastic-{{$user->id}}" class="activity-container" style="height: 500px;" data-expanded="false">
                        <div class="activity-table-wrapper">
                            <table class="table table-bordered" style="background-color: #eee;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Tracked Time</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->trackedActivitiesForWeek as $activity)
                                    <tr>
                                        <td>{{ $activity->starts_at }}</td>
                                        <td>{{ $activity->tracked }}</td>
                                        <td>{{ $activity->rate }}</td>
                                        <td>{{ $activity->earnings }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
                <td>{{$user->secondsTracked / 3600 }}</td>
                <td>{{isset($user->currentRate) ? $user->currentRate->hourly_rate : '-'}}</td>
                <td>{{$user->currency}}</td>
                <td>{{$user->total}}</td>
                <td>0</td>
                <td>
                    <button class="btn btn-secondary" onclick="makePayment({{$user->id}})">Pay</button>
                </td>

            </tr>
            @endforeach
        </tbody>

    </table>
</div>

<!-- Modal -->
<div id="paymentModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="margin-top: 150px">

        <!-- Modal content-->
        <div class="modal-content">
            {{Form::open(array('url' => '/hubstaff/makePayment', 'method' => 'POST'))}}
            {{ Form::hidden('user_id', Input::old('user_id')) }}
            <div class="modal-body">
                <div class="form-group">
                    {{ Form::label('amount', 'Amount') }}
                    {{ Form::number('amount',null, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('currency', 'Currency') }}
                    {{ Form::text('currency',null, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('note', 'Note') }}
                    {{ Form::text('note',null, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('payment_method', 'Payment Method') }}
                    {{ Form::select('payment_method', $paymentMethods, null , array('class' => 'form-control'))  }}
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Pay', array('class' => 'btn btn-primary')) }}
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            {{ Form::close() }}
        </div>

    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

@endsection

@section('scripts')

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/pingcheng/bootstrap4-datetimepicker/master/build/js/bootstrap-datetimepicker.min.js"></script>

<!-- bootstrap-weekpicker JavaScript -->
<script type="text/javascript" src="/js/bootstrap-weekpicker.js"></script>


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    function makePayment(userId) {
        $('input[name="user_id"]').val(userId);
        $("#paymentModal").modal();
    }

    $(document).ready(function() {

        adjustHeight();

        $('#payment-table').DataTable({
            "ordering": true,
            "info": false
        });

    });

    function adjustHeight() {
        $('.activity-container').each(function(index, element) {
            const childElement = $($(element).children()[0]);
            $(element).attr('data-expanded-height', childElement.height());
            $(element).height(0);
            childElement.height(0);

            setTimeout(
                function() {
                    $(element).addClass('elastic');
                    childElement.addClass('elastic');
                    $('#payment-table').css('visibility', 'visible');
                },
                1
            )
        })
    }

    function toggle(id) {
        const expandableElement = $('#elastic-' + id);

        const isExpanded = expandableElement.attr('data-expanded') === 'true';


        if (isExpanded) {
            console.log('true1');
            expandableElement.height(0);
            $($(expandableElement).children()[0]).height(0);
            expandableElement.attr('data-expanded', 'false');
        } else {
            console.log('false1');
            const expandedHeight = expandableElement.attr('data-expanded-height');
            expandableElement.height(expandedHeight);
            $($(expandableElement).children()[0]).height(expandedHeight);
            expandableElement.attr('data-expanded', 'true');
        }



    }

    $(function() {


        /* beautify preserve:start */
        var selectedYear = {{ isset($selectedYear) ? $selectedYear : 'false' }};
        var selectedWeek = {{ isset($selectedWeek) ? $selectedWeek : 'false' }};
        /* beautify preserve:end */
        var weekpicker;
        if (selectedYear !== false && selectedWeek !== false) {
            weekpicker = $("#weekpicker1").weekpicker(selectedWeek, selectedYear);
        } else {
            weekpicker = $("#weekpicker1").weekpicker();
        }


        weekpicker.find("input").on("dp.change", function() {
            console.log(weekpicker.getWeek());
            console.log(weekpicker.getYear());
            window.location = '/hubstaff/payments?week=' + weekpicker.getWeek() + '&year=' + weekpicker.getYear();
        });
    });
</script>
@endsection