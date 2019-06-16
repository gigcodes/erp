@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">
                Crop Image Approval
            </h2>
        </div>
        <div class="col-md-12">
            <div class="fotorama">
                @foreach($images as $image)

                @endforeach
            </div>
        </div>
        <div class="col-md-12">
            <form action="">
                <input type="button" class="btn btn-default" name="approved" value="Approve">
                <input type="button" class="btn btn-danger" name="rejected" value="Reject">
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Fotorama from CDNJS, 19 KB -->
    <link  href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>
@endsection