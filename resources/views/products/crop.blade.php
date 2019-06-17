@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h5 class="page-heading">
                Crop Image Approval
            </h5>
        </div>
        <div class="col-md-12 text-center">
            <form action="{{ action('ProductCropperController@rejectCrop', $product->id) }}">
                <a href="{{ action('ProductCropperController@approveCrop', $product->id) }}" type="button" class="btn btn-default">Approve</a>
                <br>
                <input type="text" class="form-control" placeholder="Remark..." name="remark" id="remark"><button href="{{ action('ProductCropperController@rejectCrop', $product->id) }}" class="btn btn-danger">Reject</button>
                @if($secondProduct)
                    <a href="{{ action('ProductCropperController@showImageToBeVerified', $secondProduct->id) }}">Next Image</a>
                @endif
            </form>
        </div>
        <div class="col-md-12">
            <div class="text-center">
                <h4>{{ $product->title }}</h4>
                <p>{{ $product->sku }}</p>
            </div>
            <div style="width: 650px; margin: 0 auto;" class="fotorama" data-nav="thumbs" data-allowfullscreen="true">
                @foreach($product->media()->get() as $image)
                    <a href="{{ $image->getUrl() }}"><img src="{{ $image->getUrl() }}"></a>
                @endforeach
            </div>
        </div>
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