<?php foreach($items as $date => $raw){ ?>
  <h4><?php echo $date; ?></h4>
    <div class="row">
    <?php 
      $rowCount = 0;
    ?>  
    <?php foreach($raw as $product) { ?>
      <?php 
        /*if($rowCount % 4 == 0) { echo '<div class="row">'; } 
           $rowCount++;  */
      ?>
      <div class="col-md-3 col-xs-6 text-left mb-5">
        <a href="{{ route( 'products.show', $product->id ) }}">
          <img style="object-fit: cover;" src="{{ $product->getMedia(config('constants.attach_image_tag'))->first() ? $product->getMedia(config('constants.attach_image_tag'))->first()->getUrl()
              : '' }}" class="img-responsive grid-image" alt="...">
          <div class="card-body">
            <p class="card-text">SKU : {{ $product->sku }}</p>
            <p class="card-text">Id : {{ $product->id }}</p>
            <p class="card-text">Size : {{ $product->size }}</p>
            <p class="card-text">Price : {{ $product->price }}</p>
            <p class="card-text">Status : {{ (new \App\Stage)->getNameById( $product->stage ) }}</p>
            <p class="card-text">Ref. Category : {{ $product->reference_category }}</p>
            <p class="card-text">Category : <?php echo Form::select("category",$categoryArray,$product->category,[
                "class" => "form-control update-product select-multiple2" , 
                "id" => "category_".$product->id,
                "data-id" => $product->id
              ]); ?></p>
            <p class="card-text">Ref. Color : {{ $product->reference_color }}</p>
            <p class="card-text">Color : <?php echo Form::select("color",$sampleColors,$product->color,[
                "class" => "form-control update-product select-multiple2" , 
                "id" => "id_".$product->id,
                "data-id" => $product->id
              ]); ?></p>
            <p class="card-text">Supplier : {{ $product->supplier }}</p>
            <p class="card-text">Suppliers : {{ $product->supplier_list }}</p>
            <p class="card-text"><input type="checkbox" class="select-product-edit" name="product_id" data-id="{{ $product->id }}"></p>
          </div>
        </a>
        </div>
      <?php //if($rowCount % 4 == 0) { echo '</div>'; } ?>
    <?php } ?>
    </div>
<?php } ?>