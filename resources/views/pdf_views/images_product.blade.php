<html>

<head></head>
<style>
    body {
        /* background-color: #f5e9df; */
    }

    .page {
        margin: 0 auto;
        width: 1200px;
        height: 1200px;
        text-align: center;
    }

    img.product {
        margin-top: 50px;
        width: auto;
        max-width : 1200px;
        height: 100%;
        max-height: 1000px;
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
        <?php if (!file_exists ($subMedia->getAbsolutePath()) ) {
            continue;
        } ?>
        <?php 
            $img = Image::make($subMedia->getAbsolutePath());
            $height = $img->height();
            $width = $img->width();
            $path = $subMedia->getAbsolutePath();
            if ($height > 1000 || $width > 1000) {
                $img->resize(1000, 1000); 
                if(!is_dir(public_path() . '/tmp_images')) {
                    mkdir(public_path() . '/tmp_images', 0777, true);
                }                  
                $path = public_path() . '/tmp_images/'.$subMedia->getBasenameAttribute();
                $img->save($path);
            }
        ?>
        <?php if (!file_exists ($path) ) {
            continue;
        } ?>
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

            <img style="padding-top: 120px;" class="product" src="<?php echo $path; ?>" />
            <div class="top-left">
                <?php echo implode("<br>", $textToSend); ?>
            </div>
            <div class="top-right">
                <?php 
                 $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();
                 
                 echo $generatorHTML->getBarcode($product->id, $generatorHTML::TYPE_CODE_11);
                 ?>
            </div>

            <?php } ?>
        </div>
    @endforeach
@endforeach
</body>
</html>