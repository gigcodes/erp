@extends('layouts.app')

@section('link-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Coupon Management</h2>
        <div class="pull-left">
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#couponModal">
                New Coupon
            </button>
        </div>
    </div>
</div>
<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('coupons.store') }}" method="POST">
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
<script type="text/javascript">
    /* beautify preserve:start */
    @if($errors -> any())
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
    });

    function copyCoupon() {

    }

    function deleteCoupon() {
        const shouldDelete = confirm('Do you want to delete coupon?');
        if(shouldDelete){

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

    }
</script>
@endsection