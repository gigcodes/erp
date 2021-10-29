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
                            <th scope="col" class="text-center">Task</th>
                            <th scope="col" class="text-center">Assigned To</th>
                            <th scope="col" class="text-center">Status</th>
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
							</tr>
                        @endforeach
                    </tbody>
            </table>
			{{$issues->links()}}
        </div>
    </div>     
@endsection
@section('scripts')
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
	
 $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });
    </script>
@endsection
