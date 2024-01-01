@extends('layouts.app')

@section('favicon' , 'vendor.png')

@section('title', 'Vendor Info')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style type="text/css">
    .numberSend {
        width: 160px;
        background-color: transparent;
        color: transparent;
        text-align: center;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        left: 19%;
        margin-left: -80px;
        display: none;
    }

    .input-sm {
        width: 60px;
    }

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
        z-index: 60;
    }

    .cls_filter_inputbox {
        width: 12%;
        text-align: center;
    }

    .message-chat-txt {
        color: #333 !important;
    }

    .cls_remove_leftpadding {
        padding-left: 0px !important;
    }

    .cls_remove_rightpadding {
        padding-right: 0px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 12px;
    }

    .cls_remove_allpadding {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    .cls_quick_message {
        width: 100% !important;
        height: 35px !important;
    }

    .cls_filter_box {
        width: 100%;
    }

    .select2-selection.select2-selection--single {
        height: 35px;
    }

    .cls_action_btn .btn-image img {
        width: 13px !important;
    }

    .cls_action_btn .btn {
        padding: 6px 2px;
    }

    .cls_textarea_subbox {
        width: 100%;
    }

    .btn.btn-image.delete_quick_comment {
        padding: 4px;
    }

    .vendor-update-status-icon {
        padding: 0px;
    }

    .cls_commu_his {
        width: 100% !important;
    }

    .vendor-update-status-icon {
        margin-top: -7px;
    }

    .clsphonebox .btn.btn-image {
        padding: 5px;
    }

    .clsphonebox {
        margin-top: -8px;
    }

    .send-message1 {
        padding: 0px 10px;
    }

    .load-communication-modal {
        margin-top: -6px;
        margin-left: 4px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px;
    }

    .select2-selection__arrow {
        display: none;
    }

    .cls_mesg_box {
        margin-top: -7px;
        font-size: 12px;
    }

    .td-full-container {
        color: #333;
    }

    .select-width {
        width: 80% !important;
    }

    .i-vendor-status-history {
        position: absolute;
        top: 17px;
        right: 10px;
    }
    #vendorCreateModal .select2-container, #vendorEditModal .select2-container {width: 100% !important;}
</style>
@endsection

@section('large_content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
    <div class="col-md-12 p-0">
        <h2 class="page-heading">
            Vendor Flow Chart ({{ $totalVendor }})
            <div style="float: right;">
                <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#vfcdatatablecolumnvisibilityList">Column Visiblity</button>

                <a class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newFlowChartModal">Create Flow Chart</a>
            </div>
        </h2>
    </div>

    <!-- <div class="col-12" style="padding-left:0px;">
        <form class="form-inline" action="{{ route('vendors.flow-chart') }}" method="GET">

            <div class="form-group col-md-3 pr-0">
                <strong>Select Vendor :</strong>
                {{ Form::select("filter_vendor[]", \App\Vendor::orderBy('name')->pluck('name','id')->toArray(), request('filter_vendor'), ["class" => "form-control select2", "multiple", "id" => "filter_vendor"]) }}
            </div>
            <div class="form-group col-md-1 pr-0 pt-20">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <a href="{{route('vendors.flow-chart')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div> -->
</div>

@include('partials.flash_messages')
@include("vendors.partials.column-visibility-modal-fc")
@include('vendors.partials.add-flow-chart')

<div class="infinite-scroll">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="vendor-table" style="table-layout: fixed;">
            <thead>
                <tr>
                    @if(!empty($dynamicColumnsToShowVendorsfc))
                        @if (!in_array('ID', $dynamicColumnsToShowVendorsfc))
                            <th width="10%">Vendor</th>
                        @endif
                        @if($vendor_flow_charts)
                            @foreach($vendor_flow_charts as $flow_chart)
                                @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                                    <th>{{$flow_chart->name}}</th>
                                @endif
                            @endforeach
                        @endif
                    @else
                        <th width="10%">Vendor</th>
                        @if($vendor_flow_charts)
                            @foreach($vendor_flow_charts as $flow_chart)
                                <th>{{$flow_chart->name}}</th>
                            @endforeach
                        @endif
                    @endif
                </tr>
            </thead>

            <tbody id="vendor-body">
                @include('vendors.partials.data-fc')
            </tbody>
        </table>
    </div>

    {!! $VendorFlowchart->appends(Request::except('page'))->links() !!}
</div>

<div id="vfc-remarks-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Remarks</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vfc-remarks-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{asset('js/zoom-meetings.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="{{asset('js/common-email-send.js')}}">
    //js for common mail
</script>

<script type="text/javascript">

    function saveRemarks(vendor_id, flow_chart_id){

        var remarks = $("#remark_"+vendor_id+"_"+flow_chart_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
        } else {

            $.ajax({
                url: "{{route('vendors.flowchart.saveremarks')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'vendor_id' :vendor_id,
                    'flow_chart_id' :flow_chart_id,
                    'remarks' :remarks,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#remark_"+vendor_id+"_"+flow_chart_id).val('');
                    $("#loading-image").hide();
                    toastr['success']('Remarks Added successfully!!!', 'success');
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }
    }

    $(document).on('click', '.remarks-history-show', function() {
        var vendor_id = $(this).attr('data-vendorid');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchart.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'vendor_id' :vendor_id,
                'flow_chart_id' :flow_chart_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vfc-remarks-histories-list").find(".vfc-remarks-histories-list-view").html(html);
                    $("#vfc-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $("#filter_vendor").select2();
</script>
@endsection