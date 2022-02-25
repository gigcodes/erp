@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#pull-request-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
			 "columnDefs": [
			  { "width": "10px", "targets": 0 },
			  { "width": "40px", "targets": 1 },
			  { "width": "100px", "targets": 2 },
			  { "width": "70px", "targets": 3 },
			  { "width": "70px", "targets": 4 },
			  { "width": "70px", "targets": 5 },
			  { "width": "70px", "targets": 6 },
			  { "width": "70px", "targets": 7 }
			  ]
        });
    });


    function confirmMergeToMaster(branchName, url) {
        let result = confirm("Are you sure you want to merge " + branchName + " to master?");
        if (result) {
            window.location.href = url;
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
    <div class="text-left">
        <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&pull_only=1">Deploy ERP Master</a>
        <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&composer=true&pull_only=1">Deploy ERP Master + Composer</a>
    </div>
</div>

<div class="container">
    <table id="pull-request-table" class="table table-bordered">
        <thead>
            <tr>
                <th style="width:10%;">Repository</th>
                <th style="width:10%;">Number</th>
                <th style="width:10%;">Title</th>
                <th style="width:10%;">Branch</th>
                <th style="width:10%;">User</th>
                <th style="width:10%;">Updated At</th>
                <th style="width:10%;">Deploy</th>
                <th style="width:10%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pullRequests as $pullRequest)
            <tr>
                <td style="width:10%;">{{$pullRequest['repository']['name']}}
                <td style="width:10%;">{{$pullRequest['id']}}</td>
                <td style="width:10%;">{{$pullRequest['title']}}</td>
                <td style="width:10%;">{{$pullRequest['source']}}</td>
                <td style="width:10%;">{{$pullRequest['username']}}</td>
                <td style="width:10%;">{{date('Y-m-d H:i:s', strtotime($pullRequest['updated_at']))}}</td>
                <td style="width:10%;">
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
                        <button class="btn btn-sm btn-secondary" onclick="confirmMergeToMaster('{{$pullRequest["source"]}}','{{url('/github/repos/'.$pullRequest['repository']['id'].'/branch/merge?destination=master&source='.urlencode($pullRequest['source']).'&task_id='.urlencode($pullRequest['id']))}}')">
                            Merge into master
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection