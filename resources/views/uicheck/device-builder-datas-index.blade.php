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
        </div>
    </div>

    @include('partials.flash_messages')

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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($builderDatas as $key => $builderData)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td style="max-width: 150px">
                                    <div data-message="{{ $builderData->category }}" data-title="Category" style="cursor: pointer" class="showFullMessage">
                                        {{ show_short_message($builderData->category, 25) }}
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
    </script>
@endsection
