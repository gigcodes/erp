<html>
    <head>
            <style>
                .container {
                    color: white;
                    padding-top: 20px;
                    page-break-after: always;
                    border: 10px;
                    color: grey;
                    background-color: grey; 
                    padding: 20px;
                    margin: auto;
                    margin-bottom: 10px;
                    width: 1000px;
                  }

                  .row {
                    text-align: center;
                    color: white;
                    color : #6c757d;
                  }

                  .bottom-left {
                    position: absolute;
                    bottom: 8px;
                    left: 16px;
                  }

                  .top-left {
                    position: absolute;
                    top: 20px;
                    left: 16px;
                    color : #6c757d;
                  }

                  .top-right {
                    position: absolute;
                    top: 70px;
                    right: -45px;
                    -ms-transform: rotate(270deg);
                    -webkit-transform: rotate(270deg);
                    transform: rotate(270deg);
                  }

                  .bottom-right {
                    position: absolute;
                    bottom: 25px;
                    right: 16px;
                  }

                  .centered {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                  }

                  * {
                    box-sizing: border-box;
                  }

                  .column {
                    float: none;
                    position: relative;
                    display: inline-block;
                  }
                 
                  .thumbnail {
                    width: 100%; /* Set a small width */
                    max-height: 800px;
                  }
            </style>
        </meta>
    </head>
    <body>
            @foreach($medias->chunk(2) as $subMedias)
              @foreach($subMedias as $subMedia)
                <div class="container">
                      <?php
                              $mediable = DB::table('mediables')->where('media_id', $subMedia->id)->where('mediable_type', 'App\Product')->first();
                              if ($mediable) {
                                  $product_id = $mediable->mediable_id;
                                  $product = App\Product::find($product_id);
                              } else {
                                  $product = null;
                              }
                         
                           if($product) {
                              $textToSend = [];
                              $textToSend[] = $product->name." ";
                              if($product->brands) {
                                  $textToSend[] = $product->brands->name;
                              }
                              if($product->lmeasurement && $product->hmeasurement && $product->dmeasurement) {
                                  $textToSend[] = "Dimension: ".\App\Helpers\ProductHelper::getMeasurements($product)."";
                              }
                              $textToSend[] = "Price: Rs. ".$product->price_special; ?>
                            <div class="column">
                                <img class="hover-shadow cursor thumbnail" src="<?php echo $subMedia->getAbsolutePath(); ?>">
                                </img>
                                <div class="top-left">
                                    <?php echo implode("<br>
                                    ",$textToSend); ?>
                                </div>
                                <div class="top-right">
                                    <?php echo  DNS1D::getBarcodeHTML($product->sku, "CODE11",1,30,"black", true); ?>
                                </div>
                                
                            </div>
                          <?php } ?>
                </div>
              @endforeach
         @endforeach
    </body>
</html>