@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
    
    .keyword-list {
        cursor: pointer;
        
    }
     .height-fix {
        height: 220px;
        /* display: inline-block; */
        overflow: auto;
        
    }
    textarea {
        overflow: hidden;
    }
</style>
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}}<span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col col-md-12 d-flex">
             
				<button style="display: inline-block;" class="btn ml-2 btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
					<img src="/images/add.png" style="cursor: default;">
				</button>
				<form class="form-inline message-search-handler" method="get">
					   
					<div class="ml-2">
						<div class="form-group">
							<?php echo Form::select("store_websites[]",$storeWebsites,request("store_websites"),["class"=> "form-control select2-ele",'multiple'=>true]) ?>
						</div>
					</div>
					<div class="ml-2">
						<div class="form-group">
							<?php echo Form::select("paths",$paths,request("paths"),["class"=> "form-control select2-ele","placeholder" => "Select Environment path"]) ?>
						</div>
					</div>
					<div class="ml-2">
						<div class="form-group">
							<label for="button">&nbsp;</label>
							<button type="submit" style="display: inline-block;width: 10%; margin-top: -22px;" class="btn btn-sm btn-image btn-search-action1">
								<img src="/images/search.png">
							</button>
							
						</div>
						<div class="form-group">
							<a href="{{route('store-website.environment.matrix')}}" style="; margin-top:0px;" class="btn btn-sm btn-image"><img src="/images/resend2.png"></a>
							
						</div>
					</div>
				</form>
			</div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="alert-msg" style="display: none;">
                    <p></p>
                </div>
            </div>
        </div>
        <div class="col-md-12 margin-tb" >
            <div class="row table-horizontal-scroll mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Env Path</th>
                            @foreach($env_store_websites as $id => $title)
                                <th class="expand-row">
                                    <span class="td-mini-container">
                                        {{ strlen($title) > 10 ? substr($title, 0, 10).'...' :  $title }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$title}}
                                    </span>

                                </td>
                            @endforeach
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($env_paths as $id => $paths)
                            <tr>
                                <td class="expand-row">
                                    <span class="td-mini-container">
                                        {{ strlen($paths) > 15 ? substr($paths, 0, 15).'...' :  $paths }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$paths}}
                                    </span>
                                </td>
                                @foreach($env_store_websites as $id => $title)
                                    <td class="expand-row">
                                        <?php 
                                            $key = array_search($paths, array_column($environments[$id], 'path'));
                                        ?>
                                        @if($key !== false)
                                            <span class="td-mini-container">
                                                {{ strlen($environments[$id][$key]['value']) > 15 ? substr($environments[$id][$key]['value'], 0, 15).'...' :  $environments[$id][$key]['value'] }}
                                            </span>
                                            <span class="td-full-container hidden">
                                                {{$environments[$id][$key]['value']}}
                                            </span>
                                            
                                            <br>
                                            <button type="button" title="Edit" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-edit-template" style="padding: 0px 5px !important;">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" title="Update Value" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-update-value" style="padding: 0px 5px !important;">
                                                <i class="fa fa-upload" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" title="History" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-history" style="padding: 0px 5px !important;">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    
                                    </td>
                                @endforeach
                                
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                
            </div>
        </div>
        
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    </div>  
</div>
<div class="preview-history-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 100%;max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Environment History</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Path</th>
								<th>Value</th>
								<th>Command</th>
								<th>Job Id</th>
								<th>Status</th>
								<th>Response</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-history-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include("storewebsite::environment.templates.list-template")
@include("storewebsite::environment.templates.create-website-template")
@include("storewebsite::environment.templates.change-value-template")
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website-environment.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".select2-ele").select2();
	});
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection 