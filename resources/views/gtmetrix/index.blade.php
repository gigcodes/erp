@extends('layouts.app')

@section('title','GT Metrix')

@section('content')
<style>
    .model-width{
        max-width: 1250px !important;
    }
</style>
<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">GTMetrix</h2>
    </div>
</div>


@include('partials.flash_messages')
<div class = "row">
    <div class="col-md-10 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline" action="" method="GET">
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="status" class="form-control">
                        <option value="">select status</option>
                        <option {{ (request('status') == 'not_queued') ? 'selected' : ''  }} value="not_queued">Not Queued</option>
                        <option {{ (request('status') == 'queued') ? 'selected' : ''  }} value="queued">Queued</option>
                        <option {{ (request('status') == 'started') ? 'selected' : ''  }} value="started">Started</option>
                        <option {{ (request('status') == 'completed') ? 'selected' : ''  }} value="completed">Completed</option>
                        <option {{ (request('status') == 'error') ? 'selected' : ''  }} value="error">Error</option>
                    </select>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" placeholder="keyword">
                </div>
                {{-- <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="tags" class="form-control" value="{{request()->get('tags')}}" placeholder="Hashtags">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="loc" class="form-control" value="{{request()->get('loc')}}" placeholder="Location">
                </div> --}}
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </form> 
        </div>
    </div>  
    <div class="col-md-2 margin-tb">
        <div class="pull-right mt-3">
            <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setSchedule" title="" data-id="1">Set cron time
                @if ( $cronTime && !empty( $cronTime->val ))
                    ( <small> {{$cronTime->val}} </small> )
                @endif
            </button>
            @if ( $cronStatus && $cronStatus->val == 'start' )
                <a href ="{{ route('gt-metrix.status','stop') }}" onclick="return confirm('Are you sure?')" class  = "btn btn-secondary"> Stop </a>
            @else
                <a href ="{{ route('gt-metrix.status','start') }}" onclick="return confirm('Are you sure?')" class = "btn btn-secondary"> Start </a>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        {{ $list->links() }}
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <table class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th>Website</th>
                            <th>Test id</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th>Report URL</th>
                            <th>Html load time</th>
                            <th>Html bytes</th>
                            <th>Page load time</th>
                            <th>Page bytes</th>
                            <th>Page elements</th>
                            <th>Pagespeed score</th>
                            <th>Yslow score</th>
                            <th style="width: 12%;">Resources</th>
                            <th style="width: 7.5%;">Date</th>
                            <th>PDF</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key)
                            <tr>
                                <td><a href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a></td>
                                <td>{{ $key->test_id }}</td>
                                <td>{{ $key->status }}</td>
                                <td>{{ $key->error }}</td>
                                <td><a href="{{$key->report_url}}" target="_blank" title="Show report"> Report </a></td>
                                <td>{{ $key->html_load_time }}</td>
                                <td>{{ $key->html_bytes }}</td>
                                <td>{{ $key->page_load_time }}</td>
                                <td>{{ $key->page_bytes }}</td>
                                <td>{{ $key->page_elements }}</td>
                                <td>{{ $key->pagespeed_score }}</td>
                                <td>{{ $key->yslow_score }}</td>
                                <td>
                                    @if ( $key->resources && $key->resources )
                                        <ul style="display: inline-block;">
                                            @foreach ($key->resources as $item => $value)
                                                    <li> <a href="{{ $value }}" target="_blank" rel="noopener noreferrer"> {{ $item }} </a> </li>
                                            @endforeach
                                        </ul>
                                    @else
                                     --
                                    @endif
                                    
                                <td>{{ $key->created_at }}</td>
                                <td><a target="__blank" href="{{url('/')}}{{ $key->pdf_file }}"> {{ !empty($key->pdf_file) ? 'Open' : 'N/A' }} </a></td>
                                <td>  
                                    <button class="btn btn-secondary show-history btn-xs" title="Show old history" data-id="{{ $key->store_view_id }}">
                                        <i class="fa fa-history"></i>
                                    </button>
                                    <button class="btn btn-secondary run-test btn-xs" title="Run Test" data-id="{{ $key->id }}">
                                        <i class="fa fa-play"></i>
                                    </button>
                                    @if ($key->status == "completed")
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Pagespeed Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>"pagespeed",'id'=>$key->test_id])}}" data-type="pagespeed">
                                            <i class="fa fa-tachometer"></i>
                                        </button>
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Yslow Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>"yslow",'id'=>$key->test_id])}}">
                                            <i class="fa fa-weight"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $list->links() }}
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
   50% 50% no-repeat;display:none;">
</div>
<div class="modal fade" id="gtmetrix-stats-modal" role="dialog">
    <div class="modal-dialog modal-md model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>
@include('gtmetrix.history')
@include('gtmetrix.setSchedule')
@endsection
    
@section('scripts')

<script>
    $(document).on('click', '.show-pagespeed', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        $('.message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
           // console.log(data);  
            $('.message-modal').html('');    
            $('.message-modal').html(data); // load response 
            $("#gtmetrix-stats-modal").modal("show");
            $('#loading-image').hide();        // hide ajax loader   
        })
        .fail(function(){
            $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
            $('#loading-image').hide();
        });
    });
    $(document).on('click','.show-history', function () {
        var btn = $(this);
        var id = $(this).data("id");
		$.ajax({
			url: "{{ route('gtmetrix.hitstory') }}",
			type: 'POST',
			data : { _token: "{{ csrf_token() }}", id : id },
			dataType: 'json',
			beforeSend: function () {
				btn.prop('disabled',true);
			},
			success: function(result){
				if(result.code == 200) {
					var t = '';
					$.each(result.data,function(k,v) {
                        var re = '';
                        $.each(v.resources, function (indexInArray, valueOfElement) { 
                            re += `<li> <a href="`+valueOfElement+`" target="_blank" > `+indexInArray+` </a> </li>`;
                        });
						t += `<tr><td>`+v.store_view_id+`</td>`;
						t += `<td>`+v.test_id+`</td>`;
						t += `<td>`+v.status+`</td>`;
						t += `<td>`+v.error+`</td>`;
						t += `<td><a href="`+v.website_url+`" target="_blank" title="Goto website"> Website </a></td>`;
						t += `<td> <a href="`+v.report_url+`" target="_blank" title="Show report"> Reprot </a></td>`;
						t += `<td>`+v.html_load_time+`</td>`;
						t += `<td>`+v.html_bytes+`</td>`;
						t += `<td>`+v.page_load_time+`</td>`;
						t += `<td>`+v.page_bytes+`</td>`;
						t += `<td>`+v.page_elements+`</td>`;
						t += `<td>`+v.pagespeed_score+`</td>`;
						t += `<td>`+v.yslow_score+`</td>`;
						t += `<td>`+re+`</td>`;
						t += `<td>`+v.created_at+`</td></tr>`;
					});
					if( t == '' ){
						t = '<tr><td colspan="5" class="text-center">No data found</td></tr>';
					}
				}
				$("#gtmetrix-history-modal").find(".show-list-records").html(t);
				$("#gtmetrix-history-modal").modal("show");
                btn.prop('disabled',false);
			},
			error: function (){
                btn.prop('disabled',false);
			}
		});
    });

    $(document).on("click",".run-test",function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = $(this).data("id");
        $.ajax({
            url: "/gtmetrix/run-event",
            type: 'POST',
            data : { _token: "{{ csrf_token() }}", id : id },
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function(result){
                $("#loading-image").hide();
                if(result.code == 200) {
                    toastr["success"](result.message);
                }else{
                    toastr["error"](result.message);
                }
            },
            error: function (){
                $("#loading-image").hide();
                toastr["error"]("Something went wrong please check log file");
            }
        });
    });

</script>

@endsection
