@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Rejected Listings ({{$products->total()}})</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form method="get" action="{{action('ProductController@showRejectedListedProducts')}}">
                <div class="row">
                    <div class="col-md-2">
                        <select name="type" id="type" class="form-control">
                            <option value="">Any</option>
                            <option value="rejected">Only Rejected</option>
                            <option value="accepted">Only Accepted</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="reason" id="reason" class="form-control">
                            <option value="">All</option>
                            <option {{ $reason=='Category Incorrect' ? 'selected' : '' }} value="Category Incorrect">Category Incorrect</option>
                            <option {{ $reason=='Price Not Incorrect' ? 'selected' : '' }} value="Price Not Incorrect">Price Not Correct</option>
                            <option {{ $reason=='Price Not Found' ? 'selected' : '' }} value="Price Not Found">Price Not Found</option>
                            <option {{ $reason=='Color Not Found' ? 'selected' : '' }} value="Color Not Found">Color Not Found</option>
                            <option {{ $reason=='Category Not Found' ? 'selected' : '' }} value="Category Not Found">Category Not Found</option>
                            <option {{ $reason=='Description Not Found' ? 'selected' : '' }} value="Description Not Found">Description Not Found</option>
                            <option {{ $reason=='Details Not Found' ? 'selected' : '' }} value="Details Not Found">Details Not Found</option>
                            <option {{ $reason=='Composition Not Found' ? 'selected' : '' }} value="Composition Not Found">Composition Not Found</option>
                        </select>
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
        <div class="col-md-12 text-center">
            {!! $products->links() !!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-5">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Image</th>
                    <th>Rejected On</th>
                    <th>Reason</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Supplier</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Size</th>
                    <th>Composition</th>
                    <th>Color</th>
                    <th>Price</th>
                </tr>
                @foreach($products as $product)
                    <tr class="rec_{{$product->id}}">
                        <td>
                            <a href="{{ action('ProductController@show', $product->id) }}">
                                <img style="width: 150px;" src="{{ $product->getMedia('gallery')->first() ? $product->getMedia('gallery')->first()->getUrl() : '' }}" alt="Image">
                            </a>
                        </td>
                        <td>{{ $product->listing_rejected_on }}</td>
                        <td>{{ $product->listing_remark ?? 'N/A' }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>
                            @foreach($product->suppliers()->get() as $supplier)
                                <span class="label label-default">{{ $supplier->supplier }}</span>
                            @endforeach
                        </td>
                        <td>{{ $product->short_description }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->size }}</td>
                        <td>{{ $product->composition }}</td>
                        <td>{{ $product->color }}</td>
                        <td>
                            <strong>EUR</strong> {{ $product->price }}<br>
                            <strong>INR</strong> {{ $product->price_inr }}<br>
                            <strong>Special</strong> {{ $product->price_special }}
                        </td>
                    </tr>
                    <tr class="rec_{{$product->id}}">
                        <td colspan="4">
                            <p><strong>Options</strong></p>
                            <div class="form-group">
                                <input data-id="{{$product->id}}" {{$product->is_corrected ? 'checked' : ''}} type="checkbox" name="corrected_{{$product->id}}" id="corrected_{{$product->id}}">
                                <label for="corrected_{{$product->id}}">Corrected</label>
                                <br>
                                <input data-id="{{$product->id}}" {{$product->is_script_corrected ? 'checked' : ''}} type="checkbox" name="script_corrected_{{$product->id}}" id="script_corrected_{{$product->id}}">
                                <label for="script_corrected_{{$product->id}}">Script Corrected</label>
                                <br>
                                <button class="btn btn-sm btn-secondary save-corrections" data-id="{{$product->id}}">Save</button>
                            </div>
                        </td>
                        <td colspan="4">
                            <p><strong>Remarks</strong></p>
                            <p>{{ $product->listing_remark }}</p>
                        </td>
                        <td colspan="4">
                            <p><strong>Final Action</strong></p>
                            <div class="form-group">
                                <a data-id="{{$product->id}}" class="btn btn-danger btn-sm text-light delete-product">Delete</a>&nbsp;
                                @if($product->is_approved)
                                    <a data-id="{{$product->id}}" class="btn btn-secondary btn-sm text-light relist-product">Re-list</a>
                                @endif
                            </div>
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

@section('scripts')
    <script>
        $(document).on('click', '.save-corrections', function() {
            let pid = $(this).attr('data-id');
            let is_corrected = $("#corrected_"+pid).is(':checked') ? 1 : 0;
            let is_script_corrected = $("#script_corrected_"+pid).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ action('ProductController@updateProductListingStats') }}',
                data: {
                    is_corrected: is_corrected,
                    is_script_corrected: is_script_corrected,
                    product_id: pid
                },
                success: function(response) {
                    console.log(response);
                }
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

        $(document).on('click', '.relist-product', function() {
            let pid = $(this).attr('data-id');

            $.ajax({
                url: '{{ action('ProductController@relistProduct') }}',
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