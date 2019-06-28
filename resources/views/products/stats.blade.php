@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Product Stats ({{$products->total()}})</h2>
        </div>
    </div>
    <form action="{{ action('ProductController@productStats') }}" method="get">
        <div class="row mb-5">
            <div class="col-md-2">
                <input value="{{$sku}}" type="text" name="sku" id="sku" placeholder="Sku" class="form-control">
            </div>
            <div class="col-md-1">
                <button class="btn btn-image btn-default">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-12 text-center">
            {!! $products->links() !!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Sku</th>
                    <th>Title</th>
                    <th>Cropped</th>
                    <th>Cropped Approved</th>
                    <th>Cropped Rejected</th>
                    <th>Cropped Sequenced</th>
                    <th>List Approved</th>
                </tr>
                @foreach($products as $product)
                    <tr>
                        <td>{{$product->sku}}</td>
                        <td>{{$product->name}}</td>
                        <td>{{$product->is_images_processed ? 'Yes' : 'No'}}</td>
                        <td>
                            {{$product->is_crop_approved ? 'Yes' : 'No'}}<br>
                            {{$product->cropApprover  ? $product->cropApprover->name : 'N/A'}}
                        </td>
                        <td>{{ $product->is_crop_rejected ? 'Yes' : 'No' }}<br>
                            {{$product->cropRejector  ? $product->cropRejector->name : 'N/A'}}
                        </td>
                        <td>
                            {{ $product->is_crop_ordered ? 'Yes' : 'No' }}<br>
                            {{$product->cropOrderer  ? $product->cropOrderer->name : 'N/A'}}
                        </td>
                        <td>{{ $product->is_approved ? 'Yes' : 'No' }}<br>
                            {{$product->approver  ? $product->approver->name : 'N/A'}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {!! $products->links() !!}
        </div>
    </div>
@endsection