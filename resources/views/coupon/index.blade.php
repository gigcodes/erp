@extends('layouts.app')

@section('link-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('content')



<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Coupon Management</h2>
    </div>
</div>

<!-- Hidden content used to generate dynamic elements (start) -->
<div id="response-alert" style="display:none;" class="alert alert-success">
    <span>You should check in on some of those fields below.</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<table style="display:none;">
    <tr id="coupon-row">
        <td colspan="3"></td>
    </tr>
    <tr id="order-row">
        <td data-identifier="order-id">Order ID</td>
        <td data-identifier="order-date">Order Date</td>
        <td data-identifier="order-client-name">Client Name</td>
    </tr>
</table>

<!-- Hidden content used to generate dynamic elements (end) -->


<div id="response-alert-container"></div>

<div style="text-align: right;">
    <button type="button" class="btn btn-primary" onclick="createCoupon()">
        New Coupon
    </button>
</div>

<!-- COUPON DETAIL MODAL -->
<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalLabel">New Coupon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group row">
                        <label for="code" class="col-sm-3 col-form-label">Code</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="code" placeholder="Code" value="{{old('code')}}" />
                            @if ($errors->has('code'))
                            <div class="alert alert-danger">{{$errors->first('code')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label">Description</label>
                        <div class="col-sm-8">
                            <textarea type="text" class="form-control" name="description" placeholder="Description">{{old('description')}}</textarea>
                            @if ($errors->has('description'))
                            <div class="alert alert-danger">{{$errors->first('description')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start" class="col-sm-3 col-form-label">Start</label>
                        <div class="col-sm-8">
                            <div class='input-group date' id='start'>
                                <input type='text' class="form-control" name="start" value="{{old('start')}}" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            @if ($errors->has('start'))
                            <div class="alert alert-danger">{{$errors->first('start')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="expiration" class="col-sm-3 col-form-label">Expiration</label>
                        <div class="col-sm-8">
                            <div class='input-group date' id='expiration'>
                                <input type='text' class="form-control" name="expiration" value="{{old('expiration')}}" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            @if ($errors->has('expiration'))
                            <div class="alert alert-danger">{{$errors->first('expiration')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="currency" class="col-sm-3 col-form-label">Currency</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="currency" placeholder="Currency" value="{{old('currency')}}" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="discount_fixed" class="col-sm-3 col-form-label">Fixed discount</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="discount_fixed" placeholder="Fixed discount" value="{{old('discount_fixed', 0.00)}}" />
                            @if ($errors->has('discount_fixed'))
                            <div class="alert alert-danger">{{$errors->first('discount_fixed')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="discount_percentage" class="col-sm-3 col-form-label">Percentage discount</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="discount_percentage" placeholder="Percentage discount" value="{{old('discount_percentage', 0.00)}}" />
                            @if ($errors->has('discount_percentage'))
                            <div class="alert alert-danger">{{$errors->first('discount_percentage')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="minimum_order_amount" class="col-sm-3 col-form-label">Minimum order amount</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="minimum_order_amount" placeholder="Minimum order amount" value="{{old('minimum_order_amount', 0)}}" />
                            @if ($errors->has('minimum_order_amount'))
                            <div class="alert alert-danger">{{$errors->first('minimum_order_amount')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="maximum_usage" class="col-sm-3 col-form-label">Maximum usage</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="maximum_usage" placeholder="Maximum usage" value="{{old('maximum_usage')}}" />
                            @if ($errors->has('maximum_usage'))
                            <div class="alert alert-danger">{{$errors->first('maximum_usage')}}</div>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- COUPON REPORT MODAL -->
<div class="modal fade" id="couponReportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input id="report-date" />
                </div>
                <div id="report-progress" class="text-center">
                    <h4>Please wait. Generating report...</h4>
                </div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="width: 99%">
                            <tbody id="report-body">
                                <tr>
                                    <td colspan="3">Coupon : <strong>Coupon Id</strong></td>
                                </tr>
                                <tr>
                                    <td data-identifier="order-id">Order ID</td>
                                    <td data-identifier="order-date">Order Date</td>
                                    <td data-identifier="order-client-name">Client Name</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif
<div class="row">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" style="width: 99%" id="coupon_table">
            <thead>
                <tr>
                    <th width="15%">Code</th>
                    <th width="20%">Description</th>
                    <th>Expiration</th>
                    <th>Discount</th>
                    <th>Minimum Order Amount</th>
                    <th>Maximum Usage</th>
                    <th>Usage</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">
    /* beautify preserve:start */
    @if($errors->any())
    $('#couponModal').modal('show');
    @endif
    /* beautify preserve:end */
    $(document).ready(function() {
        $('#start').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('#expiration').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
        $('#coupon_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: '/checkout/coupons/load',
                type: 'GET'
            }
        });
        $('.dataTables_length').addClass('bs-select');

        $('input#report-date').daterangepicker();
    });

    function copyCoupon(
        id,
        code,
        description,
        start,
        expiration,
        currency,
        discountFixed,
        discountPercentage,
        minimumOrderAmount,
        maximumUsage
    ) {
        /* beautify preserve:start */
        $('#coupon-form').attr('action', '{{ route('coupons.store') }}')
        /* beautify preserve:end */

        $('#coupon-form input[name="code"]').val(code);
        $('#coupon-form textarea[name="description"]').val(description);
        $('#coupon-form input[name="start"]').val(start);
        $('#coupon-form input[name="expiration"]').val(expiration);
        $('#coupon-form input[name="currency"]').val(currency);
        $('#coupon-form input[name="discount_fixed"]').val(discountFixed);
        $('#coupon-form input[name="discount_percentage"]').val(discountPercentage);
        $('#coupon-form input[name="minimum_order_amount"]').val(minimumOrderAmount);
        $('#coupon-form input[name="maximum_usage"]').val(maximumUsage);

        $('#couponModal').modal('show');
    }

    function deleteCoupon(id) {
        const shouldDelete = confirm('Do you want to delete coupon?');
        if (shouldDelete) {
            $.ajax({
                    method: "DELETE",
                    url: '/checkout/coupons/' + id,
                    data: {
                        _token: $('#coupon-form input[name="_token"]').val(),
                    }
                })
                .done(function(response) {
                    const responseJson = JSON.parse(response);
                    showReponseAlert(responseJson.message);
                    $('#coupon_table').DataTable().ajax.reload();
                })
                .fail(function(response) {
                    console.log(response);
                    showReponseAlert(response.responseJSON.message);
                });
        }
    }

    function editCoupon(id,
        code,
        description,
        start,
        expiration,
        currency,
        discountFixed,
        discountPercentage,
        minimumOrderAmount,
        maximumUsage
    ) {
        $('#coupon-form').attr('action', '/checkout/coupons/' + id);

        $('#coupon-form input[name="code"]').val(code);
        $('#coupon-form textarea[name="description"]').val(description);
        $('#coupon-form input[name="start"]').val(start);
        $('#coupon-form input[name="expiration"]').val(expiration);
        $('#coupon-form input[name="currency"]').val(currency);
        $('#coupon-form input[name="discount_fixed"]').val(discountFixed);
        $('#coupon-form input[name="discount_percentage"]').val(discountPercentage);
        $('#coupon-form input[name="minimum_order_amount"]').val(minimumOrderAmount);
        $('#coupon-form input[name="maximum_usage"]').val(maximumUsage);

        $('#couponModal').modal('show');
    }

    function createCoupon() {
        /* beautify preserve:start */
        $('#coupon-form').attr('action', '{{ route('coupons.store') }}')
        /* beautify preserve:end */
        $('#coupon-form input').not('input[name="_token"]').val('');
        $('#coupon-form textarea').val('');
        $('#couponModal').modal('show');
    }

    function executeCouponOperation() {

        const formActionUrl = $('#coupon-form').attr('action');

        $.ajax({
                method: "POST",
                url: formActionUrl,
                data: {
                    _token: $('#coupon-form input[name="_token"]').val(),
                    code: $('#coupon-form input[name="code"]').val(),
                    description: $('#coupon-form textarea[name="description"]').val(),
                    start: $('#coupon-form input[name="start"]').val(),
                    expiration: $('#coupon-form input[name="expiration"]').val(),
                    currency: $('#coupon-form input[name="currency"]').val(),
                    discount_fixed: $('#coupon-form input[name="discount_fixed"]').val(),
                    discount_percentage: $('#coupon-form input[name="discount_percentage"]').val(),
                    minimum_order_amount: $('#coupon-form input[name="minimum_order_amount"]').val(),
                    maximum_usage: $('#coupon-form input[name="maximum_usage"]').val()
                }
            })
            .done(function(msg) {
                const response = JSON.parse(msg);
                showReponseAlert(response.message);
                $('#couponModal').modal('hide');
                $('#coupon_table').DataTable().ajax.reload();
            })
            .fail(function(response) {
                console.log(response);
                showReponseAlert(response.responseJSON.message);
                $('#couponModal').modal('hide');
            });

        return false;
    }

    function showReponseAlert(alert) {
        const responseAlert = $('#response-alert').clone();
        $(responseAlert).show();
        $(responseAlert).find('>span').text(alert);
        $('#response-alert-container').empty().append(responseAlert);
    }

    function showReport(id) {

        const startDate = moment().subtract(30, 'days').toDate();
        const endDate = moment().toDate();

        const startString = moment(startDate).format('YYYY-MM-DD ') + '00:00:00';
        const endString = moment(endDate).format('YYYY-MM-DD ') + '23:59:59';

        $('input#report-date').data('daterangepicker').setStartDate(startDate);
        $('input#report-date').data('daterangepicker').setEndDate(endDate);

        $('input#report-date').hide();

        $('#report-progress').show();

        $('#couponReportModal').modal('show');

        $.ajax({
                method: 'GET',
                url: '/checkout/coupons/' + id + '/report?start=' + startString + '&end=' + endString
            })
            .done(function(response) {
                console.log(response);
                const orders = JSON.parse(response);
                $('#report-progress').hide();
                $('#report-body').empty();
                addCouponRow(id);
                for(let i = 0;i<orders.length;i++){
                   addOrderRow(orders[i].order_id, orders[i].order_date, orders[i].client_name);
                }
            })
            .fail(function(error) {
                console.log(error);
            });


    }

    function addCouponRow(couponId){
        
        const orderRow = $("#coupon-row").clone();
        $(orderRow).removeAttr('id');
        $(orderRow).find('td').html('<strong>Coupon Id:<strong>'+ couponId);

        console.log(orderRow);

        $('#report-body').append(orderRow);
    }

    function addOrderRow(orderId, orderDate, clientName) {
        const orderRow = $("#order-row").clone();
        $(orderRow).removeAttr('id');
        $(orderRow).find('td[data-identifier="order-id"]').text(orderId);
        $(orderRow).find('td[data-identifier="order-date"]').text(orderDate);
        $(orderRow).find('td[data-identifier="order-client-name"]').text(clientName);

        $('#report-body').append(orderRow);
    }

    function showOverallReport() {

    }
</script>
@endsection