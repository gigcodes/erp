@extends('layouts.app')
@section('favicon', '')

@section('title', 'Ui Check Logs')

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
            <h2 class="page-heading">UI Check Logs</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get">
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
                             {{-- <div class="form-group m-1">
                                <select name="task_id" id="search_task" class="form-control" placeholder="Search Dev Task">
                                    <option value="">Search Dev Task</option>
                                    @foreach ($tasks as $key => $task)
                                        <option value="{{ $task->id }}"
                                            @if (request()->get('task_id') == $task->id) selected @endif>#DEVTASK-{{ $task->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->isAdmin())
                                <div class="form-group m-1">
                                    <select name="user_gmail" id="search_user" class="form-control"
                                        placeholder="Search User">
                                        <option value="">Search User</option>
                                        @foreach ($users as $key => $val)
                                            <option value="{{ $val->gmail }}"
                                                @if (request()->get('user_gmail') == $val->gmail) selected @endif>{{ $val->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif --}}
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

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
                    <th>Estimated Time</th>
                    <th>Device No</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uiDeviceLogs as $key => $uiDeviceLog)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td style="max-width: 150px">
                            <div data-message="{{ $uiDeviceLog->title }}" data-title="Title" style="cursor: pointer"
                                class="showFullMessage">
                                {{ show_short_message($uiDeviceLog->title, 25) }}
                            </div>
                        </td>
                        <td>
                            {{ $uiDeviceLog->website }}
                        </td>
						@if (Auth::user()->isAdmin())
                        <td>{{ $uiDeviceLog->name }}</td>
						@endif
                        <td>{{ $uiDeviceLog->uiDevice->estimated_time }} Mins</td>
                        <td>{{ $uiDeviceLog->uiDevice->device_no }}</td>
                        <td>{{ $uiDeviceLog->start_time }}</td>
                        <td>{{ $uiDeviceLog->end_time }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
    </script>
@endsection
