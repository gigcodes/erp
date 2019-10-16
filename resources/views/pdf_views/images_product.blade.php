<html>

<head></head>
<style>
    body {
        /* background-color: #f5e9df; */
    }

    .page {
        margin: 0 auto;
        width: 1005px;
        height: 1015px;
        text-align: center;
    }

    img.product {
        margin: 50px;
        width: auto;
        height: 915px;
        border-bottom: 20px solid #eee;
    }

    .top-left {
        position: absolute;
        top: 75px;
        left: 200px;
        font-size: 36px;
        z-index: 2;
        text-align: left;
    }
</style>
<body>
@foreach($medias->chunk(2) as $subMedias)
    @foreach($subMedias as $subMedia)
        <div class="page">
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
            $textToSend[] = $product->name . " ";
            if ($product->brands) {
                $textToSend[] = $product->brands->name;
            }
            if ($product->lmeasurement && $product->hmeasurement && $product->dmeasurement) {
                $textToSend[] = "Dimension: " . \App\Helpers\ProductHelper::getMeasurements($product) . "";
            }
            $textToSend[] = "Price: Rs. " . $product->price_special; ?>

            <img class="product" src="<?php echo $subMedia->getAbsolutePath(); ?>">
            </img>
            <div class="top-left">
                <?php echo implode("<br>
                                    ", $textToSend); ?>
            </div>
            <div class="top-right">
                <?php 
                 $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();
                 echo $generator->getBarcode('081231723897', $generator::TYPE_CODE_11);   
                // echo DNS1D::getBarcodeHTML($product->sku, "CODE11",1,30,"black", true); ?>
            </div>

            <?php } ?>
        </div>
    @endforeach
@endforeach
</body>
</html>