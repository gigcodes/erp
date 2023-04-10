@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#pull-request-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false
        });
    });


    function confirmMergeToMaster(branchName, url) {
        let result = confirm("Are you sure you want to merge " + branchName + " to master?");
        if (result) {
            window.location.href = url;
        }
    }

    function confirmClosePR(repositoryId, pullRequestNumber) {
        let result = confirm("Are you sure you want to close this PR : "+ pullRequestNumber+ "?");
        if (result) {
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/github/repos/'+repositoryId+'/pull-request/'+pullRequestNumber+'/close',
                dataType: "json",
                success: function (response) {
                    if(response.status) {
                        toastr['success']('Pull request has been closed successfully!');
                        window.location.reload();
                    }else{
                        errorMessage = response.error ? response.error : 'Something went wrong please try again later!';
                        toastr['error'](errorMessage);
                    }
                },
                error: function () {
                    toastr['error']('Could not change module!');
                }
            });
        }
    }
</script>
<style>
    #pull-request-table_filter {
        text-align: right;
    }
	
	table{
  margin: 0 auto;
  width: 100%;
  clear: both;
  border-collapse: collapse;
  table-layout: fixed; // ***********add this
  word-wrap:break-word; // ***********and this
}
</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h2 class="page-heading">Pull Requests ({{sizeof($pullRequests)}})</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        @if(session()->has('message'))
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
        @endif
    </div>
    <div class="text-left pl-5">
        <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&pull_only=1">Deploy ERP Master</a>
        <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&composer=true&pull_only=1">Deploy ERP Master + Composer</a>
    </div>
</div>

<div class="container" style="max-width: 100%;width: 100%;">
    <table id="pull-request-table" class="table table-bordered" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:7% !important;">Repository</th>
                <th style="width:10% !important;">Number</th>
                <th style="width:13% !important;">Title</th>
                <th style="width:10% !important;">Branch</th>
                <th style="width:10% !important;">User</th>
                <th style="width:10% !important;">Updated At</th>
                <th style="width:13% !important;">Deploy</th>
                <th style="width:9% !important;">Actions</th>
            </tr>
        </thead>
        <tbody>
           @foreach($pullRequests as $pullRequest)
            <?php $class =  !empty($pullRequest['conflict_exist']) ? "table-danger" : ""; ?>
            <tr class="{!! $class !!}">
                <td class="Website-task">{{$pullRequest['repository']['name']}}
                <td class="Website-task">{{$pullRequest['id']}}</td>
                <td class="Website-task">{{$pullRequest['title']}}</td>
                <td class="Website-task">{{$pullRequest['source']}}</td>
                <td class="Website-task">{{$pullRequest['username']}}</td>
                <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($pullRequest['updated_at']))}}</td>
                <td >
                    <a class="btn btn-sm btn-secondary" href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}">Deploy</a>
                    @if($pullRequest['repository']['name'] == "erp")
                        <a style="margin-top: 5px;" class="btn btn-sm btn-secondary" href="{{ url('/github/repos/'.$pullRequest['repository']['id'].'/deploy?branch='.urlencode($pullRequest['source'])) }}&composer=true">Deploy + Composer</a>
                    @endif
                </td>
                <td style="width:10%;">
                    {{-- <div>
                        <a class="btn btn-sm btn-secondary" href="{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?source=master&destination='.urlencode($pullRequest['source']))}}">
                            Merge from master
                        </a>
                    </div> --}}
                    <div style="margin-top: 5px;">
                        <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmMergeToMaster('{{$pullRequest["source"]}}','{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?destination=master&source='.urlencode($pullRequest['source']).'&task_id='.urlencode($pullRequest['id']))}}')">
                            Merge into master
                        </button>
                        <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmClosePR({!! $pullRequest['repository']['id'] !!}, {!! $pullRequest['id'] !!})">
                            Close PR
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection