<html>
<head>
    <title>Images</title>
    <style>
        body {
            background: #eeeeee;
        }
        * {
            padding: 0;
            margin: 0
        }

        div.multi_column {
            width: 100%;
            margin: 0px auto;
            -webkit-column-count: 3;
            -webkit-column-rule: 2px solid #000;
            -moz-column-count: 3;
            -moz-column-rule: 2px solid #000;
            column-count: 3;
            column-rule: 2px solid #000;
        }
        div#multi_column > div {
            margin-bottom: 20px;
            padding:16px;
            border: #000 1px solid;
        }
        div#multi_column > div:nth-child(2n+0) {
            background: #CAE4FF;
        }
        div#multi_column > div:nth-child(2n+1) {
            background: #A4D1FF;
        }
    </style>
</head>
<body>
<div class="main">
    @foreach($medias->chunk(2) as $subMedias)
        @php $key = 0 @endphp
        <div class="multi_column">
            @foreach($subMedias as $subMedia)
                <div class="box_{{$key}}">
                    <img src="{{ $subMedia->getAbsolutePath() }}" alt="Image" style="width: 100%; border-bottom: 10px solid #cccccc">
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
                        <div style="padding: 10px; text-align: justify">
                            <p><strong>{{ $product->name }}</strong></p>
                            @if($product->brands)
                                <p style="color: #1a60aa"><strong>{{ $product->brands->name }}</strong></p>
                            @endif
                            <p><strong>Code: </strong> {{ $product->sku }}</p>
                            @if($product->lmeasurement)
                                <p><strong>Dimension: </strong> {{ $product->lmeasurement }} x {{ $product->heasurement }} x {{ $product->dmeasurement }}</p>
                            @endif
                        </div>
                    @else
                        <strong>Details Unavailable</strong>
                    @endif
                </div>
                @php $key++ @endphp
            @endforeach
        </div>
    @endforeach
</div>
</body>
</html>