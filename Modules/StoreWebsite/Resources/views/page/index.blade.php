@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }
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
                    <button class="btn custom-button ml-2 push-by-store-website"  data-toggle="modal" data-target="#push-by-store-website-modal" style="width:133px;">Push Storewebsite</button> 
                    <button class="btn custom-button ml-2 pull-by-store-website"  data-toggle="modal" data-target="#pull-by-store-website-modal" style="width:133px;">Pull Storewebsite</button>
					<button type="button" title="Pull logs" data-id="" class="btn ml-2 custom-button btn-pullLogs" style="width:133px;">Pull Logs</button>
                    
                    <button class="btn custom-button ml-2 " data-toggle="modal" data-target="#newStatusColor"> Status Color Info</button>
                    
            
                        <form class="form-inline message-search-handler" method="get">
                            <div class="ml-2">
                                <div class="form-group">
                                    <?php echo Form::select("language",$languagesList,request("language"),["class"=> "form-control","placeholder" => "Select Language"]) ?>
                                </div>
                            </div>     
                            <div class="ml-2">
                                <div class="form-group">
                                    <?php echo Form::select("store_website_id",$storeWebsites,request("store_website_id"),["class"=> "form-control selectbox","placeholder" => "Select Store website"]) ?>
                                </div>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <select name="is_pushed" class="form-control">
                                        <option value="">Is Pushed</option>
                                        <option value="0">False</option>
                                        <option value="1">True</option>
                                   </select>    
                                </div>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="button">&nbsp;</label>
                                    <button type="submit" style="display: inline-block;width: 10%; margin-top: -22px;" class="btn btn-sm btn-image btn-search-action">
                                        <img src="/images/search.png" style="cursor: default;">
                                    </button>
									
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
        <div class="col-md-12 margin-tb" id="page-view-result">

        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
</div>
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Page Status Color Info</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            
            <div class="form-group col-md-12">
                <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                    <tr>
                        <td class="text-center"><b>Status Name</b></td>
                        <td class="text-center"><b>Color Code</b></td>
                        <td class="text-center"><b>Color</b></td>
                    </tr>
                    <tr>
                        <td>Is Pending Review Translations</td>
                        <td class="text-center">#f21818</td>
                        <td class="text-center"><span style="padding: 10px;background: #f21818;width: 50px;height: 10px;display: inherit;"></span></td>
                    </tr>
                    <tr>
                        <td>Is Pushed</td>
                        <td class="text-center">#18f23f</td>
                        <td class="text-center"><span style="padding: 10px;background: #18f23f;width: 50px;height: 10px;display: inherit;"></span></td>
                    </tr>
                    <tr>
                        <td>Is Pending Pushed</td>
                        <td class="text-center">#ffeb3b</td>
                        <td class="text-center"><span style="padding: 10px;background: #ffeb3b;width: 50px;height: 10px;display: inherit;"></span></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            
        </div>

    </div>
</div>
<div class="common-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    </div>  
</div>

<div class="preview-history-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th >Id</th>
                                <th>Content</th>
								<th>URL</th>
								<th>Result</th>
								<th>Result Type</th>
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

<div class="page-logs-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Store Website</th>
								<th>Content</th>
								<th>URL</th>
								<th>Result Type</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="page-logs-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="preview-activities-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Description</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-activities-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="push-by-store-website-modal modal" id="push-by-store-website-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="form-row col-md-12">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            <?php echo Form::select("store_website_id",$storeWebsites,null, ["class" => "form-control push-website-store-id"]);  ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary push-pages-store-wise">Push Store(s)</button>
            </div>
        </div>
    </div>
</div>

<div class="pull-by-store-website-modal modal" id="pull-by-store-website-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="form-row col-md-12">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            <?php echo Form::select("store_website_id",$storeWebsites,null, ["class" => "form-control pull-website-store-id"]);  ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary pull-pages-store-wise">Pull Store(s)</button>
            </div>
        </div>
    </div>
</div>

@include("storewebsite::page.templates.list-template")
@include("storewebsite::page.templates.create-website-template")
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website-page.js') }}"></script>
<script type="text/javascript">
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });
    function save_platform_id(page_id) {
        var platform_id = $(this.event.target).val();
        $.ajax({
            url: '{{ route('store_website_page.store_platform_id') }}',
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                'page_id': page_id,
                'platform_id': platform_id
            }
        });
    }
	
	function openUrl(url) {
		if (url && !url.match(/^http([s]?):\/\/.*/)) {
			var urlToOpen = 'http://'+url;
		} else {
			var urlToOpen = url;
		}
		window.open(urlToOpen, '_blank');
	}
	
</script>
@endsection 