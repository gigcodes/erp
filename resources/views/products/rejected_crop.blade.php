@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4 class="page-heading">
                <a href="{{ action('ProductCropperController@showRejectedCrops') }}">Show All</a> &nbsp; Rejected Cropped Image
            </h4>
        </div>
        <form action="{{action('ProductCropperController@approveRejectedCropped', $product->id)}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="col-md-12 text-center">
                <button href="{{ action('ProductCropperController@approveRejectedCropped', $product->id) }}" type="button" class="btn btn-default">Approve</button>
                @if($secondProduct)
                    <a href="{{ action('ProductCropperController@showRejectedImageToBeverified', $secondProduct->id) }}">Next Image</a>
                @endif
            </div>
        </form>
        <div class="col-md-12">
            <table class="table table-striped table-bordered" style="width: 100%">
                <tr>
                    <td>
                        {{ $product->name }}
                        <br>
                        {{ $product->sku }}
                        <br>
                        {{ $product->product_category->title }}
                        <br>
                        <a class="btn btn-secondary" href="{{ action('ProductController@show', $product->id) }}">Product Details</a>
                    </td>
                    <td>
                        <p>Reject Remark : {{ $product->crop_remark ?? 'N/A' }}</p>
                        <a class="btn btn-secondary btn-sm" href="{{ action('ProductCropperController@downloadImagesForProducts', [$product->id, 'cropped']) }}">Download Cropped</a>
                        <br><br>
                        <a class="btn btn-secondary btn-sm" href="{{ action('ProductCropperController@downloadImagesForProducts', [$product->id, 'original']) }}">Download Original</a>
                    </td>
                    <td>
                        <strong>Dimension: {{round($product->lmeasurement*0.393701)}} X {{round($product->hmeasurement*0.393701)}} X {{round($product->dmeasurement*0.393701)}}</strong>
                    </td>
                    <td>
                        <form method="post" action="{{ action('ProductCropperController@approveRejectedCropped', $product->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="images">Images</label>
                                <input type="file" name="images[]" id="images" multiple class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-secondary">Approve With Changes</button>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
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

{{--    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>--}}

    @if (Session::has('mesage'))
        <script>
            toastr['success'](
                '{{Session::get('message')}}',
                'success'
            )
        </script>
    @endif
@endsection