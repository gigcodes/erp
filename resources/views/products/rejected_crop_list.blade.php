@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">
                Rejected Cropped Images
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
                            <div class="col-md-2">
                                <input value="{{$reason}}" type="text" name="reason" id="reason" placeholder="Reason..." class="form-control">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="category[]">
                                    @foreach ($category_array as $data)
                                        <option value="{{ $data['id'] }}" {{ in_array($data['id'], $selected_categories) ? 'selected' : '' }}>{{ $data['title'] }}</option>
                                        @if ($data['title'] == 'Men')
                                            @php
                                                $color = '#D6EAF8';
                                            @endphp
                                        @elseif ($data['title'] == 'Women')
                                            @php
                                                $color = '#FADBD8';
                                            @endphp
                                        @else
                                            @php
                                                $color = '';
                                            @endphp
                                        @endif

                                        @foreach ($data['child'] as $children)
                                            <option style="background-color: {{ $color }};" value="{{ $children['id'] }}" {{ in_array($children['id'], $selected_categories) ? 'selected' : '' }}>&nbsp;&nbsp;{{ $children['title'] }}</option>
                                            @foreach ($children['child'] as $child)
                                                <option style="background-color: {{ $color }};" value="{{ $child['id'] }}" {{ in_array($child['id'], $selected_categories) ? 'selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;{{ $child['title'] }}</option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control select-multiple" name="supplier[]" multiple>
                                    <optgroup label="Suppliers">
                                        @foreach ($suppliers as $key => $item)
                                            <option value="{{ $item->id }}" {{ isset($supplier) && in_array($item->id, $supplier) ? 'selected' : '' }}>{{ $item->supplier }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-image"><img src="{{asset('images/search.png')}}" alt="Search"></button>
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
                                <td colspan="3">

                                </td>
                                <td colspan="2">
                                    <strong>Remark</strong><br>
                                    {{ $product->crop_remark ?? '-' }}
                                </td>
                                <td colspan="2">
                                    <strong>Actions</strong><br>
                                    <a href="{{ action('ProductCropperController@showRejectedImageToBeverified', $product->id) }}" class="btn btn-sm btn-secondary">Check Cropping</a>
                                    <a href="{{ action('ProductController@show', $product->id) }}" class="btn btn-default btn-sm">Show Product</a>
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