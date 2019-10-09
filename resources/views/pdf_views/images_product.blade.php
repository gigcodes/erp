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
            padding: 5%;
        }

        .row {
            display: inline-block;
            max-width: 90%;
            max-height: 90%;
        }

        .box_0 {
            width: 70%;
            display: block;
            border-radius: 10px;
            margin: 0 auto 5px;
            margin-top: 10px;
        }

        .box_1 {
            width: 70%;
            display: block;
            border-radius: 10px;
            margin: 0 auto 5px;
            margin-top: 10px;
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
    <style type="text/css" media="screen,print">
           /* Page Breaks */

    /***Always insert a page break before the element***/
           .pb_before {
               page-break-before: always !important;
           }

    /***Always insert a page break after the element***/
           .pb_after {
               page-break-after: always !important;
           }

    /***Avoid page break before the element (if possible)***/
           .pb_before_avoid {
               page-break-before: avoid !important;
           }

    /***Avoid page break after the element (if possible)***/
           .pb_after_avoid {
               page-break-after: avoid !important;
           }

    /* Avoid page break inside the element (if possible) */
           .pbi_avoid {
               page-break-inside: avoid !important;
           }

       </style>
</head>
<body>
<div class="main" style="width: 100%;">
    @foreach($medias->chunk(2) as $subMedias)
        @php $key = 0 @endphp
            @foreach($subMedias as $subMedia)
                @php $first = false @endphp
                <div class="row">
                <div class="box_1 pb_after" style="position: relative; width:100%; margin: 0 auto;">
                    <img src="{{ $subMedia->getUrl() }}" alt="Image" style="width: 100%; height: 500px; border: 1px solid #cccccc">
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
                        <div style="position: absolute; bottom:2px; margin-left: 10px;opacity: 0.9;font-size: 23px;">
                            <p><strong>{{ $product->name }}</strong></p>
                            @if($product->brands)
                                <p style="color: #1a60aa"><strong>{{ $product->brands->name }}</strong></p>
                            @endif
                            <p style="color: #6c757d"><strong>Price: </strong> <span style="text-decoration: line-through">Rs. {{ $product->price_inr }}</span> Rs. {{ $product->price_special }}</p>
                            <p style="color: #6c757d"><strong>Code: </strong> {{ $product->sku }}</p>
                            @if($product->lmeasurement && $product->hmeasurement && $product->dmeasurement)
                                <p style="color: #6c757d"><strong>Dimension: </strong> {{ \App\Helpers\ProductHelper::getMeasurements($product) }}</p>
                            @endif
                        </div>
                    @else
                        <div style="margin-top: -40px; margin-left: 10px; position: absolute; text-align: justify">
                            <strong>Details Unavailable</strong>
                        </div>
                    @endif
                </div>
                </div>
                @php $key++ @endphp
            @endforeach
    @endforeach
</div>
</body>
</html>