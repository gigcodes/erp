@extends('layouts.app')
@section('content')
<style type="text/css">
	.imagePreview {
	    width: 100%;
	    height: 180px;
	    background-position: center center;
	  background:url(http://cliquecities.com/assets/no-image-e3699ae23f866f6cbdf8ba2443ee5c4e.jpg);
	  background-color:#fff;
	    background-size: cover;
	  background-repeat:no-repeat;
	    display: inline-block;
	  box-shadow:0px -3px 6px 2px rgba(0,0,0,0.2);
	}
	.btn-primary
	{
	  display:block;
	  border-radius:0px;
	  box-shadow:0px 4px 6px 2px rgba(0,0,0,0.2);
	  margin-top:-5px;
	}
	.imgUp
	{
	  margin-bottom:15px;
	}
	.del
	{
	  position:absolute;
	  top:0px;
	  right:15px;
	  width:30px;
	  height:30px;
	  text-align:center;
	  line-height:30px;
	  background-color:rgba(255,255,255,0.6);
	  cursor:pointer;
	}
	.imgAdd
	{
	  width:30px;
	  height:30px;
	  border-radius:50%;
	  background-color:#4bd7ef;
	  color:#fff;
	  box-shadow:0px 0px 2px 1px rgba(0,0,0,0.2);
	  text-align:center;
	  line-height:30px;
	  margin-top:0px;
	  cursor:pointer;
	  font-size:15px;
	}
	.error {
		color: #FF0000;
	}
</style>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Templates</h2>
        <div class="pull-right">
            <button type="button" class="btn btn-secondary create-product-template-btn">+ Add Template</button>
        </div>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
		<div class="col-md-12" id="page-view-result">

		</div>
	</div>
</div>
<div id="display-area"></div>
@include("template.partials.list-template")
@include("template.partials.create-form-template")
@include("partials.modals.large-image-modal")
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.5/jsrender.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="js/common-helper.js"></script>
<script type="text/javascript" src="js/template.js"></script>
<script type="text/javascript">
	template.init({
		bodyView : $("#product-template-page"),
		baseUrl : "<?php echo url("/"); ?>"
	});

	function bigImg(img){
        $('#large-image').attr('src',img);
        $('#imageExpand').modal('show');
    }

    function normalImg(){
        $('#imageExpand').modal('hide');
    }
</script>

@endsection