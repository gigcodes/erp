@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">
                <h2 class="page-heading">Multiple Google Search Image ({{ $count_system }})</h2>
                
                <!--Product Search Input -->
                <form method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3">
                        <select data-placeholder="Select status" class="form-control select-multiple2" name="status_id[]" multiple>
                            @foreach (\App\Helpers\StatusHelper::getStatus() as $id=>$name)
                                <option value="{{ $id }}" {{ isset($status_id) && in_array($id, $status_id)  ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-3">
                        {!! $category_selection !!}
                    </div>


                    <div class="form-group mr-3">
                        @php $brands = \App\Brand::getAll();
                        @endphp
                        <select data-placeholder="Select brands" class="form-control select-multiple2" name="brand[]" multiple>
                            <optgroup label="Brands">
                                @foreach ($brands as $id => $name)
                                    <option value="{{ $id }}" {{ isset($brand) && $brand == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-3">
                        <input placeholder="Shoe Size" type="text" name="shoe_size" value="{{request()->get('shoe_size')}}" class="form-control-sm form-control">
                    </div>
                    <div class="form-group mr-3">
                        @php $colors = new \App\Colors();
                        @endphp
                        <select data-placeholder="Select color" class="form-control select-multiple2" name="color[]" multiple>
                            <optgroup label="Colors">
                                @foreach ($colors->all() as $id => $name)
                                    <option value="{{ $id }}" {{ isset($color) && $color == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        @php $suppliers = new \App\Supplier();
                        @endphp
                        <select data-placeholder="Select Supplier" class="form-control select-multiple2" name="supplier[]" multiple>
                            <optgroup label="Suppliers">
                                @foreach ($suppliers->select('id','supplier')->where('supplier_status_id',1)->get() as $id => $suppliers)
                                    <option value="{{ $suppliers->supplier }}" {{ isset($supplier) && $supplier == $suppliers->supplier ? 'selected' : '' }}>{{ $suppliers->supplier }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    @if (Auth::user()->hasRole('Admin'))
                        @if(!empty($locations))
                            <div class="form-group mr-3">
                                <select data-placeholder="Select location" class="form-control select-multiple2" name="location[]" multiple>
                                    <optgroup label="Locations">
                                        @foreach ($locations as $name)
                                            <option value="{{ $name }}" {{ isset($location) && $location == $name ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        @endif
                        <div class="form-group mr-3">
                            <input type="checkbox" name="no_locations" id="no_locations" {{ isset($no_locations) ? 'checked' : '' }}>
                            <label for="no_locations">With no Locations</label>
                        </div>
                    @endif
                    <div class="form-group mr-3">
                        <input type="checkbox" name="quick_product" id="quick_product" {{ isset($quick_product) == 'true' ? 'checked' : '' }}  value="true">
                        <label for="quick_product">Quick Sell</label>
                    </div>
                    <div class="form-group mr-3">
                        <select class="form-control select-multiple2" name="quick_sell_groups[]" multiple data-placeholder="Quick Sell Groups...">
                            @foreach ($quick_sell_groups as $key => $quick_sell)
                                <option value="{{ $quick_sell->id }}" {{ in_array($quick_sell->id, request()->get('quick_sell_groups', [])) ? 'selected' : '' }}>{{ $quick_sell->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-3">
                        <strong class="mr-3">Price</strong>
                        <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>
                    </div>
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form>


            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <button type="button" class="btn btn-secondary select-all-system-btn" data-count="0">Send All In System</button>
    <button type="button" class="btn btn-secondary select-all-page-btn" data-count="0">Send All On Page</button>

    <div class="productGrid" id="productGrid">
        {!! $products->appends(Request::except('page'))->links() !!}
        <form method="POST" action="{{route('google.search.crop')}}" id="theForm">
            {{ csrf_field() }}
            <div class="row">
                @foreach ($products as $product)
                    <div class="col-md-3 col-xs-6 text-left" style="border: 1px solid #cccccc;">
                        <a href="{{ route('products.show', $product->id) }}" target="_blank"><img src="{{ $product->getMedia(config('constants.media_tags'))->first() ? $product->getMedia(config('constants.media_tags'))->first()->getUrl() : '' }}" class="img-responsive grid-image" alt=""/></a>
                        <p>Status : {{ ucwords(\App\Helpers\StatusHelper::getStatus()[$product->status_id]) }}</p>
                        <p>Brand : {{ isset($product->brands) ? $product->brands->name : "" }}</p>
                        <p>Transit Status : {{ $product->purchase_status }}</p>
                        <p>Location : {{ ($product->location) ? $product->location : "" }}</p>
                        <p>Sku : {{ $product->sku }}</p>
                        <p>Id : {{ $product->id }}</p>
                        <p>Size : {{ $product->size}}</p>
                        <p>Price ({{ $product->currency }}) : {{ $product->price }}</p>
                        <p>Price (INR) : {{ $product->price_inr }}</p>
                        <p>Price Special (INR) : {{ $product->price_special }}</p>
                        <input type="checkbox" class="select-product-edit" name="product_id" value="{{ $product->id }}" style="margin: 10px !important;">
                        @if($product->status_id == 31)<a href="{{ route('products.show', $product->id) }}" target="_blank" class="btn btn-secondary">Verify</a>@endif
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col text-center">
                    <button type="button" class="btn btn-image my-3" id="sendImageMessage" onclick="sendImage()"><img src="/images/filled-sent.png"/></button>
                </div>
            </div>
        </form>
        {!! $products->appends(Request::except('page'))->links() !!}
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            var ids = <?php echo json_encode($all_products_system); ?>;

            $(".select-multiple").multiselect();
            $(".select-multiple2").select2();

            

            $('.select-all-page-btn').on('click', function () {
                var result = confirm("Do you want to send " + $("input[name='product_id']").length + " images to Google?");
                var ids = [];
                $.each($("input[name='product_id']"), function () {
                    ids.push($(this).val());
                });
                if (result) {
                    for (i = 0; i < ids.length && i < 1000; i++) {
                        $.ajax({
                            url: "{{ route('google.search.product-save') }}",
                            type: 'POST',
                            beforeSend: function () {
                                $("#loading-image").show();
                            },
                            success: function (response) {
                                $("#loading-image").hide();
                            },
                            data: {
                                id: ids[i],
                                _token: "{{ csrf_token() }}",
                            }
                        });
                    }
                }
            });

            $('.select-all-system-btn').on('click', function () {
                var result = confirm("Do you want to send " + ids.length + " images to Google?");
                if (result) {
                    for (i = 0; i < ids.length && i < 1000; i++) {
                        $.ajax({
                            url: "{{ route('google.search.product-save') }}",
                            type: 'POST',
                            beforeSend: function () {
                                $("#loading-image").show();
                            },
                            success: function (response) {
                                $("#loading-image").hide();
                            },
                            data: {
                                id: ids[i],
                                _token: "{{ csrf_token() }}",
                            }
                        });
                    }
                }
            });

            
        });
        function sendImage() {
                
                var clicked = [];
                $.each($("input[name='product_id']:checked"), function () {
                    clicked.push($(this).val());
                });

                if (clicked.length == 0) {
                    alert('Please Select Product');
                } else {
                    $.each($("input[name='product_id']:checked"), function () {
                        id = $(this).val();
                        $.ajax({
                            url: "{{ route('google.search.product-save') }}",
                            type: 'POST',
                            beforeSend: function () {
                                $("#loading-image").show();
                            },
                            success: function (response) {
                                $("#loading-image").hide();
                            },
                            data: {
                                id: id,
                                _token: "{{ csrf_token() }}",
                            }
                        });
                    });
                    location.reload();
                }
            }
    </script>
@endsection
