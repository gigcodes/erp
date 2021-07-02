@extends('layouts.app')

@section('title', 'Inventory suppliers')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<style>
.ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
}
.inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}
.pd-5 {
  padding:5px !important;
}
.pd-3 {
  padding:3px !important;
}
.status-select-cls .multiselect {
  width:100%;
}
.btn-ht {
  height:30px;
}
.status-select-cls .btn-group {
  width:100%;
  padding: 0;
}
.table.table-bordered.order-table a{
color:black!important;
}
.fa-info-circle{
    padding-left:10px;
    cursor: pointer;
}
table tr td {
  word-wrap: break-word;
}
.fa-list-ul{
    cursor: pointer;
}

.fa-upload{
    cursor: pointer;
}
.fa-refresh{
    cursor: pointer;
    color:#000;
}
</style>
@endsection

@section('large_content')
	<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>

    <div class="row">
        <div class="col-12" style="padding:0px;">
            <h2 class="page-heading">Tasks</h2>
        </div>
         <div class="col-10" style="padding-left:0px;">
            <div >
                <form class="form-inline" action="" method="GET">
                    <div class="form-group col-md-2 pd-3">
                        <input style="width:100%;" id="totem__search__form" name="q" type="text" class="form-control" value="{{ isset($_REQUEST['search']) ? $_REQUEST['search'] : '' }}" placeholder="Search...">
                    </div> 
                    <div class="form-group col-md-1 pd-3">
                        <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                        <a href="{{ route('totem.tasks.all') }}" class="fa fa-refresh" aria-hidden="true"></a>
                    </div>
                </form>
            </div>
        </div>
    </div>	



    <div class="row">
        <div class="infinite-scroll" style="width:100%;">
	        <div class="table-responsive mt-2">
                <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th width="5%">Description</th> 
                            <th width="8%">Average Runtime</th>
                            <th width="5%">Last Run</th>
                            <th width="5%">Next Run</th>
                            <th width="5%">Action</th> 
                            @foreach($tasks as $key => $task)
                            <tr class="{{$task->is_active ?: 'uk-text-danger'}}">
                                <td>{{$task->id}}</td>
                                <td>
                                    <a href="{{route('totem.task.view', $task)}}">
                                        {{str_limit($task->description, 30)}}
                                    </a>
                                    <span class="uk-float-right uk-hidden@s uk-text-muted">Command</span>
                                </td>
                                <td>
                                    {{ number_format(  $task->averageRuntime / 1000 , 2 ) }} seconds
                                    <span class="uk-float-right uk-hidden@s uk-text-muted">Avg. Runtime</span>
                                </td>
                                @if($last = $task->lastResult)
                                    <td>
                                        {{$last->ran_at->toDateTimeString()}}
                                        <span class="uk-float-right uk-hidden@s uk-text-muted">Last Run</span>
                                    </td>
                                @else
                                    <td>
                                        N/A
                                        <span class="uk-float-right uk-hidden@s uk-text-muted">Last Run</span>
                                    </td>
                                @endif
                                <td>
                                    {{$task->upcoming}}
                                    <span class="uk-float-right uk-hidden@s uk-text-muted">Next Run</span>
                                </td>
                                <td class="uk-text-center@m">
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/view.png" style="cursor: nwse-resize; width: 0px;"></a>
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/edit.png" style="cursor: nwse-resize; width: 0px;"></a>
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/delete.png" style="cursor: nwse-resize; width: 0px;"></a>
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/send.png" style="cursor: nwse-resize; width: 0px;"></a>
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/{{ $task->is_active ? 'flagged' : 'flagged-green'}}.png" style="cursor: nwse-resize; width: 0px;"></a>
                                    <a style="padding:1px;" class="btn  d-inline btn-image view-task" href="#" data-id="{{$task->id}}" id="link" title="View log"><img src="/images/history.png" style="cursor: nwse-resize; width: 0px;"></a>
                                </td>
                            </tr>
                            @endforeach
                    </thead>
                    
                    <tbody> 
                    </tbody>
                </table>
	        </div>
        </div>
    </div>

@endsection
@section('scripts')

<script type="text/javascript"> 
 $(document).on("click",".view-task",function(e) {

    var url = $(this).data('url');
    $.ajax({
        type: "GET",
        url: url, 
        dataType : "json",
        success: function (response) {
            
        },
        error: function () {
            // toastr['error']('Message not sent successfully!');
        }
    });
    });
</script>
@endsection