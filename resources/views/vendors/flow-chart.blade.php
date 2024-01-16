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

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#setFlowChartSorting"> Set Flow Chart Sorting</button>

                <button type="button" class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#status-create">Add Status</button>

                <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
            </div>
        </h2>
    </div>

    <div class="col-12">
        <form class="form-inline" action="{{ route('vendors.flow-chart') }}" method="GET">

            <div class="form-group mr-3">
                <strong>Select Vendor :</strong></br>
                <input type="text" name="term" id="searchInput" value="{{ request('term') }}" class="form-control" placeholder="Enter Vendor Name">
                <input type="hidden" id="selectedId" name="selectedId" value="{{ request('selectedId') }}">
            </div>

            <div class="form-group mr-3">
                <strong>Select Vendor Category :</strong></br>
                <?php
                $category_post = request('category');
                ?>
                <select class="form-control" name="category" id="category">
                    <option value="">Category</option>
                    <?php
                    foreach ($vendor_categories as $row_cate) { ?>
                        <option value="<?php echo $row_cate->id; ?>" <?php if ($category_post == $row_cate->id) echo "selected"; ?>><?php echo $row_cate->title; ?></option>
                    <?php }
                    ?>
                </select>
            </div>

            {{-- <div class="form-group col-md-3 pr-0">
                <strong>Select Vendor :</strong>
                {{ Form::select("filter_vendor[]", \App\Vendor::orderBy('name')->pluck('name','id')->toArray(), request('filter_vendor'), ["class" => "form-control select2", "multiple", "id" => "filter_vendor"]) }}
            </div> --}}
            <div class="form-group col-md-1 pr-0 pt-20" style=" padding-top: 20px;">
                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                <a href="{{route('vendors.flow-chart')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
</div>

@include('partials.flash_messages')
@include("vendors.partials.column-visibility-modal-fc")
@include('vendors.partials.add-flow-chart')

<div class="infinite-scroll">
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped" id="vendor-table">
            <thead>
                <tr>
                    @if(!empty($dynamicColumnsToShowVendorsfc))
                        @if (!in_array('Vendor', $dynamicColumnsToShowVendorsfc))
                            <th width="10%">Vendor</th>
                        @endif
                        @if (!in_array('Categgory', $dynamicColumnsToShowVendorsfc))
                            <th width="10%">Categgory</th>
                        @endif
                        @if($vendor_flow_charts)
                            @foreach($vendor_flow_charts as $flow_chart)
                                @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                                    <th width="20%">{{$flow_chart->name}}</th>
                                @endif
                            @endforeach
                        @endif
                    @else
                        <th width="10%">Vendor</th>
                        <th width="10%">Categgory</th>
                        @if($vendor_flow_charts)
                            @foreach($vendor_flow_charts as $flow_chart)
                                <th width="20%">{{$flow_chart->name}}</th>
                            @endforeach
                        @endif
                    @endif
                </tr>
            </thead>

            <tbody>
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

<div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {!! Form::label('status_name', 'Name', ['class' => 'form-control-label']) !!}
              {!! Form::text('status_name', null, ['class'=>'form-control','required','rows'=>3]) !!}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.flowchartstatuscolor') }}" method="POST">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($status as $status_data) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status_data->status_name; ?></td>
                            <td style="text-align:center;"><?php echo $status_data->status_color; ?></td>
                            <td style="text-align:center;"><input type="color" name="color_name[<?php echo $status_data->id; ?>]" class="form-control" data-id="<?php echo $status_data->id; ?>" id="color_name_<?php echo $status_data->id; ?>" value="<?php echo $status_data->status_color; ?>" style="height:30px;padding:0px;"></td>                              
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div id="fl-status-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Value Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="fl-status-histories-list-view">
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
@include("vendors.partials.modal-flow-chart-sorting")
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

    $(document).ready(function($) {
        $("#searchInput").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('vendors.autocomplete') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function (data) {
                        var transformedData = Object.keys(data).map(function(key) {
                            return {
                                label: data[key],
                                value: data[key],
                                id: key
                            };
                        });
                        response(transformedData); // Populate autocomplete suggestions with label, value, and id
                    }
                });
            },
            minLength: 2, // Minimum characters before showing suggestions
            select: function(event, ui) {
                $('#selectedId').val(ui.item.id);
            }
        });
    })

    $(document).on("click", ".status-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
          url: "{{route('vendors.flowchartstatus.create')}}",
          type: "post",
          data: $('#status-create-form').serialize()
        }).done(function(response) {
          if (response.code = '200') {
            $('#loading-image').hide();
            $('#addPostman').modal('hide');
            toastr['success']('Status  Created successfully!!!', 'success');
            location.reload();
          } else {
            toastr['error'](response.message, 'error');
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
          toastr['error'](errObj.message, 'error');
        });
      });

    $('.status-dropdown').change(function(e) {
      e.preventDefault();
      var vendor_id = $(this).data('id');
      var flow_chart_id = $(this).data('flow_chart_id');
      var selectedStatus = $(this).val();

      // Make an AJAX request to update the status
      $.ajax({
        url: '/vendor/update-flowchartstatus',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          vendor_id: vendor_id,
          flow_chart_id: flow_chart_id,
          selectedStatus: selectedStatus
        },
        success: function(response) {
          toastr['success']('Status  Created successfully!!!', 'success');
          console.log(response);
        },
        error: function(xhr, status, error) {
          // Handle the error here
          console.error(error);
        }
      });
    });

    $(document).on('click', '.status-history-show', function() {
        var vendor_id = $(this).attr('data-id');
        var flow_chart_id = $(this).attr('data-flow_chart_id');

        $.ajax({
            url: "{{route('vendors.flowchartstatus.histories')}}",
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
                                    <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                    <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#fl-status-histories-list").find(".fl-status-histories-list-view").html(html);
                    $("#fl-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
</script>
@endsection