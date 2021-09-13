@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
		  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
		.seo_content {     width: 100%;     float: left; padding:50px 0;}
		.seo_content .seo_content_inner {     width: 100%;     float: left;}
		.seo_content .seo_content_inner .seo_text {     padding: 0 30px;     border-right: 2px solid #bfbfbf; }
		.seo_content .seo_content_inner .seo_text:last-child {     border-right: none; }
		.seo_content .seo_content_inner .seo_text_inner {     width: 100%;     float: left; }
		.seo_content .seo_content_inner .seo_text_inner h6 {     width: 100%;     float: left;     margin: 0; text-transform:capitalize;    font-size: 18px;     line-height: 28px;     font-weight: normal;     color: #000;     padding: 0 0 4px 0; font-family: 'Poppins', sans-serif;}
		.seo_content .seo_content_inner .seo_text_inner span {     width: 100%;     float: left;     font-size: 24px;     font-weight: 600;     line-height: 32px; font-family: 'Poppins', sans-serif; position:relative;}
		.seo_content .seo_content_inner .seo_text_inner span::after {     position: absolute;     content: '';     width: 100px;     height: 2px;     background: red;     left: 0;     bottom: -10px; }
		.seo_content .seo_content_inner sub {     width: auto;     color: #d23636;     font-weight: 600;     font-size: 12px;     line-height: 24px; font-family: 'Poppins', sans-serif;}
		.seo_text.seo_fir {     padding: 0 !important; }
		.row.seo_select .form-group {     padding: 0; }
		.seo_select_inner {     padding: 0; }
		ul#select2-search-results li.select2-results__option {     width: 100%;     float: left;     font-family: 'Poppins', sans-serif; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__arrow {     height: 40px !important; }
		.row.seo_select label {     width: 100%;     float: left;     margin: 0;     text-transform: capitalize;     font-size: 20px;     line-height: 32px;     font-weight: normal;     color: #000;     padding: 0 0 4px 0;     font-family: 'Poppins', sans-serif; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__rendered {     text-align: left;     font-family: 'Poppins', sans-serif;     font-size: 14px;     padding: 0 10px;     width: 100%;     float: left; }
		.row.seo_select .select2-container--default .select2-selection--single .select2-selection__rendered {     line-height: 40px !important;     color: #757575; }
		.row.seo_select .select2-container .select2-selection--single {     height: 40px !important;     border: 1px solid #ddd !important;     color: #757575;     padding-left: 6px;     width: 100%;     float: left; }
		.row.seo_select .select2-hidden-accessible option {     font-family: 'Poppins', sans-serif;     width: 100%;     float: left; }
		.seo_tabs{width:100%; float:left; padding:0 15px;}
		.seo_tabs.nav-tabs>li>a{margin-right:0; font-family: 'Poppins', sans-serif;   color: #000;}
		.seo_tabs.nav-tabs>li.active>a, .seo_tabs.nav-tabs>li.active>a:focus, .seo_tabs.nav-tabs>li.active>a:hover {     color: #555;     cursor: default;     background-color: #fff;     border: 1px solid #ddd;     border-bottom-color: transparent;     font-family: 'Poppins', sans-serif;     font-weight: bold; }
		.row.seo_select {     padding: 30px 0 0 0; }
		
		
		
		@media(max-width:1199.5px){
			div.container{width:95%; max-width:95%;}  
			}
		@media(max-width:991.5px){
			.seo_text {     width: 33%;     float: left; } 		
			}
		@media(max-width:767.5px){	
			.seo_text {     width: 50%; }
			.seo_content .seo_content_inner .seo_text:nth-child(3n) {     border-right: none; }
			.seo_content .seo_content_inner .seo_text:last-child {     border-right: none;     padding: 40px 0 0; }
		} 
		@media(max-width:480.5px){	
		.seo_content .seo_content_inner .seo_text_inner {     padding: 0 0 50px 0; }
		.seo_text {     width: 100%; }
		.seo_content .seo_content_inner .seo_text{border:none;}
		.seo_content .seo_content_inner .seo_text_inner span::after {     left: 50%;     transform: translateX(-50%);     bottom: -20px; }
		.seo_content .seo_content_inner .seo_text:last-child {     border-right: none;     padding: 0; }
		.seo_content .seo_content_inner{text-align:center;}
		}
		
		/******/
.short_report >* { padding: 0 !important; float: left; width: 100%; margin: 0; }
.short_report { padding: 0 0 50px; float: left; width: 100%; border-bottom: 1px solid #efefef69; border-radius: 3px; }
.short_report h4 { font-size: 22px; font-weight: 500; margin: 30px 0 20px; }
.short_report .seo_content .container { box-shadow: -2px 0px 9px #00000029; padding: 10px 20px 30px; border-radius: 10px; }
.short_report h3 {
    font-size: 24px;
    line-height: 36px;
    font-weight: 600;
    /* padding: 10px 15px; */
    color: #000;
    margin: 30px 0 0;
    display: inline-block;
    /* box-shadow: -4px -3px 9px #00000029; */
}
		/******/
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Dashboard</h2>
                    <!--button type="button" class="btn btn-secondary float-right mr-3" data-toggle="modal"
                            data-target="#addToolModal">
                        Add Tool
                    </button-->
                </div>
            </div>
        </div>
    </div>

    <!-- Model Add Seo tool START -->
    <div id="addToolModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Add new tool</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form name="add-seo-tool" style="padding:10px;"
                      action="{{ route('save.seo-tool') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="tool" placeholder="Tool" value="" required="">
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="api_key" placeholder="Api Key" value="" required="">
                    </div>
                    <button type="submit" class="btn btn-secondary">Add Tool</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Model Add Seo tool END -->
<div class="form-group col-md-12" style="display:none;">
				<div class="seo_select_inner col-md-4 ">
					<label for="with_archived">Select Website</label>
					{{ Form::select('search', $websites, null, array('class'=>'search select2', 'placeholder'=>'Seletc Website', 'id'=>'search')) }}
				</div>
			</div>

    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
	
@foreach($websites as $websiteId=>$website) 
	<!--20-08-2021-->
	
	@if(isset($domainOverview[$websiteId]) || isset($backlinkreports[$websiteId]) || isset($siteAudits[$websiteId]))
		<div class="short_report">
			<div class="row seo_select" >
				<div class="container">
					<h3>{{$website}}</h3>
				</div>
			</div>
	@if(isset($siteAudits[$websiteId]))
	@php $siteAudit = $siteAudits[$websiteId]; @endphp
	<div class="row seo_select" >
		<div class="container">
			<h4>Site Audit</h4>			
		</div>
	</div>
	
	<div class="seo_content">
		<div class="container">
			<div class="seo_content_inner">
				@if(isset($siteAudit['pages_crawled']))
				<div class="seo_text seo_fir col-md-4">
					<div class="seo_text_inner">
						<h6>Pages Crawled</h6>
						<a href="{{route('site-audit-details', $websiteId)}}"><span>{{$siteAudit['pages_crawled']}}</span></a>
					</div>
				</div>
				@endif
				@if(isset($siteAudit['errors']))
				<div class="seo_text col-md-4">
					<div class="seo_text_inner">
						<h6>Errors</h6>
						<a href="{{route('site-audit-details', $websiteId)}}"><span> {{$siteAudit['errors']}}</span></a>
					</div>
				</div>
				@endif
				@if(isset($siteAudit['warnings']))
				<div class="seo_text col-md-4">
					<div class="seo_text_inner">
						<h6>Warnings</h6>
						<a href="{{route('site-audit-details', $websiteId)}}"><span>{{$siteAudit['warnings']}}</span></a>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
	@endif
	
	@if(isset($domainOverview[$websiteId]))
	@php $overview = $domainOverview[$websiteId]; @endphp
	<div class="row seo_select" >
		<div class="container">
			<h4>Domain Report</h4>			
		</div>
	</div>
	
	<div class="seo_content">
		<div class="container">
			<div class="seo_content_inner">
				@if(isset($overview['organic_keywords']))
				<div class="seo_text seo_fir col-md-4">
					<div class="seo_text_inner">
						<h6>Keywords</h6>
						<a href="{{route('domain-details', $websiteId)}}"><span>{{$overview['organic_keywords']}}</span></a>
					</div>
				</div>
				@endif
				@if(isset($overview['organic_traffic']))
				<div class="seo_text col-md-4">
					<div class="seo_text_inner">
						<h6>Traffic</h6>
						<a href="{{route('domain-details', $websiteId)}}"><span> {{$overview['organic_traffic']}}</span></a>
					</div>
				</div>
				@endif
				@if(isset($overview['organic_cost']))
				<div class="seo_text col-md-4">
					<div class="seo_text_inner">
						<h6>Traffic cost</h6>
						<a href="{{route('domain-details', $websiteId)}}"><span>{{$overview['organic_cost']}}</span></a>
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
	@endif
	<!--end-->
	@if(isset($backlinkreports[$websiteId]))
		@php $backlinkreport = $backlinkreports[$websiteId]; @endphp
		<div class="row seo_select" >
			<div class="container">
				 <h4>Backlink Report</h4>
			</div>
		</div>
			
		<div class="seo_content">
			<div class="container">
				<div class="seo_content_inner">
					@if(isset($backlinkreport['ascore']))
					<div class="seo_text seo_fir col-md-4">
						<div class="seo_text_inner">
							<h6>Ascore</h6>
							<a href="{{route('backlink-details', $websiteId)}}"><span>{{$backlinkreport['ascore']}}</span></a>
						</div>
					</div>
					@endif
					@if(isset($backlinkreport['follows_num']))
					<div class="seo_text col-md-4">
						<div class="seo_text_inner">
							<h6>Follows</h6>
							<a href="{{route('backlink-details', $websiteId)}}"><span> {{$backlinkreport['follows_num']}}</span></a>
						</div>
					</div>
					@endif
					@if(isset($backlinkreport['nofollows_num']))
					<div class="seo_text col-md-4">
						<div class="seo_text_inner">
							<h6>No Follows</h6>
							<a href="{{route('backlink-details', $websiteId)}}"><span>{{$backlinkreport['nofollows_num']}}</span></a>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	@endif
	</div>
	@endif
@endforeach


		
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
		$('.select2').select2();
        $('.search').change(function(){ 
            var websiteId = $(this).val();
            var website = $('.search').select2('data'); 
			 $.ajax({
                url : "{{ route('fetch-seo-details') }}",
                type : "POST",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data : {
                    websiteId : websiteId,
                    website : website[0].text
                },
                success : function (data){ console.log(data);console.log(data.status_code);
					if(data.status_code == 200) {
						$('#myTabContent').html(data.response);
					}               
                },
                error : function (response){

                }
            });
        });
 $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });
    </script>
@endsection