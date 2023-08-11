@extends('layouts.app')
@section('favicon', '')

@section('title', 'Device builder datas')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">

            <h2 class="page-heading">Builder Datas</h2>
            @include('partials.flash_messages')
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        {{-- <form class="form-inline message-search-handler" method="get">
                            <div class="form-group m-1">
                                <?php 
									if(request('category')){   $categoriesArr = request('category'); }
									else{ $categoriesArr = ''; }
								?>
								<select name="category" id="store-categories" class="form-control select2">
									<option value="" @if($categoriesArr=='') selected @endif>-- Select a categories --</option>
									@forelse($siteDevelopmentCategories as $sdcId => $siteDevelopmentCategory)
									<option value="{{ $sdcId }}" @if($categoriesArr==$sdcId) selected @endif>{!! $siteDevelopmentCategory !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            <div class="form-group m-1">
                                <?php 
									if(request('uicheck_type')){   $uicheck_type = request('uicheck_type'); }
									else{ $uicheck_type = ''; }
								?>
								<select name="uicheck_type" id="uicheck-type" class="form-control select2">
									<option value="" @if($uicheck_type=='') selected @endif>-- Select a type --</option>
									@forelse($allUicheckTypes as $typeId => $uicheckType)
									<option value="{{ $typeId }}" @if($uicheck_type==$typeId) selected @endif>{!! $uicheckType !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            <div class="form-group m-1">
                                <?php 
									if(request('status')){   $status = request('status'); }
									else{ $status = ''; }
								?>
								<select name="status" id="status" class="form-control select2">
									<option value="" @if($status=='') selected @endif>-- Select a status --</option>
									@forelse($allStatus as $sId => $sName)
									<option value="{{ $sId }}" @if($status==$sId) selected @endif>{!! $sName !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
							<div class="form-group m-1">
                                <?php 
									if(request('user_name')){   $userNameArr = request('user_name'); }
									else{ $userNameArr = []; }
								?>
								<select name="user_name[]" id="user_name" class="form-control select2" multiple>
									<option value="" @if($userNameArr=='') selected @endif>-- Select a User --</option>
									@forelse($allUsers as $uId => $uName)
									<option value="{{ $uName->id }}" @if(in_array($uName->id, $userNameArr)) selected @endif>{!! $uName->name !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                           <div class="form-group sm-1">
							<input name="daterange" type="text" class="form-control" value="" placeholder="Select Date Range" id="term">
                            </div>
                            <div class="form-group">
                                <label for="button">&nbsp;</label>
                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image btn-search-action">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <a href="/uicheck/device-logs" class="btn btn-image" id="">
									<img src="/images/resend2.png" style="cursor: nwse-resize;">
								</a>
                            </div>
                        </form> --}}
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#buildStatusList"> List Status </button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#buildStatusCreate"> Create Status </button>      
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="max-width: 150px">Categories</th>
                            <th>Website</th>
                            @if (Auth::user()->isAdmin())
                            <th>User Name</th>
                            @endif
                            <th>Type</th>
                            <th>Device No</th>
                            <th>Title</th>
                            <th>Builder Created Date</th>
                            <th>Builder Last Updated</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($builderDatas as $key => $builderData)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td style="max-width: 150px">
                                    <div data-message="{{ $builderData->category }}" data-title="Category" style="cursor: pointer" class="showFullMessage">
                                        {{ show_short_message($builderData->category, 15) }}
                                    </div>
                                </td>
                                <td>
                                    {{ $builderData->website }}
                                </td>
                                @if (Auth::user()->isAdmin())
                                <td>{{ $builderData->name }}</td>
                                @endif
                                <td>{{ $builderData->uicheck_type_id ? $allUicheckTypes[$builderData->uicheck_type_id] : ''}}</td>
                                <td>{{ $builderData->device_no }}</td>
                                <td>{{ $builderData->title }}</td>
                                <td>{{ $builderData->builder_created_date }}</td>
                                <td>{{ $builderData->builder_last_updated }}</td>
                                <td>
                                    <input type="text" id="remarks_{{$builderData->id}}" name="remarks" class="form-control" placeholder="Remark" />
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$builderData->id}}" onclick="saveRemarks({{$builderData->id}})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-id="{{$builderData->id}}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                </td>
                                <td>
                                    <a target="_blank" href="{{ route('uicheck.get-builder-html', $builderData->id) }}">
                                        <i class="btn btn-xs fa fa-eye" title="View Builder HTML"></i>
                                    </a>
                                    <a href="{{ route('uicheck.get-builder-download-html', $builderData->id) }}">
                                        <i class="btn btn-xs fa fa-download" title="Download Builder HTML"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $builderDatas->appends(request()->except('page'))->links() !!}
        </div>
    </div>

    <div id="showFullMessageModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <div id="modalGetDevMessageHistory" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ui Device Message History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="8%">Update By</th>
                                    <th width="25%" style="word-break: break-all;">Message</th>
                                    <th width="10%" style="word-break: break-all;">Expected start time</th>
                                    <th width="10%" style="word-break: break-all;">Expected completion time</th>
                                    <th width="10%" style="word-break: break-all;">Estimated Time</th>
                                    <th width="15%" style="word-break: break-all;">Status</th>
                                    <th width="15%">Created at</th>
                                </tr>
                            </thead>
                            <tbody>
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

    <div id="buildStatusCreate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                
                <form id="build_status_create_form" class="form mb-15" >
                <div class="modal-header">
                    <h4 class="modal-title">Create Build Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Name :</strong>
                        {!! Form::text('name', null, ['placeholder' => 'Status Name', 'id' => 'name', 'class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Color :</strong>
                        <input type="color" name="color" class="form-control"  id="color" value="" style="height:30px;padding:0px;">
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

    <div id="buildStatusList" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('uicheck.device-builder.status.color.update') }}" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group col-md-12">
                        <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                            <tr>
                                <td class="text-center"><b>Status Name</b></td>
                                <td class="text-center"><b>Color Code</b></td>
                                <td class="text-center"><b>Color</b></td>
                            </tr>
                            <?php
                            foreach ($getbuildStatuses as $status) { ?>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php echo $status->name; ?></td>
                                <td class="text-center"><?php echo $status->color; ?></td>
                                <td class="text-center"><input type="color" name="color_name[<?php echo $status->id; ?>]" class="form-control" data-id="<?php echo $status->id; ?>" id="color_name_<?php echo $status->id; ?>" value="<?php echo $status->color; ?>" style="height:30px;padding:0px;"></td>
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

    <div id="remark-area-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remark</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="remark-action-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
		$('.select2').select2();
        $("#search_user").select2();
        $('#search_task').select2({
            minimumInputLength: 3 // only start searching when the user has input 3 or more characters
        });

        $(document).on("click", ".showFullMessage", function() {
            let title = $(this).data('title');
            let message = $(this).data('message');

            $("#showFullMessageModel .modal-body").html(message);
            $("#showFullMessageModel .modal-title").html(title);
            $("#showFullMessageModel").modal("show");
        });

		$(function() {
			$('input[name="daterange"]').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});
		});

        function funGetDevHistory(id, uicheckId) {
            //siteLoader(true);
            let mdl = jQuery('#modalGetDevMessageHistory');
            var uicheckId = uicheckId;
            
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "/uicheck/get/message/history/dev",
                type: 'POST',
                data: {
                    device_no: id,
                    uicheck_id : uicheckId,
                },
                beforeSend: function() {
                    //jQuery("#loading-image").show();
                }
            }).done(function(response) {
                $("#modalCreateLanguage").modal("hide");
                mdl.find('tbody').html(response.html);
                mdl.modal("show");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {      
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        }

        $(document).on('submit', '#build_status_create_form', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("build_status_create_form"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("uicheck.device-builder.status.store") }}',
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    button.html(spinner_html);
                    button.prop('disabled', true);
                    button.addClass('disabled');
                },
                success: function(response) {
                    $('#buildStatusCreate #build_status_create_form').trigger('reset');
                    toastr["success"](response.message);
                    location.reload();
                },
                error: function(xhr, status, error) { 
                    toastr["error"](response.message);
                },
            });
        });

        // Store Remarks
        function saveRemarks(row_id) {
            var remark = $("#remarks_" + row_id).val();
            $.ajax({
                url: `{{ route('uicheck.store.builder-data-remark') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remarks: remark,
                    ui_device_builder_io_data_id: row_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#remarks_" + row_id).val('');
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else if(jqXHR.responseJSON.message !== undefined) {
                    toastr["error"](jqXHR.responseJSON.message);
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        // Load Remark
        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('uicheck.get.builder-data-remark', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.remarks } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#remark-area-list").find(".remark-action-list-view").html(html);
                        $("#remark-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
    </script>
@endsection
