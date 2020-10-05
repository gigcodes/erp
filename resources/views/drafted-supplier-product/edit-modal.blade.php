@if($product)
    <form action="" method="POST" enctype="multipart/form-data" id="formDraftedProduct" data-id="{{ ($product) ? $product->id  : '' }}">
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <?php echo Form::text("name",$product->name,["class" => "form-control-sm form-control",'id'=>'name']); ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Brand:</strong>
                    <?php echo Form::select("brand_id",\App\Helpers::selectBrandList(),$product->brand,["class" => "form-control-sm form-control select2",'style' => 'width:200px']); ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Category:</strong>
                    <?php echo \App\Helpers::selectCategoryList($product->category); ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Short description:</strong>
                    <input type="text" name="short_description" value="{{ ($product) ? $product->short_description : '' }}" class="form-control" placeholder="Short description">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Price:</strong>
                    <input type="text" name="price " value="{{ ($product) ? $product->price : '' }}" class="form-control" placeholder="price">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    <?php echo Form::select("status_id",\App\Helpers::selectStatusList(),$product->status_id,["class" => "form-control-sm form-control select2",'style' => 'width:200px']); ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Quick product:</strong>
                    <?php echo Form::select("quick_product",[0 => "No", 1 => "Yes"],$product->quick_product,["class" => "form-control-sm form-control select2",'style' => 'width:200px']); ?>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endif
<style>
    .edit-drafted {
        background-color: white;
        padding:20px;
        max-width:500px;
        margin:1.75rem auto
    }
</style>