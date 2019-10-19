<script type="text/x-jsrender" id="product-templates-create-block">
<div class="modal fade" id="product-template-create-modal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Product Template</h4>
        </div>
        <div class="modal-body">
          <form>
             <div class="form-group row">
                <label for="tempalte_id" class="col-sm-3 col-form-label">Template ID</label>
                <div class="col-sm-6">
                   <?php echo Form::select("tempalte_id",range(1,30),null,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="product_title" class="col-sm-3 col-form-label">Product Title</label>
                <div class="col-sm-6">
                   <?php echo Form::text("product_title",null,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="brand_id" class="col-sm-3 col-form-label">Brand</label>
                <div class="col-sm-6">
                   <?php echo Form::select("brand_id",\App\Brand::all()->pluck("name","id"),null,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="currency" class="col-sm-3 col-form-label">Currency</label>
                <div class="col-sm-6">
                   <?php echo Form::text("currency",null,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="price" class="col-sm-3 col-form-label">Price</label>
                <div class="col-sm-6">
                   <?php echo Form::text("price",(float)0.00,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="discounted_price" class="col-sm-3 col-form-label">Discounted Price</label>
                <div class="col-sm-6">
                   <?php echo Form::text("discounted_price",(float)0.00,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
              <div class="col-sm-3 imgUp">
                 <div class="imagePreview"></div>
                 <label class="btn btn-primary">
                 Upload<input type="file" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                 </label>
              </div>
              <i class="fa fa-plus imgAdd"></i>
           </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</script>
