<!DOCTYPE html>
<html>
    <head>
        <meta content="width=device-width, initial-scale=1" name="viewport">
            <style>
                 .container {
                    text-align: center;
                    color: white;
                    padding-top: 20px;
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
                    top: 8px;
                    right: 16px;
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

                  .row > .column {
                    padding: 5px 5px 5px 5px;
                    position: relative;
                    page-break-after: always;
                    padding-top: 10px;
                  }

                  .row:after {
                    content: "";
                    display: table;
                    clear: both;
                  }

                  .column {
                    float: left;
                  }
                 
                  .thumbnail {
                    width: auto; /* Set a small width */
                  }
            </style>
        </meta>
    </head>
    <body>
        <div class="container">
            @php $key = 0 @endphp
            @foreach($medias->chunk(2) as $subMedias)
                    @foreach($subMedias as $subMedia)
                <div class="row">
                    <?php
                        $mediable = DB::table('mediables')->where('media_id', $subMedia->id)->where('mediable_type', 'App\Product')->first();
                        if ($mediable) {
                            $product_id = $mediable->mediable_id;
                            $product = App\Product::find($product_id);
                        } else {
                            $product = null;
                        }
                    ?>
                      @if($product)
                            <?php
                                $textToSend = [];
                                $textToSend[] = $product->name." ";
                                if($product->brands) {
                                    $textToSend[] = $product->brands->name;
                                }
                                if($product->lmeasurement && $product->hmeasurement && $product->dmeasurement) {
                                    $textToSend[] = "Dimension: ".\App\Helpers\ProductHelper::getMeasurements($product)."";
                                }
                                $textToSend[] = "Price: Rs. ".$product->price_special."<br>";
                             ?>
                            <div class="column">
                                <img class="hover-shadow cursor thumbnail" src="<?php echo $subMedia->getAbsolutePath(); ?>" >
                                </img>
                                <div class="top-left"><?php echo implode("<br>",$textToSend); ?></div>
                            </div>
                        @php $key++ @endphp
                      @endif
                    </div>
                    @endforeach
              <div style="clear:both"></div>
            @endforeach  
        </div>
    </body>
</html>