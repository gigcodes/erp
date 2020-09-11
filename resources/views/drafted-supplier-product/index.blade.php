@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Drafted Products</h2>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th> </th>
                <th>Product id</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Status</th>
                <th>All images</th>
                <th>Supplier</th>
                <th width="280px">Action</th>
            </tr>
            @foreach ($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" id="" name="product_id" data-id="{{$product->id}}" value="">
                    </td>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->brand }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->status }}</td>
                    <td>
                        @if ($images = $product->getMedia(config('constants.media_tags')))
                            @foreach ($images as $image)
                                <img src="{{ $image->getUrl() }}" class="img-responsive" width="50px">
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $product->supplier }}</td>
                    <td>
                        <a href class="btn btn-image edit-modal-button" data-toggle="modal" data-target="#editModal"
                                data-product="{{ $product }}"><img src="/images/edit.png" /></a>

                        <form action="{{ route('products.destroy',$product->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            @if(auth()->user()->checkPermission('products-delete'))
                                <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    {!! $products->appends(Request::except('page'))->links() !!}

    @include('drafted-supplier-product.edit-modal',['product' => $product])

    <script type="text/javascript">
        $(document).on("submit","#formDraftedProduct", function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let formData = {
                id: $(this).data("id"),
                brand:  $('input[name="email"]').val(),
                category: $('input[name=category]').val(),
                short_description: $('input[name=short_description]').val(),
                price: $('input[name=price]').val(),
                status: $('input[name=status]').val()
            }
            $.ajax({
                url: "/drafted-products/edit",
                type: 'post',
                datatype: 'json',
                data: formData,
                success: function (response) {
                    $("#editModal").modal('hide');
                    alert(response.message);
                },
                error: function () {
                    alert('Oops, Something went wrong!!');
                }
            });
        });
    </script>

@endsection
