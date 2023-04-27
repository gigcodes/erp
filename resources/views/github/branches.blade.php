@extends('layouts.app')

@section('content')
<style>
    #action-workflow-table_filter {
        text-align: right;
    }

    table {
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // 
        word-wrap: break-word; // 
    }
    .d-n{
        display: none;
    }

</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h5 class="ml-5">Branches (<span id="branches_row_html_id"></span>)</h5>
        <h3 class="text-center">Github Branches</h3>
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

<div class="container" style="max-width: 100%;width: 100%;" id="branch-section">
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
    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;" id="branches-table">
        <thead>
            <tr>
                <th style="width:7% !important;">Name</th>
                <th style="width:10% !important;">Behind By</th>
                <th style="width:13% !important;">Ahead By</th>
                <th style="width:10% !important;">Last Commit by</th>
                <th style="width:10% !important;">Last Commit by</th>
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
    $("#branches-table").DataTable({
        "bPaginate": false,
        "search":false,
    });
    
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

            getBranches();
        }else{
            getBranches();
        }
    }

    $('#repoId').change(function (){
        getBranches();
    });

    function getBranches(){
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
            success: function (response) {
                var branchHtml = getBranchHtml(response.data);

                $('#branches-table').DataTable().clear().destroy();

                $('#branches_row_html_id').html(response.data.length);
                $('#branches-table tbody').empty().html(branchHtml);

                $("#branches-table").DataTable({
                    "bPaginate": false,
                    "search":false,
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    function getBranchHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += "<td>" + value.branch_name + "</td>";
            html += "<td>" + value.behind_by + "</td>";
            html += "<td>" + value.ahead_by + "</td>";
            html += "<td>" + value.last_commit_author_username + "</td>";
            html += "<td>" + value.last_commit_time + "</td>";
            html += "</tr>";
        });
        return html;
    }

    $(document).ready(function() {
        getRepositories();
    });
</script>
@endsection
