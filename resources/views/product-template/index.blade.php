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
</style>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Product Templates</h2>
        <div class="pull-right">
            <button type="button" class="btn btn-secondary create-product-template-btn">Add Product Template</button>
        </div>
    </div>
    <br>
    <div class="row" style="margin:10px;">
		<div class="col-md-12" id="page-view-result">

		</div>
	</div>
</div>
<div id="display-area"></div>
@include("product-template.partials.list-template")
@include("product-template.partials.create-form-template")
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.5/jsrender.min.js"></script>
<script type="text/javascript" src="js/common-helper.js"></script>
<script type="text/javascript" src="js/product-template.js"></script>
<script type="text/javascript">
	productTemplate.init({
		bodyView : $("#product-template-page"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

@endsection