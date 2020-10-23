@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	.no-mr {
		margin:0px;
	}
	.pd-3 {
		padding:0px;
	}
</style>

<div class="row">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
	@include("partials.flash_messages")
    <div class="col-lg-12 margin-tb">
    	<div class="row">
		    <div class="col col-md-10">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline message-search-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group">
							    <label for="keyword">Keyword:</label>
							    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
						  	</div>
						  	<div class="form-group">
						  		<label for="button">&nbsp;</label>
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
			<div class="col col-md-1">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image add-social-account">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>		
		    </div>
			<div class="col col-md-1">
	    			<button class="btn btn-sm btn-secondary">
		  				<a href="/content-management/contents" style="color:white;">View Contents</a>
		  			</button>		
		    </div>
	    </div>	
		<div>
        <table class="table table-bordered table-striped" style="table-layout:fixed;">
            <tr>
                <th style="width:5%;">Sl no</th>
                <th style="width:20%;">Site name</th>
                <th style="width:35%;">Facebook</th>
                <th style="width:35%;">Instagram</th>
                <th style="width:5%;">Action</th>
            </tr>
            @foreach($websites as $key => $website)
            <tr>
                <td>{{++$key}} </td>
                <td>{{$website->title}} </td>
                <td>
				@if($website->facebookAccount)
					<p class="no-mr" style="word-break: break-all;">User : {{$website->facebookAccount->username}} | Pass : {{ str_limit($website->facebookAccount->password, $limit = 10, $end = '...') }}</p>
				@endif
				</td>
                <td>
				@if($website->instagramAccount)
					<p class="no-mr" style="word-break: break-all;">User : {{$website->instagramAccount->username}} | Pass : {{ str_limit($website->instagramAccount->password, $limit = 10, $end = '...') }}</p>
				@endif
				</td>
                <td>
				<button type="button" class="btn pd-3">
			        <a href="/content-management/manage/{{$website->id}}">
			        	<img width="15px" title="Manage Contents" src="/images/project.png">
			        </a>
				</button> 
				<button type="button" class="btn preview-img-btn pd-3" data-id="{{$website->id}}">
					<i class="fa fa-eye" aria-hidden="true"></i>
				</button>
				</td>
            </tr>
            @endforeach
        
            </table>
    </div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>

<div id="accountCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" id="add-social-account-content">

        </div>

      </div>
</div>


<div id="preview-website-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        	<div class="modal-body">
    			<div class="col-md-12">
	        		<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Sl no</th>
					        <th>Image</th>
					      </tr>
					    </thead>
					    <tbody class="website-image-list-view">
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
<script type="text/javascript">
	    $(document).on('click', '.add-social-account', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{route('content-management.social.create')}}",
                type: 'GET',
                success: function (response) {
					$("#accountCreateModal").modal("show");
					$("#add-social-account-content").html(response);
                },
                error: function () {
                }
            });
        });


		$(document).on('click', '.preview-img-btn', function (e) {
            e.preventDefault();
			id = $(this).data('id');
            $.ajax({
                url: "/content-management/preview-img/"+id,
                type: 'GET',
                success: function (response) {
					$("#preview-website-image").modal("show");
					$(".website-image-list-view").html(response);
                },
                error: function () {
                }
            });
        });
</script>

@endsection

