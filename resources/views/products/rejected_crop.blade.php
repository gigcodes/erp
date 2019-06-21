@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="page-heading">
                Rejected Cropped Image
            </h5>
        </div>
        <form action="{{action('ProductCropperController@approveRejectedCropped', $product->id)}}" method="post">
            @csrf
            <div class="col-md-12 text-center">
                <button href="{{ action('ProductCropperController@approveRejectedCropped', $product->id) }}" type="button" class="btn btn-default">Approve</button>
                @if($secondProduct)
                    <a href="{{ action('ProductCropperController@showRejectedImageToBeverified', $secondProduct->id) }}">Next Image</a>
                @endif
            </div>
            <div class="col-md-12">
                <div class="text-center">
                    <h4>{{ $product->title }}</h4>
                    <p>
                        {{ $product->sku }}
                        <a href="{{ action('ProductCropperController@downloadImagesForProducts', [$product->id, 'cropped']) }}">Download Cropped</a> &nbsp;
                        <a href="{{ action('ProductCropperController@downloadImagesForProducts', [$product->id, 'original']) }}">Download Original</a>
                    </p>
                </div>
                <div style="width: 650px; margin: 0 auto;" class="fotorama" data-nav="thumbs" data-allowfullscreen="true">
                    @foreach($product->media()->get() as $image)
                        <a href="{{ $image->getUrl() }}"><img src="{{ $image->getUrl() }}"></a>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <!-- Fotorama from CDNJS, 19 KB -->
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

    @if (Session::has('mesage'))
        <script>
            Swal.fire(
                'Success',
                '{{Session::get('message')}}',
                'success'
            )
        </script>
    @endif
@endsection