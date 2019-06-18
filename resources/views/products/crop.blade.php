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
                <br><br>
                <input style="display: inline; width: 400px;" type="text" class="form-control" placeholder="Remark..." name="remark" id="remark">&nbsp;<button class="btn btn-danger">Reject</button>
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
            <div>
                <form action="">
                    @foreach($product->media()->get() as $image)
                        <?php
                        list($height, $width) = getimagesize($image->getUrl())
                        ?>
                        @if ($height == 1000 && $width === 1000)
                            <div style="position: relative; margin-bottom: 5px; width: 1000px;height: 1000px; background-image: url('{{$image->getUrl()}}'); background-size: cover">
                                <img src="{{ asset('images/'.$img) }}" alt="">
                                <div style="position: absolute; top: 5px;left:380px;">
                                    <p><strong>Image Info</strong></p>
                                    <select class="form-control" name="" id="">
                                        <option value="ok">Ok</option>
                                    </select>
                                    <li>
                                        Dimension: {{$product->lmeasurement}} X {{$product->hmeasurement}} X {{$product->dmeasurement}}
                                    </li>
                                </div>
                            </div>
                                <br>
                            <hr>
                                <br>
                        @endif
                    @endforeach
                    <div class="form-group text-right">
                        <button class="btn btn-default">Update Cropped Images</button>
                    </div>
                </form>
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