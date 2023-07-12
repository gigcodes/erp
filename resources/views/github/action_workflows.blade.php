@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    var currentChatParams = {};
    currentChatParams.data = {
        page: 1
        , hasMore: true
    , };
    var workingOn = null;

    function getActionHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += "<td>" + value.name + "</td>";
            html += "<td>" + value.actor.login + "</td>";
            html += "<td>" + moment(value.created_at).format('YYYY-MM-DD HH:mm:ss') + "</td>";
            html += "<td>" + value.event "</td>";
            html += "<td>" + value.run_number "</td>";
            html += "<td>" + value.run_attempt "</td>";
            html += "<td>" + value.run_started_at "</td>";
            html += "<td>" + value.status "</td>";
            html += "<td>" + value.conclusion + "</td>";
            html += "<td>" + value.failure_reason + "</td>";
            html += "</tr>";
        });
        return html;
    }

    let isApiCall = true;
    let pageNum = 1;
    function getMoreActions(params) {
        var AllMessages = [];
        if(isApiCall) {
            workingOn = $.ajax({
                type: "GET", 
                url: '/github/repos/{!! $repositoryId !!}/github-actions', 
                data: {
                    'page': pageNum + 1
                }, 
                beforeSend: function() {
                    isApiCall = false;
                    // let loaderImg = `{{url('images/pre-loader.gif')}}`;
                    // let loadingIcon = `<div id="loading-image" style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url(${loaderImg}) 50% 50% no-repeat;"></div>`;
                    // $(document).find("#action-workflows").append(loadingIcon);
                    $(document).find('#action-workflows .loader-section').show();
                    pageNum = pageNum + 1;
                }
            }).done(function(response) {
                workingOn = null;
                if (response.workflow_runs.length > 0) {
    
                    var li = getActionHtml(response.workflow_runs);
                    $("#action-workflows table tbody").append(li);
                    $(document).find('#action-workflows .loader-section').hide();
                    isApiCall = true;
    
                    // $("#action-workflows").find("#loading-image").remove();
                    // var searchterm = $('.search_chat_pop').val();
                    // if(searchterm && searchterm != '') {
                    //     var value = searchterm.toLowerCase();
                    //     $(".filter-message").each(function () {
                    //         if ($(this).text().search(new RegExp(value, "i")) < 0) {
                    //             $(this).hide();
                    //         } else {
                    //             $(this).show()
                    //         }
                    //     });
                    // }
                } else {
                    $("#action-workflows").find("#loading-image").remove();
                    currentChatParams.data.hasMore = false;
                }
    
    
            }).fail(function(response) {
                workingOn = null;
            });
        }
    };
    $(document).ready(function() {
        $('#action-workflow-table').DataTable({
            "paging": false, 
            "ordering": true, 
            "info": false,
            "searching": true
        });
        $(document).find('#action-workflows .loader-section').hide();
    });

    $(window).on('scroll', function() {
        if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 5)) {
            getMoreActions(currentChatParams);
        }
    })

    // $(window).scroll(function() {
    //     console.log(getMoreActions(currentChatParams));
    //     //  if($(window).scrollTop() == $(document).height() - $(window).height()) {
    //     // console.log(currentChatParams.data);
    //     // console.log(currentChatParams.data.hasMore);

    //     //     if(currentChatParams.data && currentChatParams.data.hasMore && workingOn == null) {
    //     //         workingOn = true;
    //     //         // currentChatParams.data.page++;
    //     //         getMoreActions(currentChatParams);
    //     //     }
    //     }

    // });

</script>
<style>
    #action-workflow-table_filter {
        text-align: right;
    }

    table {
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // ***********add this
        word-wrap: break-word; // ***********and this
    }

</style>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-heading">Github Actions ({{ $githubActionRuns->total_count }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ url('/github/repos/'.$repositoryId.'/actions') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <?php 
                                    if(request('status')){   $status = request('status'); }
                                    else{ $status = ''; }
                                ?>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="" @if($status=='') selected @endif>-- Select a status --</option>
                                    <option value="completed" @if($status=="completed") selected @endif>completed</option>
                                    <option value="action_required" @if($status=="action_required") selected @endif>action_required</option>
                                    <option value="cancelled" @if($status=="cancelled") selected @endif>cancelled</option>
                                    <option value="failure" @if($status=="failure") selected @endif>failure</option>
                                    <option value="neutral" @if($status=="neutral") selected @endif>neutral</option>
                                    <option value="skipped" @if($status=="skipped") selected @endif>skipped</option>
                                    <option value="stale" @if($status=="stale") selected @endif>stale</option>
                                    <option value="success" @if($status=="success") selected @endif>success</option>
                                    <option value="timed_out" @if($status=="timed_out") selected @endif>timed_out</option>
                                    <option value="in_progress" @if($status=="in_progress") selected @endif>in_progress</option>
                                    <option value="queued" @if($status=="queued") selected @endif>queued</option>
                                    <option value="requested" @if($status=="requested") selected @endif>requested</option>
                                    <option value="waiting" @if($status=="waiting") selected @endif>waiting</option>
                                    <option value="pending" @if($status=="pending") selected @endif>pending</option>
                                </select>
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ url('/github/repos/'.$repositoryId.'/actions') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session()->has('message'))
<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        @php $type = Session::get('alert-type', 'info'); @endphp
        @if($type == "info")
        <div class="alert alert-secondary">
            {{ session()->get('message') }}
        </div>
        @elseif($type == "warning")
        <div class="alert alert-warning">
            {{ session()->get('message') }}
        </div>
        @elseif($type == "success")
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
        @elseif($type == "error")
        <div class="alert alert-error">
            {{ session()->get('message') }}
        </div>
        @endif
    </div>
</div>
@endif

<div class="container" style="max-width: 100%;width: 100%;" id="action-workflows">
    <table id="action-workflow-table" class="table table-bordered action-table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:10% !important;">Name</th>
                <th style="width:10% !important;">Actor</th>
                <th style="width:10% !important;">Executed On</th>
                <th style="width:10% !important;">Event</th>
                <th style="width:10% !important;">Run Number</th>
                <th style="width:10% !important;">Run Attempt</th>
                <th style="width:10% !important;">Run Started At</th>
                <th style="width:10% !important;">Status</th>
                <th style="width:10% !important;">Conclusion</th>
                <th style="width:10% !important;">Failure Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($githubActionRuns->workflow_runs as $runs)
            <tr>
                <td class="Website-task">{{$runs->name}}</td>
                <td class="Website-task">{{$runs->actor->login}}</td>
                <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($runs->created_at))}}</td>
                <td class="Website-task">{{$runs->event}}</td>
                <td class="Website-task">{{$runs->run_number}}</td>
                <td class="Website-task">{{$runs->run_attempt}}</td>
                <td class="Website-task">{{$runs->run_started_at}}</td>
                <td class="Website-task">{{$runs->status}}</td>
                <td class="Website-task">{{$runs->conclusion}}</td>
                <td class="Website-task">{{$runs->failure_reason}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="loader-section">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
@endsection
