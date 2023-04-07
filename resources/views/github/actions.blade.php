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
            html += "<td>" + moment(value.created_at).format('YYYY-MM-DD HH:mm:ss') + "</td>";
            html += "<td>" + value.conclusion + "</td>";
            html += "<td>" + value.failure_reason + "</td>";
            html += "</tr>";
        });
        return html;
    }


    async function getActions({repoId, page, date = null})
    {
        return $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
                page:page,
                date: date          
            },
            dataType: "json",
            success: function (response) {
                return response
            }
        });
    }

   

    
    $(document).ready(function() {
        let isApiCall = true;
        let pageNum = 1;
        let totalCount = 0;
        
        async function fetchActions(data) {
            $(document).find("#action-workflows .loader-section").show();
            $(document).find("#action-workflows table tfoot").hide();
            let actions = await getActions(data);
            totalCount = actions.data.total_count;
            $(document).find('#actionCount').html(`(${totalCount})`)
            if(actions.data.workflow_runs.length < 1 && data.page == 1) {
                $(document).find("#action-workflows table tfoot").show();
                $(document).find("#action-workflows .loader-section").hide();
            } else if(actions.data.workflow_runs.length < 1 && data.page > 1) {
                $(document).find("#action-workflows .loader-section").hide();
            }
            let htmlContent = getActionHtml(actions.data.workflow_runs);
            $(document).find("#action-workflows .loader-section").hide();
            $(document).find("#action-workflows table tbody").append(htmlContent);
        }
        
        $(async function() {
            await fetchActions({
                repoId: $(document).find("select[name=repoId]").val(),
                page: 1
            });
        });

        $(document).on('change', "select[name=repoId], [name=fromDate], [name=toDate]", async function() {
            $(document).find("#action-workflows table tbody tr").remove();
            let fromDate = $(document).find("[name=fromDate]").val();
            let endDate = $(document).find("[name=toDate]").val();
            if(!endDate && fromDate) {
                alert("Plase select to date.")
                return false;
            }
            pageNum = 1;
            await fetchActions({
                repoId: $(document).find("select[name=repoId]").val(),
                page: 1,
                date:(fromDate && endDate) ? `${fromDate}..${endDate}` : null
            });
        });

        $(window).on('scroll', async function() {
        if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 5)) {
            if(isApiCall) {
                isApiCall = false;
                let fromDate = $(document).find("[name=fromDate]").val();
                let endDate = $(document).find("[name=toDate]").val();
                
                await fetchActions({
                    repoId: $(document).find("select[name=repoId]").val(),
                    page: pageNum + 1,
                    date:(fromDate && endDate) ? `${fromDate}..${endDate}` : null
                });
                if(totalCount > 0) {
                    isApiCall = true;
                }
                pageNum = pageNum + 1;
            }
        }
    });
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
        <h2 class="page-heading">Actions <span id="actionCount"></span> </h2>
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
        <h3 class="text-center">Github Actions</h3>
    </div>
</div>

<div class="container" style="max-width: 100%;width: 100%;" id="action-workflows">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" class="form-control">
                @foreach ($repos as $repo)
                    <option value="{{ $repo->id }}">{{ $repo->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="" class="form-label">From date</label>
            <input type="date" name="fromDate" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="" class="form-label">To date</label>
            <input type="date" name="toDate" class="form-control">
        </div>
    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:7% !important;">Name</th>
                <th style="width:10% !important;">Executed On</th>
                <th style="width:13% !important;">Status</th>
                <th style="width:10% !important;">Failure Reason</th>
            </tr>
        </thead>
        <tbody> 
        </tbody>
        <tfoot style="display: none">
            <td colspan="4" >
                <h5 class="text-center text-bold">No Data Found</h5>
            </td>
        </tfoot>
    </table>
    <div class="loader-section">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
@endsection
