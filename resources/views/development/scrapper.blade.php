@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@if($title == "devtask")
    @section('title', 'Development Issue')
@else
    @section('title', 'Development Task')
@endif

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">

    </style>
@endsection

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
    .mr-t-5 {
        margin-top:5px !important;
    }
    /* START - Pupose : Set Loader image - DEVTASK-4359*/
    #myDiv{
        position: fixed;
        z-index: 99;
        text-align: center;
    }
    #myDiv img{
        position: fixed;
        top: 50%;
        left: 50%;
        right: 50%;
        bottom: 50%;
    }
    /* END - DEVTASK-4359*/
</style>
@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Scrapper Task List</h2>
                    <div class="pull-left cls_filter_box">
                        {{Form::model( [], array('method'=>'get', 'class'=>'form-inline')) }}

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Module</label>
                                <select id="module" name="module" class="form-control">
                                    <option value=""> Select Module</option>
                                    @foreach ($modules as $key => $item)
                                        <option value="{{ $key }}" {{ @$inputs['module'] == $key ? 'selected' : '' }}> {{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                         
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="name">Subject</label>
                                {{Form::text('subject', @$inputs['subject'], array('class'=>'form-control'))}}
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="name">Task</label>
                                {{Form::text('task', @$inputs['task'], array('class'=>'form-control'))}}
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Assigned To</label>
                                <select name="user_id" id="user_id" class="form-control" aria-placeholder="Select User" style="float: left">
                                    @if (isset($users->id))
                                        <option value="{{ $users->id }}" selected="selected">{{ $users->name }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Status</label>
                                {{Form::select('status', [''=>'Select','In Review'=>'In Review','In Progress'=>'In Progress'], @$inputs['status'], array('class'=>'form-control'))}}
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">&nbsp;</label>
                                <button type="submit" class="btn btn-secondary ml-4">Search</button>
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">&nbsp;</label>
                                <button type="button" class="btn btn-secondary ml-4 reset" >Reset</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<div class="row mb-3">
		<div class="mt-3 col-md-12">
		    <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Id</th>
                            <th scope="col" class="text-center">Module</th>
                            <th scope="col" class="text-center">Subject</th>
                            <th style="width:22%;">Communication </th>
                            <th scope="col" class="text-center">Task</th>
                            <th scope="col" class="text-center">Assigned To</th>
                            
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center task_queue_list">
                        @foreach($issues as $i=>$issue) 
                            <tr>
								<td>{{ $issue['id'] }}</td>
								<td>{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</td>
								<td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
									<span class="show-short-subject-{{$i}}">{{ str_limit($issue->subject, 10, '...')}}</span>
									<span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $issue->subject }}</span>
								</td>
                                <td class="expand-row">
                                    <!-- class="expand-row" -->
                                    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}" style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
                                    <input type="text" class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px"/>
                                    <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
                                    <?php echo Form::select("send_message_".$issue->id,[
                                                        "to_developer" => "Send To Developer",
                                                        "to_master" => "Send To Master Developer",
                                                        "to_team_lead" => "Send To Team Lead",
                                                        "to_tester" => "Send To Tester"
                                                    ],null,["class" => "form-control send-message-number", "style" => "width:30% !important;display: inline;"]); 
                                    ?>
                                    
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="{{env('APP_URL')}}/images/filled-sent.png"/></button>

                                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top:-0%;margin-left: -3%;" title="Load messages"><img src="{{env('APP_URL')}}/images/chat.png" alt=""></button>
                                    <br>
                                        <div class="td-full-container hidden">
                                            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
                                            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                                            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
                                        </div>
                                 </td>

								<td class="expand-row-msg" data-name="task" data-id="{{$i}}">
									<span class="show-short-task-{{$i}}">{{ str_limit($issue->task, 20, '...')}}</span>
									<span style="word-break:break-all;" class="show-full-task-{{$i}} hidden">{{ $issue->task }}</span>
								</td>
								 <td>   @if($issue->assignedUser)
											<p>{{ $issue->assignedUser->name }}</p>
										@else
											<p>Unassigned</p>
										@endif
								</td>
                                <td>
									{{ $issue->status}}
								</td>
                                <td>
                                    <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $issue->scraper_id}}" data-assigned="{{$issue->assigned_to}}"><i class="fa fa-eye"></i></button>
                         
                                </td>
							</tr>
                        @endforeach
                    </tbody>
            </table>
			{{$issues->links()}}
        </div>
    </div>     
    <div id="ErrorLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Flow Log Detail</h4>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                    
                    <th style="width:10%">Flow Action</th>
                  <th style="width:20%">Modal Type </th>
                  <th style="width:20%">Leads</th>
                  <th style="width:25%">Message</th>
                  <th style="width:15%">Website</th>
                  <th style="width:10%">Date</th>
                </thead>
                <tbody class="error-log-data">
    
                </tbody>
              </table>
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
@endsection
@section('scripts')
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>


    $(document).ready(function() {
        $("#user_id").select2({
            ajax: {
                url: '{{ route('user-search') }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: "Select User",
            allowClear: true,
            minimumInputLength: 2,
            width: '100%',
        });
    });
    $(".select2").select2({
        "tags": true
    });

    $(document).on('click', '#submit_message', function (event) {
            let self = this;
            let developer_task_id = $(this).attr('data-id');
            let message = $("#message_" + developer_task_id).val();

            // if (event.which != 13) {
            //     return;
            // }

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'developer_task')}}",
                type: 'POST',
                data: {
                    _token: "{{csrf_token()}}",
                    message: message,
                    developer_task_id: developer_task_id,
                    status: 2
                },
                success: function () {
                    $(self).removeAttr('disabled');
                    $("#message_" + developer_task_id).removeAttr('disabled');
                    $(self).val('');
                    $("#message_" + developer_task_id).val('');
                    toastr['success']('Message sent successfully!', 'Message');
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                    $("#message_" + developer_task_id).attr('disabled', true);
                }
            });
        });



    $(document).on('click', '.reset', function () {
        var url = "{{ route('development.scrap.index') }}";
        window.location.href = url;
    });
	
    $(document).on('click', '.expand-row-msg', function () {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-'+name+'-'+id;
        var mini ='.expand-row-msg .show-full-'+name+'-'+id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
    $(document).on("click", ".show_error_logs", function() {
        var id = $(this).data('id');
        var assigned = $(this).data('assigned');
        $.ajax({
            method: "GET",
            url: "{{ route('logging.flow.detail') }}" ,
            data: {
                "_token": "{{ csrf_token() }}",
                "scraper_id" : id,
                "assigned" : assigned
            },
            dataType: 'html'
            })
        .done(function(result) {
            $('#ErrorLogModal').modal('show');
            $('.error-log-data').html(result);
    });

});
    </script>
@endsection
