@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">
                Rejected Cropped Images ({{ $products->total() }})
            </h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! $products->links() !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="get" action="{{action('ProductCropperController@showRejectedCrops')}}">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <input value="{{$reason}}" type="text" name="reason" id="reason" placeholder="Reason..." class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <select name="user_id" id="user_id" class="form-control select2" placeholder="Select user...">
                                    <option value="">Select user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{request()->get('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                {!! $category_array !!}
                            </div>
                            <div class="form-group col-md-2">
                                <select class="form-control select2" name="supplier[]" multiple placeholder="Suppliers">
                                     @foreach ($suppliers as $key => $item)
                                        <option value="{{ $item->id }}" {{ in_array($item->id, request()->get('supplier', [])) ? 'selected' : '' }}>{{ $item->supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-3">
                                @php $brands = \App\Brand::getAll(); @endphp
                                <select class="form-control select2" name="brand[]" multiple placeholder="Brands...">
                                    @foreach ($brands as $key => $name)
                                        <option value="{{ $key }}" {{ in_array($key, request()->get('brand', [])) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group mr-3">
                                @php $colors = new \App\Colors(); @endphp
                                <select class="form-control select2" name="color[]" multiple placeholder="Colors...">
                                    <@foreach ($colors->all() as $key => $col)
                                        <option value="{{ $key }}" {{ in_array($key, request()->get('color', [])) ? 'selected' : '' }}>{{ $col }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->hasRole('Admin'))
                                @php $locations = \App\ProductLocation::pluck("name","name"); @endphp
                                <div class="form-group mr-3">
                                    <select class="form-control select2" name="location[]" multiple placeholder="Location...">
                                        @foreach ($locations as $name)
                                            <option value="{{ $name }}" {{ in_array($name, request()->get('location', [])) ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group mr-3">
                                <input name="size" type="text" class="form-control"
                                       value="{{ request()->get('size') }}"
                                       placeholder="Size">
                            </div>
                            <div class="form-group col-md-1">
                                <button class="btn btn-image"><img src="{{asset('images/search.png')}}" alt="Search"></button>
                                <a href="{{url()->current()}}" class="btn btn-image" style="position: absolute;"><img src="/images/clear-filters.png"/></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered mt-5">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Supplier</th>
                            <th>Category</th>
                            <th>Remark</th>
                            <th>Rejected By</th>
                        </tr>
                        @foreach($products as $product)
                            <tr class="rec_{{$product->id}}">
                                <td>
                                    <img style="width: 120px;" src="{{ $product->imageurl }}" alt="Image">
                                </td>
                                <td>
                                    {{ $product->name }}
                                </td>
                                <td>
                                    {{ $product->sku }}
                                </td>
                                <td>
                                    {{ $product->supplier ?? '-' }}
                                </td>
                                <td>
                                    {{ $product->product_category ? $product->product_category->title : '-'}}
                                </td>
                                <td>{{ $product->crop_remark ?? '-' }}</td>
                                <td>
                                    {{ $product->cropRejector ? $product->cropRejector->name : 'N/A' }}
                                </td>
                            </tr>
                            <tr class="rec_{{$product->id}}">
                                <td colspan="2">

                                </td>
                                <td colspan="2">
                                    <strong>Remark</strong><br>
                                    {{ $product->crop_remark ?? '-' }}
                                </td>
                                <td colspan="3">
                                    <strong>Actions</strong><br>
{{--                                    <a target="_new" href="{{ action('ProductCropperController@showImageToBeVerified', $product->id) }}" class="btn btn-sm btn-secondary">Show Grid</a>--}}
                                    <a target="_new" href="{{ action('ProductCropperController@showRejectedImageToBeverified', $product->id) }}" class="btn btn-sm btn-secondary">Check Cropping</a>
                                    <a target="_new" href="{{ action('ProductController@show', $product->id) }}" class="btn btn-default btn-sm">Show Product</a>
                                    <a data-id="{{$product->id}}" class="btn btn-danger btn-sm text-light delete-product btn-sm">Delete</a>&nbsp;
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
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        
        $(".select2").each(function(){
            $(this).select2({
                placeholder : $(this).attr('placeholder'),
            });
        });

        $(document).on('click', '.delete-product', function() {
            let pid = $(this).attr('data-id');

            $.ajax({
                url: '{{ action('ProductController@deleteProduct') }}',
                data: {
                    product_id: pid
                },
                success: function(response) {
                    $('.rec_'+pid).hide();
                }
            });
        });
    </script>
@endsection