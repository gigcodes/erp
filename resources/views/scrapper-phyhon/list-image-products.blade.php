@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">
        .select-multiple-cat-list .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }
        /*.update-product + .select2-container--default{
            width: 60% !important;
        }*/
        .no-pd {
            padding:0px;
        }

        .select-multiple-cat-list + .select2-container {
            width:100% !important;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .row .btn-group .btn {
            margin: 0px;
        }
        .btn-group-actions{
            text-align: right;
        }

        .multiselect-supplier + .select2-container{
            width: 198px !important;
        }
        .size-input{
            width: 155px !important;
        }
        .quick-sell-multiple{
            width: 98px !important;
        }
        .image-filter-btn{
            padding: 10px;
            margin-top: -12px;
        }
        .update-product + .select2-container{
            width: 150px !important;
        }
        .product-list-card > .btn, .btn-sm {
            padding: 5px;
        }

        .select2-container {
            width:100% !important;
            min-width:200px !important;
        }
        .no-pd {
            padding:3px;
        }
        .mr-3 {
            margin:3px;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <style>
               .product-list-card::-webkit-scrollbar-track, .image-diamention-rasio::-webkit-scrollbar-track
        {
            border: 1px solid transparent;
	background-color: #F5F5F5;
        }

        .product-list-card::-webkit-scrollbar, .image-diamention-rasio::-webkit-scrollbar
        {
            width: 5px;
	background-color: #F5F5F5;
        }

        .image-diamention-rasio-desktop
        {
            text-align: center;
            text-align: center;
            width: fit-content;
            /* display: flex; */
            overflow: auto;
            margin-bottom: 30px;
        }
        .image-diamention-rasio-mobile
        {
            text-align: left;
            width:100%;
            /* display: flex; */
            overflow-y: auto;
            overflow-x: hidden;
            margin-bottom: 30px;
        }

        .manage-product-image
        {
            padding-bottom: 20px;
            border-bottom: 1px solid;
            margin-bottom: 20px;
            object-fit:cover;
        }
        .product-list-card::-webkit-scrollbar-thumb, .image-diamention-rasio::-webkit-scrollbar-thumb
        {
            background-color: transparent;	
        }
        .infinite-scroll-images{
            max-width:1150px;
            margin:0 auto;
        }

        .infinite-scroll-images-mobile{
            max-width:360px;
            margin:0 auto;
        }

        .infinite-scroll-images-tablet{
            max-width:767px;
            margin:0 auto;
        }
    </style>
@endsection

@section('content')
<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
<br/>
<div style="position:fixed;z-index:1"><button class="btn btn-secondary hide start-again" onclick="callinterval();" disabled>Start Scroll</button>
<button class="btn btn-secondary stopfunc hide pause" id="clearInt">Stop Scroll</button></div>
<div class="row">
    <div class="col-sm-2">  
       
    </div>
    <div class="col-sm-3">  
        <div class="form-group">
            <input type="text" class="form-control" id="scrolltime" placeholder="scroll interval in second"/>
        </div>
    </div>
    <div class="col-sm-1">  
        <div class="form-group">
        <input type="button" onclick="callinterval()" class="btn btn-secondary" value="Start"/>
        </div>
    </div>
</div>



<div>
    <form method="get" >
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    
                </div>
                <div class="col-md-2">
                    <select class="form-control select-multiple globalSelect2" id="web-select" tabindex="-1" aria-hidden="true" name="website" onchange="showStores(this)">
                        <option value="">Select Website</option>
                        @foreach($allWebsites as $websiteRow)
                            @if(isset($_REQUEST['web_id']) && $websiteRow->id==$_REQUEST['web_id'])
                                <option value="{{$websiteRow->id}}" selected="selected">{{$websiteRow->name}}</option>
                            @else
                                <option value="{{$websiteRow->id}}">{{$websiteRow->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-">
                    <select class="form-control select-multiple globalSelect2" id="web_store" tabindex="-1" aria-hidden="true" name="store" onchange="showLanguages(this)">
                        <option value="">Select Store</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control select-multiple globalSelect2" id="web_language" tabindex="-1" aria-hidden="true" name="language">
                        <option value="">Select Language</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control select-multiple" id="web_device" tabindex="-1" aria-hidden="true" name="device">
                        <option value="">Select Device</option>

                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "desktop" ? 'selected' :'' ) }} value="desktop">Desktop</option>
                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "mobile" ? 'selected' :'' ) }} value="mobile">Mobile</option>
                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "tablet" ? 'selected' :'' ) }} value="tablet">Tablet</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-image filter_img" ><img src="/images/filter.png"></button>
               
                    <!-- <button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>     -->
                 </div>
            </div>
        </div>
    </form>
</div>
<!-- END - DEVTASK-4271 -->

<!-- Purpose : Add class infinite-scroll - DEVTASK-4271 -->
<div class="infinite-scroll customer-count infinite-scroll-data customer-list-{{$website_id}} customer-{{$website_id}}" style="padding: 0px 10px;">
        @php
            $oldDate = null;
            $count   = 0;
            /*$images = null;
            // dd( $list->store_website_id );
            $webStore = \App\WebsiteStore::where('website_id',$list->id)->first();
            if( $webStore ){
                $website_store_views = \App\WebsiteStoreView::where('website_store_id',$webStore->id)->first();
                if( $website_store_views ){
                    $images = \App\scraperImags::where('store_website',$list->store_website_id)->where('website_id',$website_store_views->code)->get()->toArray();
                }
            }*/

            // dump($list);
            // dump($webStore);
            // dump($website_store_views);
            // dd($images);
            $device = ((isset($_REQUEST['device'])) ? $_REQUEST['device'] : '' );

            if($device == "desktop" || $device == ""){
                 $imageHeight = 'fit-content';
                 $imageWidth = 'infinite-scroll-images';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = 'fit-content';
            }
            else if($device == "tablet"){
                 $imageHeight = '800px';
                 $imageWidth = 'infinite-scroll-images-tablet';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = '100%';

            }
            else if($device == "mobile"){
                 $imageHeight = '600px';
                 $imageWidth = 'infinite-scroll-images-mobile';
                 $imageDimensioClass ='image-diamention-rasio-mobile';
                 $width = '100%';
            }
            
        @endphp

        @foreach($images as $image)
                {{-- @foreach($imageM->scrapperImage->toArray() as $image) --}}

                <?php
                    if ( date( 'Y-m-d' ,strtotime($image['created_at'])) !== $oldDate ) { 
                        $count = 0; 
                        $oldDate = date( 'Y-m-d' ,strtotime($image['created_at']));
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <h5 class="product-attach-date" style="margin: 5px 0px;">{{$image['created_at']}} || Number Of Images:{{count($images)}}</h5> 

                                <hr style="margin: 5px 0px;">
                            </div>
                        </div> 

                    <?php } ?>
                    
                @if ($image['img_name'] )
                    @php
                    if($count == 6){
                        $count = 0;
                    }
                    
                    @endphp
                        {{-- START - Purpose : Comment Code - DEVTASK-4271 --}}
                        {{--  @if($count == 0)
                              <div class="row parent-row">
                        @endif --}}
                        {{-- END - DEVTASK-4271 --}}
                        <div class="{{ $imageWidth }}" style="position: relative;">               
                            @if ($image['coordinates'])
                                @php 
                                    $x = 0;
                                    $coordinates = explode(',',$image['coordinates']);
                                    array_push($coordinates,$image['height']);
                                    $total_img_height = $image['height'];
                                @endphp
                        

                                <div class="image-diamention-rasio {{ $imageDimensioClass }}" style="max-height: {{ $imageHeight }};">
                                    @foreach ($coordinates as $z)
                                        @php
                                            if($device == "mobile"){
                                                $z = ceil((2372*$z)/$total_img_height);
                                            }
                                            if($device == "tablet"){
                                                $z = ceil((5070*$z)/$total_img_height);
                                            }
                                        @endphp
                                        <td>
                                            <img data-coordinates="{{ $z}}" class="manage-product-image" src="{{ asset( 'scrappersImages/'.$image['img_name']) }}" style="object-position: 100% -{{ $x }}px;height:{{ $z - $x+20 }}px;width:{{ $width }}">
                                        </td>
                                        @php $x = $z; @endphp
                                    @endforeach
                                </div>
                            @else    
                                <div class="col-md-12 col-xs-12 text-center product-list-card mb-4 " style="padding:0px 5px;margin-bottom:2px !important;max-height:{{ $imageHeight }};overflow:auto">
                                    <div style="padding:0px 5px;">
                                        <div data-interval="false" id="carousel_{{ $image['id'] }}" class="carousel slide" data-ride="carousel">
                                            <a href="#" data-toggle="tooltip" data-html="true" data-placement="top" >
                                                <div class="carousel-inner maincarousel">
                                                    <div class="item" style="display: block;"> <a data-fancybox="gallery" href="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" ><img src="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" style="height: 100%; width: 100%; max-width:fit-content; display: block;margin-left: auto;margin-right: auto;"> </a> </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                        
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn-secondarys" data-toggle="modal" data-target="#remark-area-list" style="position: absolute;top: 0;right : -43px;"><i class="fa fa-comments"></i></button>  
                        </div>
                    

                    {{-- START - Purpose : Comment Code - DEVTASK-4271 --}}
                    {{-- @php
                    if( $count == 0 || $count == 6){
                        echo '</div>';
                    }
                    @endphp--}}
                    {{-- END - DEVTASK-4271 --}}

                @endif
            @php $count++;  @endphp
        {{-- @endforeach --}}
        @endforeach
        <br>
</div>
<!--Remark Modal-->
<div id="remark-area-list" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
			<div class="modal-body">
				<div class="col-md-12">
                    <select name="" id="" class="col-md-2 form-control site-development-category" data-website_id="{{ $website_id }}">
                        <option value="">Select</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
					<div class="col-md-8 pr-0" style="padding-bottom: 10px;">
						<textarea class="form-control" col="5" name="remarks" data-id="" id="remark-field" placeholder="Enter Remarks" style="height:34px"></textarea>
					</div>
					<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-remark-field pl-0" data-website_id="{{ $website_id }}">
						<img src="/images/send.png" style="cursor: default;">
					</button>
				</div>
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="45%">Remark</th>
								<th width="25%">BY</th>
								<th width="25%">Date</th>
							</tr>
						</thead>
						<tbody class="remark-action-list-view">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!--Remark Load More Data Modal -->
<div id="remark-load-more-data" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Remark</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script>
    var i=1;
    $(document).ready(function() { 
        $("#scrolltime").val('2');
        setTimeout(() => {
        callinterval();
        }, 0);
        showStores();
        showLanguages();
    });

    var scroll = true;
        var old_product;
        function start_scroll_down() { 
            var img_count = $(".infinite-scroll-data img").length;
            if(scroll && i < img_count){
                if(i == 1)
                {
                    $("html, body").animate({
                    scrollTop: $(".infinite-scroll-data div").eq(1).offset().top
                    }, 300).delay(300); 
                    i+=1;
                }else{
                    $("html, body").animate({
                    scrollTop: $(".infinite-scroll-data div img").eq(i).offset().top
                    }, 500).delay(500); 
                    i+=1;
                }
                
            }else{
                console.log("no scroll")
            }
        }

    var stop;
    function callinterval(){
        
        if($("#scrolltime").val() == ""){
            toastr["error"]("please add time interval for scroll");
            return;
        }
            
        $(".start-again").removeClass("hide")
        $(".pause").removeClass("hide")

        $(".start-again").attr("disabled","disabled")
        $(".pause").attr("disabled",false)

        if(i == 1)
        {
            $("html, body").animate({
            scrollTop: $(".infinite-scroll-data div").eq(1).offset().top
            }, 300).delay(300); 
            i+=1;
        }else{
            $("html, body").animate({
            scrollTop: $(".infinite-scroll-data div img").eq(i).offset().top
            }, 500).delay(500); 
            i+=1;
        }

        stop = setInterval(function(){ console.log("Running");start_scroll_down() }, $("#scrolltime").val()*1000);
    }

    
    $('#clearInt').click(function(){ 
        $(".start-again").attr("disabled",false)
        $(".pause").attr("disabled","disabled")
        clearInterval(stop);
        console.log("Stopped");
    });

    function showStores(selector)
    {
       
        var website=$(selector).val();
       
        if(website == undefined)
        {
            var website="{{ (isset($_REQUEST['web_id']) ? ($_REQUEST['web_id']) : '' )}}";
        }

        $('[name="store"]').find('option').eq(1).not().remove();


        if(website)
        {
            $.get('{{route("website.store.list")}}'+'/'+website,function(res)
            {
                if(res.status && res.list.length)
                {
                    $.each(res.list,function(k,v){

                    //console.log(k,v);
                    var selected='';

                    if(v.id=="{{$_REQUEST['id']??0}}")
                    {
                        selected='selected'
                    }

                    $('[name="store"]').append('<option value="'+v.id+'" '+selected+'>'+v.name+'</option>');

                    })
                    
                }
                else
                {
                    $('[name="store"]').find('option').eq(1).not().remove();
                }
            })
        }
    }

    function showLanguages(selector)
    {
        var store=$(selector).val();
        $('[name="language"]').find('option').eq(1).not().remove();

        if(store == undefined)
        {
            var store="{{ $_REQUEST['id'] }}";
        }

        if(store)
        {
            $.get('{{route("store.language.list")}}'+'/'+store,function(res)
            {
                if(res.status && res.list.length)
                {
                    $.each(res.list,function(k,v){

                    //console.log(k,v);
                    var selected='';

                    if(v.code=="{{$_REQUEST['code']??''}}")
                    {
                        selected='selected'
                    }

                    $('[name="language"]').html('<option value="">Select Language</option><option value="'+v.code+'" '+selected+'>'+v.name+' ('+v.code+') '+'</option>');

                    })
                    
                }
                else
                {
                    $('[name="language"]').find('option').eq(1).not().remove();
                }
            })
        }
    }

    $(document).on('click', '.filter_img', function (e) {     
        var website = $('#web-select').find(":selected").val();
        var webstore = $('#web_store').find(":selected").val();
        var weblanguage = $('#web_language').find(":selected").val();
        var webdevice = $('#web_device').find(":selected").val();
        if(website == '' ){
             toastr['error']('Please Select Website');
             return false;
        }
        else if(webstore == ''){
            toastr['error']('Please Select Store');
             return false;
        }
        else if(weblanguage == ''){
            toastr['error']('Please Select Language');
             return false;
        }

        if(webdevice != '' && (website == '' || webstore == '' || weblanguage == ''))
        {
            var full_url   = window.location.href; 
            const myArr = full_url.split("&");
                
            var url_path = myArr[0]+'&'+myArr[1];
            var url = full_url+"&device="+webdevice;
        }else{

            if(website == '' || webstore == '' || weblanguage == '')
            {
                var full_url   = window.location.href; 
                const myArr = full_url.split("&");
                
                var url_path = myArr[0]+'&'+myArr[1];
                var url = url_path+"&device="+webdevice;
            }else{
                var origin   = window.location.origin;
                var url = origin+"/scrapper-python/list-images?web_id="+website+"&id="+webstore+"&code="+weblanguage+"&device="+webdevice;
            }
        }
       
        window.location.href = url;
    });

    // $(document).ready(function() {
    //     var screenHeight = screen.height;
        
    //     $('.manage-product-image').css('height',  screenHeight );
    //     $(window).resize(function(){
    //         var screenHeight = screen.height;
    //         $('.manage-product-image').css('height',  screenHeight );
    //     });
    // })


    $(document).on("click", ".btn-remark-field", function() {
        var cat_id = $(this).parents('.modal-body').find('.site-development-category').val();
        var remark = $(this).parents('.modal-body').find('textarea').val();
        var website_id = $(this).data('website_id');
        if (!cat_id) {
            alert('Please Select Categories')
            return;
        }
        if (!remark) {
            alert('Please Enter Remarks')
            return;
        }
        $.ajax({
            type: "get",
            url: "{{ route('image-remark.store') }}",
            data: {
                remark: remark,
                cat_id:cat_id,
                website_id:website_id,
            },
            success: function(response){
                toastr.success(response.message);
                var html = '<tr><td>'+response.remark.id+'</td><td class="load-more-remarks" data-remark="'+response.remark.remarks+'">'+response.remark.remarks+'</td><td>'+response.username+'</td><td>'+response.remark.created_at+'</td></tr>';
                $('.remark-action-list-view').append(html);
            }
        })
    });
    $(document).on('change','.site-development-category',function(){
        $('.remark-action-list-view').show();
        var remark = $(this).val();
        var website_id = $(this).data('website_id');
        $('.remark-action-list-view').html('');
        $.ajax({
            type: "get",
            url: "{{ route('change-category.remarks-show') }}",
            data: {
                remark: remark,
                website_id:website_id,
            },
            success: function(response){
                for (let i = 0; i < response.remarks.length; i++) {
                    var remark = response.remarks[i];
                    if (remark.remarks.length > 80) {
                        remark.remarks = remark.remarks.substring(0, 80)+'...';
                    }
                    var html = '<tr><td>'+remark.id+'</td><td class="load-more-remarks" data-remark="'+remark.remarks+'">'+remark.remarks+'</td><td>'+remark.username+'</td><td>'+remark.created_at+'</td></tr>';
                    $('.remark-action-list-view').append(html);
                }
            }
        })
    });
    $('#remark-area-list').on('hide.bs.modal', function (e) {
        $('.site-development-category').val('');
        $('#remark-field').val('');
        $('.remark-action-list-view').hide();
    });


    $(document).on('click','.load-more-remarks', function (){
        var remark = $(this).data('remark');
        if (remark.length <= 80) {
            return;
        }
        $('#remark-load-more-data').modal('show');
        $('#remark-load-more-data').find('p').text(remark);
    })
</script>
<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
@endsection