@extends('layouts.app')

@section("styles")
@endsection
<style type="text/css">
 .card {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
  max-width: 300px;
  margin: auto;
  text-align: center;
  font-family: arial;
}

.price {
  color: grey;
  font-size: 22px;
}

.card button {
  border: none;
  outline: 0;
  padding: 12px;
  color: white;
  background-color: #000;
  text-align: center;
  cursor: pointer;
  width: 100%;
  font-size: 18px;
}

.card button:hover {
  opacity: 0.7;
}
</style>
@section('content')
  @include('partials.flash_messages')
 <div class="row" style="padding-top: 10px;">
  <?php if(!empty($product)) { ?>
   <div class="col-md-12">
      <div class="card col-lg-6" style="margin:auto;float:none;">
        <h1><?php echo "#".$product->id . " " .$product->name ?></h1>
        <p class="price">SKU : <?php echo $product->sku ?></p>
        <p class="price">Brand : <?php echo ($product->brand) ? $product->brand->name : ""; ?></p>
        <p class="price">Description : <?php echo $product->short_description ?></p>
        <?php $brand = ($product->brand) ? $product->brand: ""; ?>
        <p><button data-keyword="<?php echo implode(",",array_filter([$brand,$product->name,"fashion"])); ?>" class="get-images">Get Images</button></p>
      </div>
   </div>
   <form method="post" id="save-images" action="{{ route('google.search.product-save') }}">
     {{ csrf_field() }}
     <input type="hidden" name="product_id" value="<?php echo $product->id; ?>"> 
     <div class="col-md-12 image-result-show">
        
      </div>
   </form> 
    <div class="col-md-12" style="text-align:right;">
      <button class="attach-and-continue btn btn-secondary">Attach And Continue</button>
      <button class="skip-product btn btn-secondary">Skip Product</button>
    </div>
 </div>
 <?php } else { ?>
       <?php echo "No products found"; ?>
 <?php } ?>

@endsection

@section('scripts')
 
 <script type="text/javascript">
    
    var productSearch = $(".get-images");
        productSearch.on("click",function() {
          var keyword = $(this).data("keyword");
          $.ajax({url: "{!! env('GOOGLE_CUSTOM_SEARCH') !!}&q="+keyword+"&searchType=image&imgSize=large", success: function(result){
              console.log(result.items.length);
              console.log(result.items);
              console.log(result);
              if(typeof result != "undefined" && result.items.length > 0) {
                $.each(result.items,function(k,v) {

                  var template = '<div class="col-md-3"><div class="card" style="width: 18rem;">';
                      template += '<img title="'+v.title+'" class="card-img-top" src="'+v.link+'" alt="'+v.title+'">';
                      template += '<div class="card-body">';
                      template += '<input type="checkbox" class="selected-image" name="images[]" value="'+v.link+'">';
                      template += '</div>';
                      template += '</div></div>';

                      $(".image-result-show").append(template);

                });
              }
          }});
        });

        $(".attach-and-continue").on("click",function() {
           var selectedImages = $(".selected-image:checked").length;
               if(selectedImages > 0) {
                  $("#save-images").submit();
               }else{
                  alert("Please Select Images from list and then proceed");
               } 
        });

        $(".skip-product").on("click",function() {
            $("#save-images").submit();
        });  
        

 </script> 
  

@endsection
