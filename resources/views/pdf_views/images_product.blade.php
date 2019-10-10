<html>
<head>
    <meta name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Images</title>
    <style>
        body {
            background: #F4E7DF;
        }

        * {
            padding: 0;
            margin: 0
        }

        .main {
            text-align: center;
            padding: 10%;
        }

        .row {
            display: inline-block;
            max-width: 90%;
            max-height: 90%;
        }

        .box_0 {
            width: 90%;
            display: inline-block;
            border-radius: 10px;
            margin: 0 auto 5px;
            margin-top: 10px;
        }

        .box_1 {
            width: 100%;
            display: inline-block;
            border-radius: 10px;
            margin: 10px;
        }

        img {
            margin-top: 100px;
            max-width: 800px;
            max-height: 800px;
        }
        .page-break {
            overflow: hidden;
            page-break-after: always;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
<div class="main" style="width: 100%;">
      <div class="row">
            @foreach($medias->chunk(2) as $subMedias)
                @php $key = 0 @endphp
                    @foreach($subMedias as $subMedia)
                        @php $first = false @endphp
                            <div class="box_1 page-break" style="width:100%;">
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
                                    $textToSend = $product->name."\n";
                                    if($product->brands) {
                                        $textToSend .= $product->brands->name."\n";
                                    }
                                    $textToSend .= "Price: Rs. ".$product->price_special."\n";
                                    $textToSend .= "Code: ".$product->sku."\n";
                                    if($product->lmeasurement && $product->hmeasurement && $product->dmeasurement) {
                                        $textToSend .= "Dimension: ".\App\Helpers\ProductHelper::getMeasurements($product)."";
                                    }
                                 ?>
                                <img src="<?php echo createProductTextImage(
                                    $subMedia->getAbsolutePath(),
                                    $folder, 
                                    $textToSend, 
                                    $color = "545b62", 
                                    $fontSize = "20" , 
                                    $needAbs = true
                                ); ?>" alt="Image" style="width: 100%; height: 500px; border: 1px solid #cccccc">
                                @endif
                            </div>
                        @php $key++ @endphp
                    @endforeach
            @endforeach
       </div>
    </div>
</body>
</html>