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

    let isApiCall = true;
    let pageNum = 1;

    async function getBranches({repoId, page})
    {
        return $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
                page:page
            },
            dataType: "json",
            success: function (response) {
                return response;
            }
        });
    }


    let $dataTable = $(document).find("#branch-section table").DataTable({
            "bPaginate": false,
            "search":false,
        });
    async function fetchBranches({repoId}) {
        $dataTable.destroy();
        $(document).find("#branch-section .loader-section").show();
        $(document).find("#branch-section table tbody tr").remove();
        $(document).find("#branch-section table tfoot").hide();
        let branches = await getBranches({
            repoId: repoId,
        });
        if(branches.data.length < 1) {
            $(document).find("#branch-section table tfoot").show();
            // return false;
        }
        let htmlContent = getBranchHtml(branches.data);
        $(document).find("#branch-section table tbody").html(htmlContent);
        $(document).find("#branchCount").html(`(${branches.data.length})`)
        $(document).find("#branch-section .loader-section").hide();
        $dataTable = (document).find("#branch-section table").DataTable({
            "bPaginate": false,
            "search":false,
        });
    }
    
    $(document).ready(function() {
        $(async function() {
            await fetchBranches({
                repoId: $(document).find("select[name=repoId]").val(),
            });
        });

        $(document).on('change', "select[name=repoId]", async function() {
            await fetchBranches({
                repoId: $(document).find("select[name=repoId]").val(),
            });
        })
    })


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
        table-layout: fixed; // 
        word-wrap: break-word; // 
    }

</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h5 class="ml-5">Branches <span id="branchCount"></span></h5>
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
            <label for="" class="form-label">Repository</label>
            <select name="repoId" class="form-control">
                @foreach ($repos as $repo)
                    <option value="{{ $repo->id }}">{{ $repo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;">
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
        <tfoot style="display: none">
            <td colspan="5" >
                <h5 class="text-center text-bold">No Data Found</h5>
            </td>
        </tfoot>
    </table>
    <div class="loader-section">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
@endsection
