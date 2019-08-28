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

        .main {
            text-align: center;
            padding: 5%;
        }

        .row {
            display: block;
        }

        .box_0 {
            width: 45%;
            display: block;
            border-radius: 10px;
            margin: 0 auto 5px;
        }

        .box_1 {
            width: 45%;
            display: block;
            border-radius: 10px;
            margin: 0 auto 5px;
        }
    </style>
</head>
<body>
<div class="main">
    @foreach($medias->chunk(2) as $subMedias)
        @php $key = 0 @endphp
        <div class="row">
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