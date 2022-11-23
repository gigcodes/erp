@extends('layouts.app')


@section('title', 'Bug Tracking List')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Bug Tracking List</h2>
            <div class="pull-left">
                <button class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newEnvironment"> Environment </button>&nbsp;&nbsp;
                <button class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newType"> Type </button>&nbsp;&nbsp;
                <button class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newStatus"> Status </button>&nbsp;&nbsp;
                <button class="btn btn-secondary btn-xs" style="color:white;" data-toggle="modal" data-target="#newSeverity"> Severity </button>


            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('bug-tracking.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered" style="margin-top: 25px">
            <tr>
                <th>ID</th>
                <th>Type of Bug</th>
                <th>Summary</th>
                <th>Steps to reproduce</th>
                <th>Environment</th>
                <th> Screenshot/Video url</th>
                <th>Assign to</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Module/Feature</th>
                <th>Remarks </th>
                <th width="200px">Action</th>
            </tr>
                @foreach($bugTrackings as $key => $bugTracking)

                 <tr>
                <td>{{$bugTracking->id}}</td>
                <td>{{App\BugType::where('id',$bugTracking->bug_type_id)->value('name')}}</td>
                <td>{{$bugTracking->summary}}</td>
                <td>{{$bugTracking->step_to_reproduce}}</td>
                <td>{{App\BugEnvironment::where('id',$bugTracking->bug_environment_id)->value('name')}}</td>
                <td>{{$bugTracking->url}}</td>
                <td>{{App\User::where('id',$bugTracking->assign_to)->value('name')}}</td>
                <td>{{App\BugSeverity::where('id',$bugTracking->bug_severity_id)->value('name')}}</td>
                <td>{{App\BugStatus::where('id',$bugTracking->bug_status_id)->value('name')}}</td>
                <td>{{$bugTracking->module_id}}</td>
                <td>{{$bugTracking->remark}} </td>
                     @php
                        $bugTrackingHistories = App\BugTrackerHistory::where('bug_id',$bugTracking->id)->get();
                        $bugTrackingHistories = $bugTrackingHistories->map(function ($bug){
                           $bug->bug_type_id= App\BugType::where('id',$bug->bug_type_id)->value('name');
                           $bug->bug_environment_id= App\BugEnvironment::where('id',$bug->bug_environment_id)->value('name');
                           $bug->assign_to= App\User::where('id',$bug->assign_to)->value('name');
                           $bug->bug_severity_id= App\BugSeverity::where('id',$bug->bug_severity_id)->value('name');
                           $bug->bug_status_id= App\BugStatus::where('id',$bug->bug_status_id)->value('name');
                           return $bug;
                        })
                     @endphp
                <td width="200px">
                    <a class="btn btn-image edit-postman-btn" href="{{ route('bug-tracking.edit',$bugTracking->id) }}"><img data-id="{{ $bugTracking->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                    <a class="btn delete-bug-btn" data-id="{{ $bugTracking->id }}" href="#"><img data-id="{{ $bugTracking->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                    <button type="button" class="btn btn-xs btn-image load-bug-history-modal"  data-value='{{$bugTrackingHistories}}' data-id="{{ $bugTracking->id }}" style="margin-top: 2%;" title="Load messages">Info </button>

                </td>
            </tr>
            @endforeach

        </table>
    </div>

    {!! $bugTrackings->links() !!}

    @include('bug-tracking.bug-environment')
    @include('bug-tracking.bug-type')
    @include('bug-tracking.bug-status')
    @include('bug-tracking.bug-severity')

    <div id="newHistoryModal" class="modal fade " role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Bug Tracker History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <table class="table">
                    <tr>

                        <th>Type of Bug</th>
                        <th>Summary</th>
                        <th>Environment</th>
                        <th>Status</th>
                        <th>Severity</th>
                        <th>Module/Feature</th>
                        <th>Remarks </th>
                    </tr>
                    <tbody class="tbh">

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script>
        $(document).on("click", ".load-bug-history-modal", function(e) {
            $('#newHistoryModal').modal('show');
            var data = $(this).data('value');
            $('.tbh').html("")
            if(data.length >0){

                var html ="";

                $.each(data, function (i,item){
                    console.log(item)
                    html+="<tr>"
                    html+=" <th>"+ item.bug_type_id +"</th>"
                    html+=" <th>"+ item.summary +"</th>"
                    html+=" <th>"+ item.bug_environment_id +"</th>"
                    html+=" <th>"+ item.bug_status_id +"</th>"
                    html+=" <th>"+ item.bug_severity_id +"</th>"
                    html+=" <th>"+ item.module_id +"</th>"
                    html+=" <th>"+ item.remark +"</th>"
                    html+="</tr>"
                })

                $('.tbh').html(html)
            }

        });
            $(document).on("click", ".delete-bug-btn", function(e) {
                e.preventDefault();
                if (confirm("Are you sure?")) {
                    var $this = $(this);
                    var id = $this.data('id');
                    $.ajax({
                        url: "/bug-tracking/delete",
                        type: "delete",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id
                        }
                    }).done(function(response) {
                        if (response.code = '200') {
                            toastr['success']('Bug deleted successfully!!!', 'success');
                            location.reload();
                        } else {
                            toastr['error'](response.message, 'error');
                        }
                    }).fail(function(errObj) {
                        // $('#loading-image').hide();
                        // $("#addPostman").hide();
                        toastr['error'](errObj.message, 'error');
                    });
                }
            });


    </script>
@endsection
