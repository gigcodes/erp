
@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List | Chatbot')

@section('content')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
 -->
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .panel-img-shorts {
            width: 80px;
            height: 80px;
            display: inline-block;
        }
        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }
    </style>
<div id="common-page-layout">

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Chat Message List <span class="count-text">0</span></h2>
        </div>
    </div>

    <div class="row ml-2 mr-2">
        <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
            <div class="pull-left">
                <div class="form-inline">
                    <form class="form-inline message-search-handler" method="get">
                        <div class="row">


                            <div class="form-group mr-2">
	                            <div class="col pr-0">
	                                <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
	                            </div>
	                        </div>    

                            <!-- <div class="form-group">
                    	    	<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
	                                <input type="hidden" name="customrange" id="custom" value="{{ isset($customrange) ? $customrange : '' }}">
	                                <i class="fa fa-calendar"></i>&nbsp;
	                                <span @if(isset($customrange)) style="display:none;" @endif id="date_current_show"></span> <p style="display:contents;" id="date_value_show"> {{ isset($customrange) ? $from .' '.$to : '' }}</p><i class="fa fa-caret-down"></i>
	                            </div>
		                    </div> -->
                            
                            <div class="pull-right">
	                            <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-secondary btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
		                	</div>

                        
                            
                        </div>
                    </form>

                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    
                </div>
            </div>

        </div>
    </div>


    <div class="row ml-2 mr-2">
        <div class="col-md-12">
            <div class="margin-tb" id="page-view-result">
			</div>
        </div>
    </div>
</div>
    
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
  	</div>	
</div>

@include("custom-chat-message.templates.list-template")

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
 -->
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/custom_chat_message.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>


@endsection