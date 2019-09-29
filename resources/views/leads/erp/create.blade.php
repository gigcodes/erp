<form action="<?php echo route("leads.erpLeads.store"); ?>">
  <?php echo csrf_field(); ?>
  <div class="form-group">
    <label for="customer_id">Customer:</label>
    <?php echo Form::select("customer_id", $customerList, null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="product_id">Products:</label>
    <?php echo Form::select("product_id", [] , null,["class"=> "form-control" ,"id" => "select2-product", "style"=>"width:100%;"]);  ?>
  </div>
  <div class="form-group">
    <label for="brand_id">Brand:</label>
    <?php echo Form::select("brand_id", $brands , null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="category_id">Category:</label>
    <?php echo Form::select("category_id", $category , null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="color">Color:</label>
    <?php echo Form::text("color",null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="size">Size:</label>
    <?php echo Form::text("size",null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="min_price">Min price:</label>
    <?php echo Form::text("min_price",null,["class"=> "form-control"]);  ?>
  </div>
  <div class="form-group">
    <label for="max_price">Max price:</label>
    <?php echo Form::text("max_price",null,["class"=> "form-control"]);  ?>
  </div>
  <button type="submit" class="btn btn-default lead-button-submit-form">Submit</button>
</form>