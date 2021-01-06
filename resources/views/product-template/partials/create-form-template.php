<script type="text/x-jsrender" id="product-templates-create-block">
<div class="modal fade" id="product-template-create-modal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Product Template</h4>
        </div>
        <div class="modal-body">
          <form method="post" enctype="multipart/form-data" id="product-template-from">
             <?php echo csrf_field(); ?>
             <div class="form-group row">
                <label for="template_no" class="col-sm-3 col-form-label">Template No</label>
                <div class="col-sm-6">
                    <select class="form-control template_no valid" name="template_no" aria-invalid="false">
                        <?php 
                            foreach ($templateArr as $template) {
                                $media = $template->lastMedia(config('constants.media_tags'));
                                echo '<option value="'.$template->id.'" data-image="'.(($media) ? $media->getUrl() : "").'" data-no-of-images="'.$template->no_of_images.'">'.$template->name.'</option>';
                            }
                       ?>
                    </select>
                </div>
                <div class="col-sm-3">
                  <div class="image_template_no" style="position: absolute; width: 85%;">
                  </div>
                </div>
             </div>
             <div class="form-group row">
                <label for="product_id" class="col-sm-3 col-form-label">Product</label>
                <div class="col-sm-6">
                    <div style="width: 94%; float: left;" class="div-select-product">
                        
                        <select class="orm-control ddl-select-product" name="product_id[]" aria-invalid="false" multiple>
                        <?php 
                            if ($productArr) {
                              foreach ($productArr as $product) {
                                  echo '<option value="'.$product->id.'" data-brand="'.$product->brand.'" data-product-title="'.$product->name.'" selected>'.$product->name.'</option>';
                              }
                            }
                       ?>
                    </select>
                    </div>
                    <div style="width: 6%; float: right;">
                        <a href="<?php echo route('attachImages', 'product-templates');?>" class="btn btn-image px-1 images-attach"><img src="/images/attach.png"></a>
                    </div>
                </div>
             </div>
             <div class="form-group row">
                <label for="product_title" class="col-sm-3 col-form-label">Product Title</label>
                <div class="col-sm-6">
                   <?php echo Form::text("product_title",null,["class" => "form-control product_title"]); ?>
                </div>
             </div>

             <div class="form-group row">
                <label for="text" class="col-sm-3 col-form-label">Text</label>
                <div class="col-sm-6">
                   <?php echo Form::select("text",["" => ""] + $texts , null ,["class" => "form-control text select2"]); ?>
                </div>
             </div>

             <div class="form-group row">
                <label for="font_style" class="col-sm-3 col-form-label">Font Style</label>
                <div class="col-sm-6">
                   <?php echo Form::text("font_style",null ,["class" => "form-control"]); ?>
                </div>
             </div>

             <div class="form-group row">
                <label for="font_size" class="col-sm-3 col-form-label">Font Size</label>
                <div class="col-sm-6">
                   <?php echo Form::text("font_size",null ,["class" => "form-control"]); ?>
                </div>
             </div>

             <div class="form-group row">
                <label for="background_color" class="col-sm-3 col-form-label">Background color</label>
                <div class="col-sm-6">
                   <?php echo Form::select("background_color[]",$backgroundColors,null ,["class" => "form-control select2","multiple" => "true"]); ?>
                </div>
             </div>

             <div class="form-group row">
                <label for="brand_id" class="col-sm-3 col-form-label">Brand</label>
                <div class="col-sm-6">
                   <?php echo Form::select("brand_id",["" => "-Select-"] + \App\Brand::all()->pluck("name","id")->toArray(),null,["class" => "form-control"]); ?>
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
                <label for="discounted_price" class="col-sm-3 col-form-label">Discounted Price</label>
                <div class="col-sm-6">
                   <?php echo Form::text("discounted_price",(float)0.00,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row">
                <label for="store_website_id" class="col-sm-3 col-form-label">Store Website</label>
                <div class="col-sm-6">
                   <?php echo Form::select("store_website_id",\App\StoreWebsite::pluck('title','id')->toArraY(),null,["class" => "form-control"]); ?>
                </div>
             </div>
             <div class="form-group row show-product-image"> </div>
             <div class="form-group row">
              <div class="col-sm-3 imgUp">
                 <div class="imagePreview"></div>
                 <label class="btn btn-primary">
                 Upload<input type="file" name="files[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                 </label>
              </div>
              <i class="fa fa-plus imgAdd"></i>
           </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary create-product-template">Create Template</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</script>
