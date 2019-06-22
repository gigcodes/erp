@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Order Cropped Images ({{$total}})</h2>
        </div>
        <form action="{{ action('ProductCropperController@saveSequence', $product->id) }}" method="post">
            @csrf
            <div class="col-md-12 text-center">
                <div id="sortable">
                    @foreach($product->getMedia('gallery') as $media)
                        <div class="card" style="display: inline-block; background: #dddddd">
                            <img src="{{ $media->getUrl() }}" alt="" style="width: 150px;">
                            <input type="hidden" name="images[]" value="{{$media->id}}">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="form-group">
                    <button class="btn btn-secondary">Save Sequence</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $( function() {
            $( "#sortable" ).sortable();
            $( "#sortable" ).disableSelection();
        } );
    </script>
@endsection