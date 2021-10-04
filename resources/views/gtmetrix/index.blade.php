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
        <button class="btn btn-secondary multi-run-test-btn btn-xs" onclick="checkCheckbox()" title="Run Test">
            <i class="fa fa-play"></i>
        </button>
        <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setCron" title="" data-id="1">
                Add
            </button>
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
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <table class="table table-bordered table-striped table-responsive site-gtMetrix-data">
                    <thead>
                        <tr>
                            @php
                              $url=url('gtmetrix');
                              $p='';
                              if (isset($_GET['date']))
                                 {
                                     if ($p=='')
                                       $p="?date=".$_GET['date'];
                                     else
                                        $p.= "&date=".$_GET['date']; 
                                 }
                                 if (isset($_GET['status']))
                                 {
                                     if ($p=='')
                                       $p="?status=".$_GET['status'];
                                     else
                                        $p.= "&status=".$_GET['status']; 
                                 } 
                                 if (isset($_GET['keyword']))
                                 {
                                     if ($p=='')
                                       $p="?keyword=".$_GET['keyword'];
                                     else
                                        $p.= "&keyword=".$_GET['keyword']; 
                                 } 
                                 
                                 $ord1='';
                                 if (isset($_GET['sortby']) && $_GET['sortby']=='pagespeed_score' && $_GET['ord']=='asc' )
                                 {
                                     if ($p=='')
                                     $ord1="?sortby=pagespeed_score&ord=desc";
                                     else
                                     $ord1.= "&sortby=pagespeed_score&ord=desc"; 
                                 }
                                 else
                                 {
                                    if ($p=='')
                                     $ord1="?sortby=pagespeed_score&ord=asc";
                                     else
                                     $ord1.= "&sortby=pagespeed_score&ord=asc"; 
                                 }

                                 $ord2='';
                                 if (isset($_GET['sortby']) && $_GET['sortby']=='yslow_score' && $_GET['ord']=='asc' )
                                 {
                                     if ($p=='')
                                     $ord2="?sortby=yslow_score&ord=desc";
                                     else
                                     $ord2.= "&sortby=yslow_score&ord=desc"; 
                                 }
                                 else
                                 {
                                    if ($p=='')
                                     $ord2="?sortby=yslow_score&ord=asc";
                                     else
                                     $ord2.= "&sortby=yslow_score&ord=asc"; 
                                 }
                                 $url1=$url.$p.$ord1;
                                 $url2=$url.$p.$ord2;

                            @endphp
                            <th>Website <br><input type="checkbox" onclick="selectAll();" />&nbsp;Select All</th>
                            <th>Test id</th>
                            <th>Status</th>
                            <th>Error</th>
                            <th>Report URL</th>
                            <th>Html load time</th>
                            <th>Html bytes</th>
                            <th>Page load time</th>
                            <th>Page bytes</th>
                            <th>Page elements</th>
                            <th>

                              <a href="{{$url1}}">  Pagespeed score</a>
                            </th>
                            <th><a href="{{$url2}}">Yslow score</a></th>
                            <th style="width: 12%;">Resources</th>
                            <th style="width: 7.5%;">Date</th>
                            <th>PDF</th>
                            <th style="width: 10.5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
                        @foreach ($list as $key)
                        
                            <tr>
                            
                                <td> <input type="checkbox" name ="multi-run-test-type" class= "multi-run-test" value ="{{ $key->id }}"><a href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a></td>
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
                                    @if (!empty($key->resources) && is_array($key->resources))
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
                                    <button class="btn btn-secondary show-history btn-xs" title="Show old history" data-url="{{ route('gtmetrix.web-hitstory',[ 'id'=>$key->website_url ])}}">
                                        <i class="fa fa-history"></i>
                                    </button>
                                    <button class="btn btn-secondary run-test btn-xs" title="Run Test" data-id="{{ $key->id }}">
                                        <i class="fa fa-play"></i>
                                    </button>
                                    @if ($key->status == "completed")
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Pagespeed Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'pagespeed','id'=>$key->test_id])}}" data-type="pagespeed">
                                            <i class="fa fa-tachometer"></i>
                                        </button>
                                        <button class="btn btn-secondary show-pagespeed btn-xs" title="Show Yslow Stats" data-url="{{ route('gtmetrix.getPYstats',['type'=>'yslow','id'=>$key->test_id])}}">
                                            <i class="fa fa-compass"></i>
                                        </button>
                                        <button class="btn btn-secondary show-comparison btn-xs" title="Show comparison" data-url="{{ route('gtmetrix.getstatsCmp',['id'=>$key->test_id])}}">
                                        <i class="fa fa-balance-scale"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
<!-- 
                <td colspan="12">
                    {{ $list->links() }}
                </td> -->
            </div>
        </div>
        <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

       
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
   50% 50% no-repeat;display:none;">
</div>
<div class="modal fade" id="gtmetrix-stats-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

<div class="modal fade" id="gtmetrix-comparison-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

<div class="modal fade" id="gtmetrix-history-modal" role="dialog">
    <div class="modal-dialog modal-xl model-width w-100">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

@include('gtmetrix.setSchedule')
@include('gtmetrix.setCron')
@endsection
    
@section('scripts')
<script>

//$(".site-gtmetrix-data tbody>tr").append("<input type='checkbox' />");
    function setactive(id){
//   $(".nav-tabs li.nav-item a.nav-link").removeClass('active');

        if(id =="a_tab"){
            $("#PageSpeed").addClass("active");
            $("#YSlow").removeClass("active");
            console.log(id);
            
            }else{
            $("#YSlow").addClass("active");
            $("#PageSpeed").removeClass("active");
            console.log(id);

            }
        };


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
            $('.message-modal').html('');    
            $('.message-modal').html(data); // load response 
            $("#gtmetrix-stats-modal").modal("show");
            $('#loading-image').hide();        // hide ajax loader 
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    });
    function selectAll() {
        if ($('.multi-run-test').prop('checked')) {
            $('.multi-run-test').prop('checked',false);
        } else {
            $('.multi-run-test').prop('checked',true);
        }

    }
    $(document).on('click', '.show-comparison', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        $('#gtmetrix-comparison-modal .message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
            $('#gtmetrix-comparison-modal .message-modal').html('');    
            $('#gtmetrix-comparison-modal .message-modal').html(data); // load response 
            $("#gtmetrix-comparison-modal").modal("show");
            $('#loading-image').hide();        // hide ajax loader 
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    });

    const showHistory = (url) => {
        $('.message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
            $('#gtmetrix-history-modal .message-modal').html('');    
            $('#gtmetrix-history-modal .message-modal').html(data); // load response 
            $("#gtmetrix-history-modal").modal("show");

            $('#loading-image').hide();        // hide ajax loader 
            pageClick();
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    }

    const pageClick = () => {
        $('#gtmetrix-history-modal .pagination a.page-link').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            let href = $(this).attr('href');
            showHistory(href);
            return false;
        });
    }

    $(document).on('click', '.show-history', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        showHistory(url);
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
    var arrayList = [];
    $(document).on("click",".multi-run-test",function() {
        
        $("input:checkbox[class=multi-run-test]:checked").each(function () {
            if(arrayList.length == 0){
                arrayList.push($(this).val()); 
            }
            else{
               let findValue = arrayList.find(item =>{
                if(item!==$(this).val()) 
                arrayList.push($(this).val());
                })
            }
            console.log(arrayList,"array");
            
        });
    });
    
    function checkCheckbox() {  
        
        console.log(arrayList,"final ertyuy array");
        if(arrayList.length){
           
            $.ajax({
                url: "/gtmetrix/multi-run-event",
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", arrayList : arrayList},
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
            })
        }else{

            toastr["error"]("Please select atleast one checkbox");
        }
        
    };

</script>

<script>
        
        var isLoading = false;
        var page = 1;
        $(document).ready(function () {
            
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMore();
                }
            });

            function loadMore() { 
              if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                const params = new URLSearchParams(window.location.search); 
                keyword = params.get('keyword');
                status = params.get('status');
                date = params.get('date');
                if(keyword == null) {
                    keyword = '';
                }
                if(status == null || status == 'null') { 
                    status = '';
                }
                if(date == null) {
                    date = '';
                }
                 var url = "{{url('gtmetrix')}}?ajax=1&page="+page+"&date="+date+"&status="+status+"&keyword="+keyword;
                
                console.log(url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });

       

  </script>      


@endsection
