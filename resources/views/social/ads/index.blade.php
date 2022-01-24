@extends('layouts.app')


@section('title', 'Social  Ads')

@section('content')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @include("social.ads.history")
   
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Social  Ads ({{ $ads->total() }})<span class="count-text"></span></h2>
            <div class="pull-right">
                <a class="btn btn-secondary create-post">+</a>
            </div>
        </div>

        <br>
        <div class="col-lg-12 margin-tb">

            <div class="col-md-12 margin-tb">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout:fixed;">
                        <tr>
                            <th style="width:5%">Date</th>
                            <th style="width:7%"> Name</th>
                            <th style="width:7%">Config Name</th>
                           
                            <th style="width:7%">Adset Name</th>
                            <!-- <th style="width:10%">Image</th> -->
                            <th style="width:17%">Creation Name</th>
                            <th style="width:5%">Status</th>
                            <th style="width:5%">Live Status    </th>
                            <th style="width:5%">Action</th>
                        </tr>
                        <tbody class="infinite-scroll-data">
                            @include("social.ads.data")
                        </tbody>
                    </table>
                </div>
                {{ $ads->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
    </div>
    <div id="create-modal" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="record-content">
                    
            </div>
        </div>
    </div>


    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>

    <script type="text/javascript">
        $(document).on("click",".account-history",function(e) {
        e.preventDefault();
            var post_id = $(this).data("id");
            $.ajax({
                url: "{{ route('social.ad.history') }}",
                type: 'POST',
                data : { "_token": "{{ csrf_token() }}", post_id : post_id },
                dataType: 'json',
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();

                    if(result.code == 200) {
                       var t = '';
                       $.each(result.data,function(k,v) {
                          t += `<tr><td>`+v.post_id+`</td>`;
                          t += `<td>`+v.log_title+`</td>`;
                          t += `<td>`+v.log_description+`</td>`;
                          t += `<td>`+v.created_at+`</td>`;
                          t += `<td>`+v.updated_at+`</td></tr>`;
                       });
                    }
                    $("#log-history-modal").find(".show-list-records").html(t);
                    $("#log-history-modal").modal("show");
                },
                error: function (){
                    $("#loading-image").hide();
                }
            });
       });
        $(document).on('click', '.create-post', function(e) {
             e.preventDefault();
            
            var $action_url = "{{ route('social.ad.create') }}";
            jQuery.ajax({

                type: "GET",
                url: $action_url,
                dataType: 'html',
                success: function(data) {
                    $("#create-modal").modal('show');
                    $("#record-content").html(data);

                },
                error: function(error) {},

            });
            return false;

        });

        $(document).on('change','#adset_id',function(){
            var g_name = $('option:selected', this).attr('rel');
            $("#ad_set_name").val(g_name);
            //alert(g_name);
        });
        $(document).on('change','#adcreative_id',function(){
            //alert($(this).attr("rel"));
            var g_name = $('option:selected', this).attr('rel');
            $("#ad_creative_name").val(g_name);
        });
        $(document).on('change','#config_id',function(){
            //alert("rerere");
            if($(this).val() != ""){
             //alert($(this).val());
                $.ajax({
                    url:'{{route("social.ad.getpost")}}',
                    dataType:'json',
                    data:{
                        id:$(this).val(),
                    },
                    success:function(result){
                        //console.log(result);
                        if(result.type=="success"){
                            $("#loading-image").hide();
                            let html = `<option value="">-----Select Adsets-----</option>`;
                            if(result.message.adsets.data){
                                console.log("come toadsets adsets ");
                                console.log(result.message.adsets.data);
                                $.each(result.message.adsets.data,function(key,value){
                                    html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`; 
                                });
                            }
                            $('#adset_id').html(html);
                            let c_html = `<option value="">-----Select AdCreative-----</option>`;
                            if(result.message.adcreatives.data){
                                console.log("come toadsets adcreatives ");
                                console.log(result.message.adcreatives.data);
                                $.each(result.message.adcreatives.data,function(key,value){
                                    c_html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`; 
                                });
                            }
                            $('#adcreative_id').html(c_html);
                            
                        }else{
                            $("#loading-image").hide();
                            alert("token Expired");
                        }
                    },
                    error:function(exx){

                    }
                });
            }
		});

        $(document).on('change','#post_id',function(){
            alert($(this).val());
            if($(this).val()!= ""){
                var object_story_title = $(this).attr("rel");
                $("#object_story_title").val(object_story_title);
            }
        });


        $(window).scroll(function() {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                loadMore();
            }
        });

        var isLoadingProducts;

        function loadMore() {
            if (isLoadingProducts)
                return;
            isLoadingProducts = true;
            if (!$('.pagination li.active + li a').attr('href'))
                return;

            var $loader = $('.infinite-scroll-products-loader');
            $.ajax({
                    url: $('.pagination li.active + li a').attr('href'),
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                        $('ul.pagination').remove();
                    }
                })
                .done(function(data) {
                    if ('' === data.trim())
                        return;

                    $loader.hide();

                    $('.infinite-scroll-data').append(data);

                    isLoadingProducts = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
        }
    </script>
@endsection
