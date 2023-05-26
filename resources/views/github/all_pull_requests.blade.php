@extends('layouts.app')

@section('content')
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
    .d-n{
        display: none;
    }
</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h2 class="page-heading">Pull Requests (<span id="pull_request_html_id"></span>)</h2>
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
</div>

<div class="container" style="max-width: 100%;width: 100%;">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="" class="form-label">Organization</label>
            <select name="organizationId" id="organizationId" class="form-control">
                @foreach ($githubOrganizations as $githubOrganization)
                    <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" id="repoId" class="form-control">
                
            </select>
        </div>

        <div class="col-md-6">
            <div class="text-right pl-5">
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&pull_only=1">Deploy ERP Master</a>
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&composer=true&pull_only=1">Deploy ERP Master + Composer</a>
            </div>
        </div>
    </div>

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
          
        </tbody>
    </table>
    <div class="loader-section d-n">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $('#pull-request-table').DataTable({
        "paging": false,
        "ordering": true,
        "info": false
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

    $('#organizationId').change(function (){
        getRepositories();
    });

    function getRepositories(){
        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repoId').empty();

        if(repos.length > 0){
            $.each(repos, function (k, v){
                $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
            });

            getPullRequests();
        }else{
            getPullRequests();
        }
    }

    $('#repoId').change(function (){
        getPullRequests();
    });

    function getPullRequests(){
        var repoId = $('#repoId').val();

        $('.loader-section').removeClass('d-n');

        $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
            },
            dataType: "json",
            success: function (result) {
                $('#pull-request-table').DataTable().clear().destroy();

                $('#pull_request_html_id').html(result.count);;
                $('#pull-request-table tbody').empty().html(result.tbody);

                $('#pull-request-table').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    $(document).ready(function() {
        getRepositories();
    });
</script>
@endsection