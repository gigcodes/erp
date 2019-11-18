@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">
        .select-multiple-cat-list .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">

                <!--roletype-->
                <h2 class="page-heading">Attach Images to Message (<span id="products_count">{{ $products_count }}</span>)</h2>

                <!--pending products count-->
                @if(auth()->user()->isAdmin())
                    @if( $roletype != 'Selection' && $roletype != 'Sale' )
                        <div class="pt-2 pb-3">
                            <a href="{{ route('pending',$roletype) }}"><strong>Pending
                                    : </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
                        </div>
                    @endif
                @endif

            <!--attach Product-->
                @if( isset($doSelection) )
                    <p><strong> {{ strtoupper($model_type)  }} ID : {{ $model_id }} </strong></p>
            @endif

            <!--Product Search Input -->
                <form action="{{ route('search') }}" method="GET" id="searchForm" class="form-inline align-items-start">
                    <input type="hidden" name="source_of_search" value="attach_media">
                    @csrf
                    {{-- <div class="form-group">
                        <div class="row"> --}}
                    <input type="hidden" name="selected_products" id="selected_products" value="">
                    <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control" id="product-search"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="sku,brand,category,status,stage">
                        <input hidden name="roletype" type="text" value="{{ $roletype }}">
                        <input hidden name="model_type" type="text" value="{{ $model_type }}">
                        {{--@if( $roletype == 'Sale' )
                            <input hidden name="saleId" type="text" value="{{ $sale_id ?? '' }}">
                        @endif--}}
                        @if( isset($doSelection) )
                            <input hidden name="doSelection" type="text" value="true">
                            <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                            <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                            <input hidden name="assigned_user" type="text" value="{{ $assigned_user ?? '' }}">
                            <input hidden name="status" type="text" value="{{ $status ?? '' }}">
                        @endif
                    </div>
                    <div class="form-group mr-3">
                        {!! $category_selection !!}
                    </div>

                    <div class="form-group mr-3">
                        @php $brands = \App\Brand::getAll(); @endphp
                        {{-- {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                        <select class="form-control select-multiple" name="brand[]" multiple data-placeholder="Brands...">
                            <optgroup label="Brands">
                                @foreach ($brands as $key => $name)
                                    <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        {{-- <strong>Color</strong> --}}
                        @php $colors = new \App\Colors(); @endphp
                        {{-- {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                        <select class="form-control select-multiple" name="color[]" multiple data-placeholder="Colors...">
                            <optgroup label="Colors">
                                @foreach ($colors->all() as $key => $col)
                                    <option value="{{ $key }}" {{ isset($color) && $color == $key ? 'selected' : '' }}>{{ $col }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        {{-- @php $suppliers = new \App\ReadOnly\SupplierList(); @endphp --}}
                        {{-- {!! Form::select('supplier[]',$suppliers->all(), (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                        <select class="form-control select-multiple" name="supplier[]" multiple data-placeholder="Supplier...">
                            <optgroup label="Suppliers">
                                @foreach ($suppliers as $key => $supp)
                                    <option value="{{ $supp->id }}" {{ isset($supplier) && $supplier == $supp->id ? 'selected' : '' }}>{{ $supp->supplier }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    @if (Auth::user()->hasRole('Admin'))
                        <div class="form-group mr-3">
                            <select class="form-control select-multiple" name="location[]" multiple data-placeholder="Location...">
                                <optgroup label="Locations">
                                    @foreach ($locations as $name)
                                        <option value="{{ $name }}" {{ isset($location) && $location == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    @endif

                    <div class="form-group mr-3">
                        <input name="size" type="text" class="form-control"
                               value="{{ isset($size) ? $size : '' }}"
                               placeholder="Size">
                    </div>
                     <div class="form-group mr-3">
                        <select class="form-control select-multiple" name="quick_sell_groups[]" multiple data-placeholder="Quick Sell Groups...">
                            @foreach ($quick_sell_groups as $key => $quick_sell)
                                <option value="{{ $quick_sell->id }}" {{ in_array($quick_sell->id, request()->get('quick_sell_groups', [])) ? 'selected' : '' }}>{{ $quick_sell->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-3">
                        {!! Form::select('per_page',[
                        "20" => "20 Images Per Page",
                        "30" => "30 Images Per Page",
                        "50" => "50 Images Per Page",
                        "100" => "100 Images Per Page",
                        ], request()->get("per_page",null), ['placeholder' => '-- Select Images Per Page --','class' => 'form-control']) !!}
                    </div>
                    <div class="form-group mr-3">
                        <strong class="mr-3">Price</strong>
                        <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="400000" data-slider-step="1000" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '400000' }}]"/>
                    </div>


                    <input type="hidden" name="message" value="{{ $model_type == 'customers' ? "$message_body" : 'Images attached from grid' }}" id="attach_all_message">
                    <input type="hidden" name="{{ $model_type == 'customer' ? 'customer_id' : 'nothing' }}" value="{{ $model_id }}" id="attach_all_model_id">
                    <input type="hidden" name="status" value="{{ $status }}" id="attach_all_status">
                    &nbsp;
                    <input type="checkbox" class="is_on_sale" id="is_on_sale" name="is_on_sale"><label
                            for="is_on_sale">Sale Products</label>
                    <input type="checkbox" class="random" id="random" name="random"><label
                            for="random">Random</label>        


                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                    {{-- </div>
                </div> --}}
                </form>

                <form action="{{ route('search') }}" method="GET" id="quickProducts" class="form-inline align-items-start my-3">
                    <input type="hidden" name="quick_product" value="true">
                    <button type="submit" class="btn btn-xs btn-secondary">Quick Sell</button>
                </form>
                <button type="button" class="btn btn-secondary select-all-product-btn" data-count="0">Select All</button>
                <button type="button" class="btn btn-secondary select-all-product-btn" data-count="20">Select 20</button>
                <button type="button" class="btn btn-secondary select-all-product-btn" data-count="30">Select 30</button>
                <button type="button" class="btn btn-secondary select-all-product-btn" data-count="50">Select 50</button>
                <button type="button" class="btn btn-secondary select-all-product-btn" data-count="100">Select 100</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="productGrid" id="productGrid">
        @include('partials.image-load')
    </div>
    @php 
        $action = url('whatsapp/updateAndCreate/'); 
        if ($model_type == 'images') {
            $action =  route('image.grid.attach');
        } else if ($model_type == 'customers') {
            $action =  route('customer.whatsapp.send.all', 'false');
        } else if ($model_type == 'purchase-replace') {
            $action =  route('purchase.product.replace');
        } else if ($model_type == 'broadcast-images') {
            $action =  route('broadcast.images.link');
        } else if ($model_type == 'customer') {
            $action =  route('attachImages.queue');
        } else if ($model_type == 'selected_customer') {
            $action =  route('whatsapp.send_selected_customer');
        } else if ($model_type == 'product-templates') {
            $action =  route('product.templates');
        }
    @endphp
    <form action="{{ $action }}" data-model-type="{{$model_type}}" method="POST" id="attachImageForm">
        @csrf
        <input type="hidden" id="send_pdf" name="send_pdf" value="0"/>
        @if ($model_type == 'customers')
            <input type="hidden" name="sending_time" value="{{ $sending_time }}"/>
        @endif

        @if (request()->get('return_url'))
            <input type="hidden" name="return_url" value="{{ request()->get('return_url') }}"/>
        @endif

        <input type="hidden" name="images" id="images" value="">
        <input type="hidden" name="image" value="">
        <input type="hidden" name="screenshot_path" value="">
        <input type="hidden" name="message" value="{{ $model_type == 'customers' || $model_type == 'selected_customer' ? "$message_body" : '' }}">
        <input type="hidden" name="{{ $model_type == 'customer' ? 'customer_id' : ($model_type == 'purchase-replace' ? 'moduleid' : ($model_type == 'selected_customer' ? 'customers_id' : 'nothing')) }}" value="{{ $model_id }}">
        {{-- <input type="hidden" name="moduletype" value="{{ $model_type }}">
        <input type="hidden" name="assigned_to" value="{{ $assigned_user }}" /> --}}
        <input type="hidden" name="status" value="{{ $status }}">
    </form>
    <div id="confirmPdf" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <p>Do you want to send with Pdf ?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-approve-pdf">Yes</button>
            <button type="button" class="btn btn-secondary btn-ignore-pdf">No</button>
          </div>
        </div>
      </div>
    </div>




    <?php $stage = new \App\Stage(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script>
        var image_array = [];
        var all_product_ids = [<?= implode(',', $all_product_ids) ?>];
        $(document).ready(function () {
            $(".select-multiple").select2();
            //$(".select-multiple-cat").multiselect();
            $("body").tooltip({selector: '[data-toggle=tooltip]'});
            $('.lazy').Lazy({
                effect: 'fadeIn'
            });
            $(".select-multiple-cat-list").select2();
            $('.select-multiple-cat-list').on('select2:close', function (evt) {
                var uldiv = $(this).siblings('span.select2').find('ul')
                var count = uldiv.find('li').length - 1;
                if (count == 0) {

                } else {
                    uldiv.html('<li class="select2-selection__choice">' + count + ' item selected</li>');
                }
            });

            var selectAllBtn = $(".select-all-product-btn");
            selectAllBtn.on("click", function () {
                var $this = $(this);
                var vcount = 0;

                vcount = $this.data('count');
                if (vcount == 0) {
                    vcount = 'all';
                }

                if ($this.hasClass("has-all-selected") === false) {
                    $this.html("Deselect " + vcount);
                    if (vcount == 'all') {
                        $(".select-pr-list-chk").prop("checked", true).trigger('change');
                    } else {
                        var boxes = $(".select-pr-list-chk");
                        for (i = 0; i < vcount; i++) {
                            try {
                                $(boxes[i]).prop("checked", true).trigger('change');
                            } catch (err) {
                            }
                        }
                    }
                    $this.addClass("has-all-selected");
                } else {
                    $this.html("Select " + vcount);
                    if (vcount == 'all') {
                        $(".select-pr-list-chk").prop("checked", false).trigger('change');
                    } else {
                        var boxes = $(".select-pr-list-chk");
                        for (i = 0; i < vcount; i++) {
                            try {
                                $(boxes[i]).prop("checked", false).trigger('change');
                            } catch (err) {
                            }
                        }
                    }
                    $this.removeClass("has-all-selected");
                }

                /*// Add all images to array
                image_array = [];
                console.log(all_product_ids.length);
                for (i = 0; i < all_product_ids.length && i < vcount; i++) {
                    image_array.push(all_product_ids[i]);
                }
                image_array = unique(image_array);
                console.log(image_array);*/
            })
        });

        function unique(list) {
            var result = [];
            $.each(list, function (i, e) {
                if ($.inArray(e, result) == -1) result.push(e);
            });
            return result;
        }

        $(document).on('change', '.select-pr-list-chk', function (e) {
            var $this = $(this);
            var productCard = $this.closest(".product-list-card").find(".attach-photo");
            if (productCard.length > 0) {
                var image = productCard.data("image");
                if ($this.is(":checked") === true) {
                    //Object.keys(image).forEach(function (index) {
                    image_array.push(image);
                    //});
                    image_array = unique(image_array);

                } else {
                    //Object.keys(image).forEach(function (key) {
                    var index = image_array.indexOf(image);
                    image_array.splice(index, 1);
                    //});
                    image_array = unique(image_array);
                }
            }
        });


        // $('#product-search').autocomplete({
        //   source: function(request, response) {
        //     var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
        //
        //     response(results.slice(0, 10));
        //   }
        // });

        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href') + '&selected_products=' + JSON.stringify(image_array);

            getProducts(url);
        });

        function getProducts(url) {
            $.ajax({
                url: url
            }).done(function (data) {
                console.log(data);
                $('#productGrid').html(data.html);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
            }).fail(function () {
                alert('Error loading more products');
            });
        }

        $(document).on('click', '.attach-photo', function (e) {
            e.preventDefault();
            var image = $(this).data('image');

            if ($(this).data('attached') == 0) {
                $(this).data('attached', 1);
                image_array.push(image);
            } else {
                var index = image_array.indexOf(image);

                $(this).data('attached', 0);
                image_array.splice(index, 1);
            }

            $(this).toggleClass('btn-success');
            $(this).toggleClass('btn-secondary');

            console.log(image_array);
        });

        $(document).on('click', '.attach-photo-all', function (e) {
            e.preventDefault();
            var image = $(this).data('image');

            if ($(this).data('attached') == 0) {
                $(this).data('attached', 1);

                Object.keys(image).forEach(function (index) {
                    image_array.push(image[index]);
                });
            } else {
                Object.keys(image).forEach(function (key) {
                    var index = image_array.indexOf(image[key]);

                    image_array.splice(index, 1);
                });

                $(this).data('attached', 0);
            }

            $(this).toggleClass('btn-success');
            $(this).toggleClass('btn-secondary');

            console.log(image_array);
        });

        // $('#attachImageForm').on('submit', function(e) {
        //   e.preventDefault();
        //
        //   if (image_array.length == 0) {
        //     alert('Please select some images');
        //   } else {
        //     $('#images').val(JSON.stringify(image_array));
        //     alert(JSON.stringify(image_array));
        //     // $('#attachImageForm')[0].submit();
        //   }
        // });

        $('#searchForm button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            $('#selected_products').val(JSON.stringify(image_array));

            var url = "{{ route('search') }}";
            var formData = $('#searchForm').serialize();

            $.ajax({
                url: url,
                data: formData
            }).done(function (data) {
                all_product_ids = data.all_product_ids;
                $('#productGrid').html(data.html);
                $('#products_count').text(data.products_count);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
            }).fail(function () {
                alert('Error searching for products');
            });
        });

        $('#quickProducts').on('submit', function (e) {
            e.preventDefault();

            var url = "{{ route('search') }}?quick_product=true";
            var formData = $('#searchForm').serialize();

            $.ajax({
                url: url,
                data: formData
            }).done(function (data) {
                $('#productGrid').html(data.html);
                $('#products_count').text(data.products_count);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
            }).fail(function () {
                alert('Error searching for products');
            });
        });


        // $('#product-search').on('keyup', function() {
        //   alert('t');
        // });

        {{--@if($roletype == 'Supervisor')
         @if(auth()->user()->checkPermission('productsupervisor-edit'))
        attactApproveEvent();
        @endif
        @endif--}}

        jQuery('.btn-attach').click(function (e) {

            e.preventDefault();

            let btn = jQuery(this);
            let product_id = btn.attr('data-id');
            let model_id = btn.attr('model-id');
            let model_type = btn.attr('model-type');


            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/attachProductToModel/' + model_type + '/' + model_id + '/' + product_id,

                success: function (response) {

                    if (response.msg === 'success') {
                        btn.toggleClass('btn-success');
                        btn.html(response.action);
                    }
                }
            });
        });

        $(document).on('click', '#sendImageMessage', function () {
            @if ($model_type == 'purchase-replace')
            if (image_array.length > 1) {
                alert('Please select only one product');

                return;
            }
            @endif

            if (image_array.length == 0) {
                alert('Please select some images');
            } else {
                $('#images').val(JSON.stringify(image_array));
                var form = $('#attachImageForm');
                var modelType = form.data("model-type");
                if(modelType == "selected_customer" || modelType == "customer" || modelType == "customers") {
                    $("#confirmPdf").modal("show");
                }else{
                    $('#attachImageForm').submit();
                } 
            }
        });

        $(".btn-approve-pdf").on("click",function() {
            $("#send_pdf").val("1");
            $('#attachImageForm').submit();
        });

        $(".btn-ignore-pdf").on("click",function() {
            $("#send_pdf").val("0");
            $('#attachImageForm').submit();
        });
        // });

        $('#attachAllButton').on('click', function () {
            var url = "{{ route('customer.attach.all') }}";

            $('#searchForm').attr('action', url);
            $('#searchForm').attr('method', 'POST');

            $('#searchForm').submit();
        });

    </script>

@endsection

@section('scripts')
<script type="text/javascript">
    function myFunction(id){
    $('#description'+id).hide();    
    $('#description_full'+id).show();
   }
</script>

@endsection