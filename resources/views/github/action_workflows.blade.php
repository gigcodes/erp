@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>

    var currentChatParams = {};
    var workingOn =  null;

    ver getActionHtml = function(response){
        let html = "<tr>";
        $.each( response, function( key, value ) {
            html .= "<td>"+value.name+"<td>";
            html .= "<td>"+moment(value.created_at).format('DD-M H:mm') +"<td>";
            html .= "<td>"+value.conclusion+"<td>";
            html .= "<td>"+value.failure_reason+"<td>";
        });
        html .= "<tr>";
        return html;
    }

    var getMoreActions = function(params) {
        var AllMessages = [];
        workingOn = $.ajax({
            type: "GET",
            url: params.url,
            data: params.data,
            beforeSend: function () {
                var loadingIcon = '<div id="loading-image" style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url(/images/pre-loader.gif)50% 50% no-repeat;"></div>';
                
                    $("#action-workflows").append(loadingIcon);
            }
        }).done(function (response) {
            workingOn = null;
            if(response.messages.length > 0) {
                AllMessages = AllMessages.concat(response.messages);
                var li = getActionHtml(response);
                
                $("#action-workflows").find("#loading-image").remove();
                $("#action-workflows").append(li);
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
            }else{
                $("#action-workflows").find("#loading-image").remove();
                currentChatParams.data.hasMore = false;
            }

        }).fail(function (response) {
            workingOn = null;
        });

    };
    $(document).ready(function() {
        $('#action-workflow-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false
        });
    });

    $('.action-table').on("scroll", function() {
        console.log("Hey");
        var $this = $(this);

        var modal_scrollTop = $this.scrollTop();
        var modal_scrollHeight = $this.find('.action-workflow-table').prop('scrollHeight');


        // Bottom reached:
        console.log([modal_scrollTop,(modal_scrollHeight - 500), workingOn , currentChatParams.data.hasMore]);
        if (modal_scrollTop > (modal_scrollHeight - 1000) && workingOn == null) {
            if(currentChatParams.data.hasMore && workingOn == null) {
                workingOn = true;
                currentChatParams.data.page++;
                getMoreActions(currentChatParams);
            }
        }

    });

</script>
<style>
    #action-workflow-table_filter {
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
        <h2 class="page-heading">Actions ({{ $githubActionRuns->total_count }})</h2>
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

<div class="container" style="max-width: 100%;width: 100%;" id="action-workflows">
    <table id="action-workflow-table" class="table table-bordered action-table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:7% !important;">Name</th>
                <th style="width:10% !important;">Executed On</th>
                <th style="width:13% !important;">Status</th>
                <th style="width:10% !important;">Failure Reason</th>
            </tr>
        </thead>
        <tbody>
           @foreach($githubActionRuns->workflow_runs as $runs)
            <tr >
                <td class="Website-task">{{$runs->name}}
                <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($runs->created_at))}}</td>
                <td class="Website-task">{{$runs->conclusion}}</td>
                <td class="Website-task">{{$runs->failure_reason}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection