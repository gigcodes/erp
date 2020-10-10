@extends('layouts.app')


@if($cropped == 'on')
    @section('favicon' , 'approvedproductlisting.png')
@section('title', 'Approved Listing - ERP Sololuxury')
@endif
@section('favicon' , 'attributeedit.png')
@section('title', 'Approved Product Listing - ERP Sololuxury')

@section('title', 'Product Listing')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    <style>
        .quick-edit-color {
            transition: 1s ease-in-out;
        }
        /*thead th {*/
        /*    font-size: 0.6em;*/
        /*    padding: 1px !important;*/
        /*    height: 15px;*/
        /*}*/
        .thumbnail-pic {
            position: relative;
            display: inline-block;
        }
        .thumbnail-pic:hover .thumbnail-edit {
            display: block;
        }
        .thumbnail-edit {
            padding-top: 12px;
            padding-right: 7px;
            position: absolute;
            left: 0;
            top: 0;
            display: none;
        }
        .thumbnail-edit a {
            color: #FF0000;
        }
        .thumbnail-pic {
            position: relative;
            padding-top: 10px;
            display: inline-block;
        }
        .notify-badge {
            position: absolute;
            right: -20px;
            top: 10px;
            text-align: center;
            border-radius: 30px 30px 30px 30px;
            color: white;
            padding: 5px 10px;
            font-size: 10px;
        }
        .notify-red-badge {
            background: red;
        }
        .notify-green-badge {
            background: green;
        }
        .cropme-container {
            bottom: -43px;
            margin-left: 35px !important;
            top: 22px !important;
        }
        .product_filter .row > div:not(:first-child):not(:last-child) {
            padding-left: 10px;
            padding-right: 10px;
        }
        .product_filter .row > div:first-child {
            padding-right: 10px;
        }
        .product_filter .row > div:last-child {
            padding-left: 10px;
        }
        /* Select2 changes */
        .select2-container .select2-selection--single {
            height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
            right: 5px;
        }
        .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--multiple {
            border: 1px solid #ccc;
        }
        .select2-container .select2-selection--multiple {
            min-height: 34px;
        }
        .select2-selection select2-selection--multiple {
            padding: 0 5px;
        }
        .select2-container .select2-search--inline .select2-search__field {
            padding: 0 5px;
        }
        td.action > div, td.action > button {
            margin-top: 8px;
        }
        .lmeasurement-container, .dmeasurement-container, .hmeasurement-container {
            display: block;
            margin-bottom: 10px;
        }
        .quick-name {
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
            width: 90px;
            height: 1.2em;
            white-space: nowrap;
        }
        .quick-description {
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
            width: 100%;
            max-width: 140px;
            height: 1.2em;
            white-space: nowrap;
        }
    </style>
@endsection

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Approved Product Listing ({{ $products_count }}) <a
                        href="{{ action('ProductController@showSOP') }}?type=ListingApproved" class="pull-right">SOP</a>
            </h2>

                <form class="product_filter" action="{{ action('ProductController@approvedListing') }}" method="GET">
                <div class="row">
                    <div class="col-sm-1">
                        <div class="form-group">
                            <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}"
                                   placeholder="sku,brand,category,status,stage">
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            {{-- {!! $category_search !!} --}}
                            <select class="form-control select-multiple" name="category[]"
                                    data-placeholder="Category..">
                                <option></option>
                                @foreach ($category_array as $data)
                                    <option value="{{ $data['id'] }}" {{ in_array($data['id'], $selected_categories) ? 'selected' : '' }}>{{ $data['title'] }}</option>
                                    @if ($data['title'] == 'Men')
                                        @php
                                            $color = "#D6EAF8";
                                        @endphp
                                    @elseif ($data['title'] == 'Women')
                                        @php
                                            $color = "#FADBD8";
                                        @endphp
                                    @else
                                        @php
                                            $color = "";
                                        @endphp
                                    @endif

                                    @foreach ($data['child'] as $children)
                                        <option style="background-color: {{ $color }};"
                                                value="{{ $children['id'] }}" {{ in_array($children['id'], $selected_categories) ? 'selected' : '' }}>
                                            &nbsp;&nbsp;{{ $children['title'] }}</option>
                                        @foreach ($children['child'] as $child)
                                            <option style="background-color: {{ $color }};"
                                                    value="{{ $child['id'] }}" {{ in_array($child['id'], $selected_categories) ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $child['title'] }}</option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control select-multiple" name="brand[]" multiple
                                    data-placeholder="Brand..">
                                @foreach ($brands as $key => $name)
                                    <option value="{{ $key }}" {{ !empty(request()->get('brand')) && in_array($key, request()->get('brand', [])) ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control select-multiple" name="color[]" multiple
                                    data-placeholder="Color..">
                                @foreach ($colors as $key => $col)
                                    <option value="{{ $key }}" {{ !empty(request()->get('color')) && in_array($key, request()->get('color', [])) ? 'selected' : '' }}>{{ $col }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control select-multiple" name="supplier[]" multiple
                                    data-placeholder="Supplier..">
                                @foreach ($suppliers as $key => $item)
                                    <option value="{{ $item->id }}" {{ !empty(request()->get('supplier')) && in_array($item->id, request()->get('supplier', [])) ? 'selected' : '' }}>{{ $item->supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control  select-multiple" name="type" data-placeholder="Select type">
                                <option></option>
                                <option value="Not Listed" {{ isset($type) && $type == "Not Listed" ? 'selected' : ''  }}>
                                    Not Listed
                                </option>
                                <option value="Listed" {{ isset($type) && $type == "Listed" ? 'selected' : ''  }}>
                                    Listed
                                </option>
                                {{--              <option value="Approved" {{ isset($type) && $type == "Approved" ? 'selected' : ''  }}>Approved</option>--}}
                                {{--              <option value="Image Cropped" {{ isset($type) && $type == "Image Cropped" ? 'selected' : ''  }}>Image Cropped</option>--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control select-multiple" name="user_id" id="user_id"
                                    data-placeholder="Select user">
                                <option></option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select class="form-control select-multiple" name="crop_status"
                                    data-placeholder="Select cropped images">
                                <option></option>
                                <option value="Matched" {{app('request')->crop_status == "Matched" ? 'selected' : ''}}>Matched</option>
                                <option value="Not Matched" {{app('request')->crop_status == "Not Matched" ? 'selected' : ''}}>Not Matched</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4  text-right">
                        <div class="form-group">

                            @if(auth()->user()->isReviwerLikeAdmin('final_listing'))
                                <?php echo Form::checkbox("submit_for_approval", "on", (bool)(request('submit_for_approval') == "on"), ["class" => ""]); ?>
                                <lable for="submit_for_approval pr-3">Submit For approval ?</lable>
                            @endif

                            <button type="submit" class="btn btn-secondary" title="Filter"><i type="submit"
                                                                                              class="fa fa-filter"
                                                                                              aria-hidden="true"></i>
                            </button>

                            <a href="{{url()->current()}}" class="btn  btn-secondary" title="Clear"><i type="submit"
                                                                                                       class="fa fa-times"
                                                                                                       aria-hidden="true"></i></a>
                            <input type="button" onclick="pushProduct()" class="btn btn-secondary"
                                   value="Push product"/>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    {{-- @include('development.partials.modal-task')
    @include('development.partials.modal-quick-task') --}}

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="infinite-scroll table-responsive mt-5 infinite-scroll-data">

                @php
                    $imageCropperRole = Auth::user()->hasRole('ImageCropers');
                @endphp

                <table class="table table-bordered table-striped" style="min-width:1500px;width: 100%">
                    <thead>
                    <tr>
                        <th style="width:30px"><input type="checkbox" id="main_checkbox" name="choose_all"></th>
                        <th style="width:120px">Product ID</th>
                        <th style="width:70px">Image</th>
                        <th style="width:110px">Brand</th>
                        <th style="width:120px">Category</th>
                        <th style="width: 90px">Title</th>
                        <th style="max-width: 200px;"> Description</th>
                        <th style="width:120px">Composition</th>
                        <th style="width:120px">Color</th>
                        <th style="width:120px">Dimension</th>
                        <th style="width:100px">Sizes</th>
                        <th style="width:70px">Price</th>
                        <th style="min-width: 100px">Action</th>
                        <th style="width:120px">Status</th>
                        <th style="width:120px">User</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($products as $key => $product)
                        <tr style="display: none" id="product{{ $product->id }}">
                            <td colspan="15">
                                <div class="row">

                                    <div class="col-md-3">
                                        <p class="same-color">{{ strtoupper($product->name) }}</p>
                                        <br/>
                                        <p class="same-color" style="font-size: 18px;">
                                            <span style="text-decoration: line-through">EUR {{ number_format($product->price) }}</span>
                                            EUR {{ number_format($product->price_eur_special) }}

                                        </p>
                                        <?php
                                        // check brand sengment
                                        if ($product->brands) {
                                            $segmentPrice = \App\Brand::getSegmentPrice($product->brands->brand_segment, $product->category);
                                            if ($segmentPrice) {
                                                echo "<p class='same-color'>Min Segment Price : " . $segmentPrice->min_price . "<br>
                                                    Max Segment Price : " . $segmentPrice->max_price . "</p>";
                                            }
                                        }
                                        ?>
                                        <p>
                                            <strong class="same-color"
                                                    style="text-decoration: underline">Description</strong>
                                            <br/>
                                            <span id="description{{ $product->id }}" class="same-color">
                                                {{ ucwords(strtolower(html_entity_decode($product->short_description))) }}
                                            </span>
                                        </p>
                                        <br/>
                                        @php
                                            $descriptions = \App\ScrapedProducts::select('description','website')->where('sku', $product->sku)->get();
                                        @endphp

                                        @if ( $descriptions->count() > 0 )
                                            @foreach ( $descriptions as $description )
                                                @if ( !empty(trim($description->description)) && trim($description->description) != trim($product->short_description) )
                                                    <hr/>
                                                    <span class="same-color">
                                                        {{ ucwords(strtolower(html_entity_decode($description->description))) }}
                                                    </span>
                                                    <p>
                                                        <button class="btn btn-default btn-sm use-description"
                                                                data-id="{{ $product->id }}"
                                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                                            Use this description ({{ $description->website }})
                                                        </button>

                                                        <button class="btn btn-default btn-sm set-description-site"
                                                                data-id="{{ $product->id }}"
                                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                                            Set Description
                                                        </button>
                                                    </p>
                                                @endif
                                            @endforeach
                                            <hr/>
                                        @endif

                                        @php
                                            //getting proper composition and hscode
                                            $composition = $product->commonComposition($product->category , $product->composition);
                                            $hscode =  $product->hsCode($product->category , $product->composition);
                                        @endphp
                                        <p>
                                            <strong class="same-color"
                                                    style="text-decoration: underline;">HsCode</strong>
                                            <br/>
                                            <span class="same-color flex-column">
                                                {{ strtoupper($hscode) }}
                                            </span>
                                        </p>

                                        @if (1==2)
                                            <p>
                                            <span>
                                                <strong>Color</strong>: {{ strtoupper($product->color) }}<br/>
                                            </span>
                                            </p>
                                        @endif

                                        <p>
                                            <strong>Sizes</strong>: {{ $product->size }}<br/>
                                            <strong>Dimension</strong>: {{ \App\Helpers\ProductHelper::getMeasurements($product) }}
                                            <br/>
                                        </p>
                                        <p>
                                            <span class="sololuxury-button">ADD TO BAG</span>
                                            <span class="sololuxury-button"><i class="fa fa-heart"></i> ADD TO WISHLIST</span>
                                        </p>
                                        <p class="same-color">
                                            View All:
                                            <strong>{{ isset($product->product_category->id) ? \App\Category::getCategoryPathById($product->product_category->id)  : '' }}</strong>
                                            <br/>
                                            View All:
                                            <strong>{{ $product->brands ? $product->brands->name : 'N/A' }}</strong>
                                        </p>
                                        <p class="same-color">
                                            <strong>Style ID</strong>: {{ $product->sku }}
                                            <br/>
                                            <strong class="text-danger">{{ $product->is_on_sale ? 'On Sale' : '' }}</strong>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        @if(auth()->user()->isReviwerLikeAdmin('final_listing'))
                                            <p class="text-right mt-5">
                                                <button class="btn btn-xs btn-default edit-product-show"
                                                        data-id="{{$product->id}}">Toggle Edit
                                                </button>
                                                @if ($product->status_id == 9)
                                                    <button type="button"
                                                            class="btn btn-xs btn-secondary upload-magento"
                                                            data-id="{{ $product->id }}" data-type="list">List0
                                                    </button>
                                                @elseif ($product->status_id == 12)
                                                    <button type="button"
                                                            class="btn btn-xs btn-secondary upload-magento"
                                                            data-id="{{ $product->id }}" data-type="update">Update0
                                                    </button>
                                                @endif
                                            </p>
                                        @endif

                                        @php
                                            $logScrapers = \App\ScrapedProducts::where('sku', $product->sku)->where('validated', 1)->get();
                                        @endphp
                                        @if ($logScrapers)
                                            <div>
                                                <br/>
                                                Successfully scraped on the following sites:<br/>
                                                <ul>
                                                    @foreach($logScrapers as $logScraper)
                                                        @if($logScraper->url != "N/A")
                                                            <li><a href="<?= $logScraper->url ?>"
                                                                   target="_blank"><?= $logScraper->website ?></a>
                                                                ( <?= $logScraper->last_inventory_at ?> )
                                                            </li>
                                                        @else
                                                            <li><?= $logScraper->website ?></li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr id="product_{{ $product->id }}" class="">
                            <td>
                                <input type="checkbox" class="affected_checkbox" name="products_to_update[]"
                                       data-id="{{$product->id}}">
                            </td>

                            @php
                                $websiteArraysForProduct = \App\Helpers\ProductHelper::getStoreWebsiteName($product->id);
                            @endphp

                            <td class="table-hover-cell">
                                {{ $product->id }}
                                @if($product->cropped_images_count == count($websiteArraysForProduct))
                                    <span class="badge badge-success" >&nbsp;</span>
                                @else
                                    <span class="badge badge-warning" >&nbsp;</span>
                                @endif
                                <div>
                                    {{ $product->sku }}
                                </div>
                            </td>

                            <td style="word-break: break-all; word-wrap: break-word">
                                <button type="button" class="btn-link quick-view_image__"
                                        data-id="{{ $product->id }}" data-target="#product_image_{{ $product->id }}"
                                        data-toggle="modal">View
                                </button>
                            </td>

                            <td>
                                {{ $product->brands ? $product->brands->name : 'N/A' }}
                            </td>

                            <td class="table-hover-cell">
                                @if (!$imageCropperRole)
                                    {{-- {!! $category_selection !!} --}}
                                    {{--                  {{ $product->pr->title }}--}}
                                    <div class="mt-1">
                                        <select class="form-control quick-edit-category select-multiple"
                                                name="Category" data-placeholder="Category"
                                                data-id="{{ $product->id }}">
                                            <option></option>
                                            @foreach ($category_array as $data)
                                                <option value="{{ $data['id'] }}" {{ $product->category == $data['id'] ? 'selected' : '' }} >{{ $data['title'] }}</option>
                                                @if(isset($data['child']) && is_array($data['child'])) 
                                                    @foreach ($data['child'] as $child)
                                                        <option value="{{ $child['id'] }}" {{ $product->category == $child['id'] ? 'selected' : '' }} >&nbsp;{{ $child['title'] }}</option>
                                                        @if(isset($child['child']) && is_array($child['child'])) 
                                                            @foreach ($child['child'] as $smchild)
                                                                <option value="{{ $smchild['id'] }}" {{ $product->category == $smchild['id'] ? 'selected' : '' }} >&nbsp;&nbsp;{{ $smchild['title'] }}</option>
                                                            @endforeach
                                                        @endif

                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ( isset($product->log_scraper_vs_ai) && $product->log_scraper_vs_ai->count() > 0 )
                                        @foreach ( $product->log_scraper_vs_ai as $resultAi )
                                            @php $resultAi = json_decode($resultAi->result_ai); @endphp
                                            @if ( !empty($resultAi->category) )
                                                <button id="ai-category-{{ $product->id }}" data-id="{{ $product->id }}"
                                                        data-category="{{ \App\LogScraperVsAi::getCategoryIdByKeyword( $resultAi->category, $resultAi->gender, null ) }}"
                                                        class="btn btn-default btn-sm mt-2 ai-btn-category">{{ ucwords(strtolower($resultAi->category)) }}
                                                    (AI)
                                                </button>
                                            @endif
                                        @endforeach
                                    @endif
                                @else



                                @endif
                            </td>

                            <td class="table-hover-cell quick-edit-name" data-id="{{ $product->id }}">
                                @if (!$imageCropperRole)
                                    <span class="quick-name">{{ $product->name }}</span>
                                    {{-- <input type="text" name="name" class="form-control quick-edit-name-input hidden" placeholder="Product Name" value="{{ $product->name }}"> --}}
                                    <input name="text" class="form-control quick-edit-name-input hidden"
                                           placeholder="Product Name" value="{{ $product->name }}">
                                @else

                                    <span>{{ $product->name }}</span>

                                @endif
                            </td>


                            <td class="table-hover-cell quick-edit-description" data-id="{{ $product->id }}">

                                @if (!$imageCropperRole)

                                    <span class="quick-description">{{ $product->short_description}}</span>
                                    <textarea name="description" id="textarea_description_{{ $product->id }}"
                                              class="form-control quick-edit-description-textarea hidden" rows="8"
                                              cols="80">{{ $product->short_description }}</textarea>
                                @else

                                    <span class="short-description-container">{{ substr($product->short_description, 0, 100) . (strlen($product->short_description) > 100 ? '...' : '') }}</span>
                                    <span class="long-description-container hidden">
                                        <span class="description-container">{{ $product->short_description }}</span>
                                    </span>

                                @endif

                            </td>

                            <td class="table-hover-cell" data-id="{{ $product->id }}">
                                @if (!$imageCropperRole)
                                    @php
                                        $arrComposition = ['100% Cotton', '100% Leather', '100% Silk', '100% Wool', '100% Polyester', '100% Acetate', '100% Polyamide', 'Cotton', 'Leather', 'Silk', 'Wool', 'Polyester'];
                                        if(!in_array($product->composition , $arrComposition)){
                                                $arrComposition[] = $product->composition;
                                        }
                                        $i=1;
                                    @endphp
                                    <select class="form-control quick-edit-composition-select select-multiple mt-1"
                                            data-id="{{ $product->id }}"
                                            name="composition" data-placeholder="Composition">
                                        <option></option>
                                        @foreach ($arrComposition as $compositionValue)
                                            <option value="{{ $compositionValue }}" {{ $product->composition == $compositionValue ? 'selected' : '' }}>{{ $compositionValue }}</option>
                                        @endforeach
                                    </select>
                                @else

                                    <span class="quick-composition">{{ $product->composition }}</span>

                                @endif

                            </td>

                            <td class="table-hover-cell">
                                @if (!$imageCropperRole)
                                    <select id="quick-edit-color-{{ $product->id }}"
                                            class="form-control quick-edit-color select-multiple" name="color"
                                            data-id="{{ $product->id }}">
                                        @foreach ($colors as $color)
                                            <option value="{{ $color }}" {{ $product->color == $color ? 'selected' : '' }}>{{ $color }}</option>
                                        @endforeach
                                    </select>x
                                @else

                                    {{ $product->color }}

                                @endif

                            </td>


                            <td class="table-hover-cell">

                                @if (!$imageCropperRole)
                                    {{-- <input type="text" name="other_size" class="form-control mt-3 hidden" placeholder="Manual Size" value="{{ is_array(explode(',', $product->size)) && count(explode(',', $product->size)) > 1 ? '' : $product->size }}"> --}}
                                    <span class="lmeasurement-container">
                  <input type="text" name="measurement" class="form-control mt-1"
                         value="{{ !empty($product->lmeasurement) ? $product->lmeasurement : '' }}x{{ !empty($product->hmeasurement) ? $product->hmeasurement : ' ' }}x{{ !empty($product->dmeasurement) ? $product->dmeasurement : '' }}">
                </span>

                                @endif

                            </td>
                            <td>

                                @php
                                    $size_array = explode(',', $product->size);
                                @endphp

                                {{ isset($size_array[0]) ? $size_array[0] : '' }} {{ isset($size_array[1]) ? ', '.$size_array[1] :  '' }}
                            </td>

                            <td class="table-hover-cell quick-edit-price" data-id="{{ $product->id }}">
                                @if (!$imageCropperRole)
                                    <span class="quick-price">{{ $product->price }}</span>
                                    <input type="number" name="price" class="form-control quick-edit-price-input hidden"
                                           placeholder="100" value="{{ $product->price }}">

                                    {{--                                <span class="quick-price-inr">{{ $product->price }}</span>--}}
                                    {{--                                <span class="quick-price-special">{{ $product->price_eur_special }}</span>--}}

                                @else

                                    <span>EUR {{ $product->price }}</span>
                                    {{--                                <span>EUR {{ $product->price_eur_special }}</span>--}}
                                    {{--                                <span>INR {{ $product->price_inr }}</span>--}}
                                    {{--                                <span>INR {{ $product->price_special }}</span>--}}
                                @endif

                            </td>

                            {{-- <td>
                              @if ($product->hasMedia(config('constants.media_tags')))
                                <a href="{{ route('products.quick.download', $product->id) }}" class="btn btn-xs btn-secondary mb-1 quick-download">Download</a>
                              @endif
                              <input type="file" class="dropify quick-images-upload-input" name="images[]" value="" data-height="100" multiple>
                              <div class="form-inline">
                                <button type="button" class="btn btn-xs btn-secondary mt-1 quick-images-upload" data-id="{{ $product->id }}">Upload</button>
                                @if ($product->last_imagecropper != '')
                                  <img src="/images/1.png" class="ml-1" alt="">
                                @endif
                              </div>
                            </td> --}}

                            <td class="action">
                                <div class="text-center">
                                    {{--                                {{ $product->isUploaded }} {{ $product->isFinal }}--}}
                                </div>
                                @if(auth()->user()->isReviwerLikeAdmin('final_listing'))
                                    @if ($product->is_approved == 0)
                                        {{--                                        <button type="button" class="btn btn-xs btn-secondary upload-magento"--}}
                                        {{--                                                data-id="{{ $product->id }}" data-type="approve">Approve--}}
                                        {{--                                        </button>--}}
                                        <i style="cursor: pointer;" class="fa fa-check upload-magento" title="Approve"
                                           data-id="{{ $product->id }}" data-type="approve" aria-hidden="true"></i>
                                    @elseif ($product->is_approved == 1 && $product->isUploaded == 0)
                                        {{--                                        <button type="button" class="btn btn-xs btn-secondary upload-magento"--}}
                                        {{--                                                data-id="{{ $product->id }}" data-type="list">List--}}
                                        {{--                                        </button>--}}
                                        <i style="cursor: pointer;" class="fa fa-list upload-magento" title="List"
                                           data-id="{{ $product->id }}"
                                           data-type="list" aria-hidden="true"></i>
                                    @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)
                                        {{--                                        <button type="button" class="btn btn-xs btn-secondary upload-magento"--}}
                                        {{--                                                data-id="{{ $product->id }}" data-type="enable">Enable --}}{{--catch--}}
                                        {{--                                        </button>--}}
                                        <i style="cursor: pointer;" class="fa fa-toggle-off upload-magento"
                                           title="Enable"
                                           data-id="{{ $product->id }}" data-type="enable" aria-hidden="true"></i>
                                    @else
                                        {{--                                        <button type="button" class="btn btn-xs btn-secondary upload-magento"--}}
                                        {{--                                                data-id="{{ $product->id }}" data-type="update">Update--}}
                                        {{--                                        </button>--}}
                                        <i style="cursor: pointer;" class="fa fa-pencil upload-magento" title="Update"
                                           data-id="{{ $product->id }}" data-type="update" aria-hidden="true"></i>
                                    @endif
                                    @if ($product->product_user_id != null)
                                        {{ \App\User::find($product->product_user_id)->name }}
                                    @endif

                                    <i style="cursor: pointer;" class="fa fa-upload upload-single"
                                       data-id="{{ $product->id }}" title="push to magento"
                                       aria-hidden="true"></i>


                                @else
                                    {{--                                    <button type="button" class="btn btn-xs btn-secondary upload-magento"--}}
                                    {{--                                            data-id="{{ $product->id }}" data-type="submit_for_approval">Submit For--}}
                                    {{--                                        Approval--}}
                                    {{--                                    </button>--}}
                                    <i style="cursor: pointer;" class="fa fa-toggle-off upload-magento" title="Enable"
                                       data-id="{{ $product->id }}" data-type="submit_for_approval"
                                       aria-hidden="true"></i>
                                @endif

                                {{--                                <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal"--}}
                                {{--                                        data-target="#product_activity_{{ $product->id }}">Activity--}}
                                {{--                                </button>--}}

                                <i style="cursor: pointer;" class="fa fa-tasks" data-toggle="modal" title="Activity"
                                   data-target="#product_activity_{{ $product->id }}" aria-hidden="true"></i>
                                {{--                                <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal"--}}
                                {{--                                        data-target="#product_scrape_{{ $product->id }}">Scrape--}}
                                {{--                                </button>--}}

                                <i style="cursor: pointer;" class="fa fa-trash" data-toggle="modal" title="Scrape"
                                   data-target="#product_scrape_{{ $product->id }}" aria-hidden="true"></i>


                            </td>
                            <td>
                                {{--                                <input type="checkbox" name="reject_{{$product->id}}" id="reject_{{$product->id}}">--}}
                                {{--                                Reject<br/>--}}
                                <select class="form-control post-remark" id="post_remark_{{$product->id}}"
                                        data-id="{{$product->id}}" data-placeholder="Select Remark">
                                    <option></option>
                                    <option value="Category Incorrect" {{ $product->listing_remark == 'Category Incorrect' ? 'selected' : '' }} >
                                        Category Incorrect
                                    </option>
                                    <option value="Price Not Incorrect" {{ $product->listing_remark == 'Price Not Incorrect' ? 'selected' : '' }} >
                                        Price Not Correct
                                    </option>
                                    <option value="Price Not Found" {{ $product->listing_remark == 'Price Not Found' ? 'selected' : '' }} >
                                        Price Not Found
                                    </option>
                                    <option value="Color Not Found" {{ $product->listing_remark == 'Color Not Found' ? 'selected' : '' }} >
                                        Color Not Found
                                    </option>
                                    <option value="Category Not Found" {{ $product->listing_remark == 'Category Not Found' ? 'selected' : '' }} >
                                        Category Not Found
                                    </option>
                                    <option value="Description Not Found" {{ $product->listing_remark == 'Description Not Found' ? 'selected' : '' }} >
                                        Description Not Found
                                    </option>
                                    <option value="Details Not Found" {{ $product->listing_remark == 'Details Not Found' ? 'selected' : '' }} >
                                        Details Not Found
                                    </option>
                                    <option value="Composition Not Found" {{ $product->listing_remark == 'Composition Not Found' ? 'selected' : '' }} >
                                        Composition Not Found
                                    </option>
                                    <option value="Crop Rejected" {{ $product->listing_remark == 'Crop Rejected' ? 'selected' : '' }} >
                                        Crop Rejected
                                    </option>
                                    <option value="Other">Other</option>
                                </select>
                                {{--                                <textarea name="remark-input-{{$product->id}}" id="remark-input-{{$product->id}}"--}}
                                {{--                                          class="form-control remark-input-post" data-id="{{$product->id}}"--}}
                                {{--                                          style="display: none;"></textarea>--}}

                                {{--                                <div class="mt-3">--}}
                                {{--                                    <input class="form-control send-message" data-sku="{{$product->sku}}"--}}
                                {{--                                           type="text" placeholder="Message..."--}}
                                {{--                                           id="message_{{$product->approved_by}}"--}}
                                {{--                                           data-id="{{$product->approved_by}}">--}}
                                {{--                                </div>--}}

                                {{--                <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $product->id }}"><img src="/images/remark.png" /></button>--}}
                            </td>
                            <td>
                                <select class="form-control select-multiple approved_by" name="approved_by"
                                        id="approved_by" data-id="{{ $product->id }}" data-placeholder="Select user">
                                    <option></option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}" {{ $product->approved_by == $user->id ? 'selected' : '' }} >{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="mb-5">
                    <button class="btn btn-secondary text-left mass_action delete_checked_products">DELETE</button>
                    <button class="btn btn-secondary text-left mass_action approve_checked_products">APPROVE</button>
                    <button style="float: right" class="btn btn-secondary text-right">UPLOAD ALL</button>
                </div>
                <p class="mb-5">
                    &nbsp;
                </p>
            </div>
            <?php echo $products->appends(request()->except("page"))->links(); ?>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />

        </div>
    </div>

    @foreach ($products as $key => $product)

        <div id="product_activity_{{ $product->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Activity</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>User Name</th>
                                <th>Status</th>
                            </tr>
                            <tr>
                                <th>Cropping</th>
                                <td>{{ $product->crop_approved_at ?? 'N/A' }}</td>
                                <td>
                                    {{ $product->cropApprover ? $product->cropApprover->name : 'N/A' }}
                                </td>
                                <td>
                                    <select style="width: 90px !important;" data-id="{{$product->id}}"
                                            class="form-control-sm form-control reject-cropping bg-secondary text-light"
                                            name="reject_cropping"
                                            id="reject_cropping_{{$product->id}}">
                                        <option value="0">Select...</option>
                                        <option value="Images Not Cropped Correctly">Images Not Cropped
                                            Correctly
                                        </option>
                                        <option value="No Images Shown">No Images Shown</option>
                                        <option value="Grid Not Shown">Grid Not Shown</option>
                                        <option value="Blurry Image">Blurry Image</option>
                                        <option value="First Image Not Available">First Image Not
                                            Available
                                        </option>
                                        <option value="Dimension Not Available">Dimension Not
                                            Available
                                        </option>
                                        <option value="Wrong Grid Showing For Category">Wrong Grid
                                            Showing For Category
                                        </option>
                                        <option value="Incorrect Category">Incorrect Category</option>
                                        <option value="Only One Image Available">Only One Image
                                            Available
                                        </option>
                                        <option value="Image incorrect">Image incorrect</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>Sequencing</th>
                                <td>{{ $product->crop_ordered_at ?? 'N/A' }}</td>
                                <td>{{ $product->cropOrderer ? $product->cropOrderer->name : 'N/A' }}</td>
                                <td>
                                    <button style="width: 90px" data-button-type="sequence"
                                            data-id="{{$product->id}}"
                                            class="btn btn-secondary btn-sm reject-sequence">Reject
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>Approval</th>
                                <td>{{ $product->listing_approved_at ?? 'N/A' }}</td>
                                <td>{{ $product->approver ? $product->approver->name : 'N/A' }}</td>
                                <td>
                                    <select style="width: 90px !important;" data-id="{{$product->id}}"
                                            class="form-control-sm form-control reject-listing bg-secondary text-light"
                                            name="reject_listing" id="reject_listing_{{$product->id}}">
                                        <option value="0">Select Remark</option>
                                        <option value="Category Incorrect">Category Incorrect</option>
                                        <option value="Price Not Incorrect">Price Not Correct</option>
                                        <option value="Price Not Found">Price Not Found</option>
                                        <option value="Color Not Found">Color Not Found</option>
                                        <option value="Category Not Found">Category Not Found</option>
                                        <option value="Description Not Found">Description Not Found
                                        </option>
                                        <option value="Details Not Found">Details Not Found</option>
                                        <option value="Composition Not Found">Composition Not Found
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            @php
                                // Set opener URL
                                $openerUrl = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI']);
                            @endphp
                            @if ( isset($product->log_scraper_vs_ai) && $product->log_scraper_vs_ai->count() > 0 )
                                <tr>
                                    <th>AI</th>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <button style="width: 90px" class="btn btn-secondary btn-sm"
                                                data-toggle="modal" id="linkAiModal{{ $product->id }}"
                                                data-target="#aiModal{{ $product->id }}">AI result
                                        </button>
                                        <div class="modal fade" id="aiModal{{ $product->id }}"
                                             tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog modal-lg"
                                                 role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">{{ strtoupper($product->name) }}</h4>
                                                        <button type="button" class="close"
                                                                data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe id="aiModalLoad{{ $product->id }}"
                                                                frameborder="0" border="0" width="100%"
                                                                height="800"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $('#linkAiModal{{ $product->id }}').click(function () {
                                                $('#aiModalLoad{{ $product->id }}').attr('src', '/log-scraper-vs-ai/{{ $product->id }}?opener={{ $openerUrl }}');
                                            });
                                        </script>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div id="product_scrape_{{ $product->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Scraped sites</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @php
                            $logScrapers = \App\ScrapedProducts::where('sku', $product->sku)->where('validated', 1)->get();
                        @endphp
                        @if ($logScrapers)
                            <div>
                                <ul>
                                    @foreach($logScrapers as $logScraper)
                                        @if($logScraper->url != "N/A")
                                            <li><a href="{!! $logScraper->url  !!}"
                                                   target="_blank">{!! $logScraper->website  !!} </a>
                                                ( {!! $logScraper->last_inventory_at  !!} )
                                            </li>
                                        @else
                                            <li>{!! $logScraper->website  !!}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <div id="product_image_{{ $product->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Images</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @foreach($store_websites as $index => $site)
                            <div class="product-slider {{$index == 0 ? 'd-block' : 'd-none'}}">
                                <div class="col-md-5">
                                    @php
                                        $product = \App\Product::find($product->id)
                                    @endphp
                                    <?php $gridImage = ''; ?>
                                    @if ($product->hasMedia(config('constants.media_gallery_tag')))
                                        @foreach($product->getMedia(config('constants.media_gallery_tag')) as $media)
                                            @if(strpos($media->filename, 'crop') !== false && $product->tag == 'gallery_'.$site->cropper_color)
                                                <?php
                                                $width = 0;
                                                $height = 0;
                                                if (file_exists($media->getAbsolutePath())) {
                                                    list($width, $height) = getimagesize($media->getAbsolutePath());
                                                    $badge = "notify-red-badge";
                                                    if ($width == 1000 && $height == 1000) {
                                                        $badge = "notify-green-badge";
                                                    }
                                                } else {
                                                    $badge = "notify-red-badge";
                                                }
                                                // Get cropping grid image
                                                $gridImage = \App\Category::getCroppingGridImageByCategoryId($product->category);
                                                if ($width == 1000 && $height == 1000) {
                                                ?>
                                                <div class="thumbnail-pic">
                                                    <div class="thumbnail-edit"><a class="delete-thumbail-img"
                                                                                   data-product-id="{{ $product->id }}"
                                                                                   data-media-id="{{ $media->id }}"
                                                                                   data-media-type="gallery"
                                                                                   href="javascript:;"><i
                                                                    class="fa fa-trash fa-lg"></i></a></div>
                                                    <span class="notify-badge {{$badge}}">{{ $width."X".$height}}</span>
                                                    <img style="display:block; width: 70px; height: 80px; margin-top: 5px;"
                                                         src="{{ $media->getUrl() }}"
                                                         class="quick-image-container img-responive" alt=""
                                                         data-toggle="tooltip" data-placement="top"
                                                         title="ID: {{ $product->id }}"
                                                         onclick="replaceThumbnail('{{ $product->id }}','{{ $media->getUrl() }}','{{$gridImage}}')">
                                                </div>
                                                <?php } ?>
                                            @endif
                                        @endforeach
                                    @endif
                                    <div>

                                        <div class="form-group">
                                            <input type="radio" id="approve_site_color" name="image_status"
                                                   value="approve" data-site_id="{{$site->id}}" data-product_id="{{$product->id}}"
                                                    {{isset($product->cropped_image_status) && $product->cropped_image_status !== null && $product->cropped_image_status == 1 && $product->website_id == $site->id ? 'checked' : ''}}>
                                            <lable for="approve_site_color pr-3">Approve</lable>
                                        </div>
                                        <div class="form-group">
                                            <input type="radio" id="reject_site_color" name="image_status" value="reject"
                                                   data-site_id="{{$site->id}}" data-product_id="{{$product->id}}"
                                                    {{isset($product->cropped_image_status) && $product->cropped_image_status !== null && $product->cropped_image_status == 0 && $product->website_id == $site->id ? 'checked' : ''}}>

                                            <lable for="reject_site_color pr-3">Reject</lable>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7" id="col-large-image{{ $product->id }}">
                                    @if ($product->hasMedia(config('constants.media_gallery_tag')))
                                        <div onclick="bigImg('{{ $product->getMedia(config('constants.media_gallery_tag'))->first()->getUrl() }}')"
                                             style=" margin-bottom: 5px; width: 300px;height: 300px; background-image: url('{{ $product->getMedia(config('constants.media_gallery_tag'))->first()->getUrl() }}'); background-size: 300px"
                                             id="image{{ $product->id }}">
                                            <img style="width: 300px;" src="{{ asset('images/'.$gridImage) }}"
                                                 class="quick-image-container img-responive" style="width: 100%;"
                                                 alt="" data-toggle="tooltip" data-placement="top"
                                                 title="ID: {{ $product->id }}" id="image-tag{{ $product->id }}">
                                        </div>
                                        <button onclick="cropImage('{{ $product->getMedia(config('constants.media_gallery_tag'))->first()->getUrl() }}','{{ $product->id }}')"
                                                class="btn btn-secondary">Crop Image
                                        </button>
                                        <button onclick="crop('{{ $product->getMedia(config('constants.media_gallery_tag'))->first()->getUrl() }}','{{ $product->id }}','{{ $gridImage }}')"
                                                class="btn btn-secondary">Crop
                                        </button>

                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center">

                            <i style="cursor: pointer;" class="fa fa-arrow-left product-slider-arrow-left"></i>
                            <i style="cursor: pointer;" class="fa fa-arrow-right product-slider-arrow"></i>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div id="description_modal_view_{{ $product->id }}" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content ">
                    <div class="modal-header">
                        <h4 class="modal-title">Description</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>
                            {{--                            <strong class="same-color"--}}
                            {{--                                    style="text-decoration: underline">Description</strong>--}}
                            {{--                            <br/>--}}
                            <span id="description{{ $product->id }}" class="same-color">
                                                {{ ucwords(strtolower(html_entity_decode($product->short_description))) }}
                                            </span>
                        </p>
                        <br/>
                        @php
                            $descriptions = \App\ScrapedProducts::select('description','website')->where('sku', $product->sku)->get();
                        @endphp
                        @if ( $descriptions->count() > 0 )
                            @foreach ( $descriptions as $description )
                                @if ( !empty(trim($description->description)) && trim($description->description) != trim($product->short_description) )
                                    <hr/>
                                    <span class="same-color">
                                                        {{ ucwords(strtolower(html_entity_decode($description->description))) }}
                                                    </span>
                                    <p>
                                        <button class="btn btn-default btn-sm use-description"
                                                data-id="{{ $product->id }}"
                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                            Use this description ({{ $description->website }})
                                        </button>

                                        <button class="btn btn-default btn-sm set-description-site"
                                                data-id="{{ $product->id }}"
                                                data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">
                                            Set Description
                                        </button>
                                    </p>
                                @endif
                            @endforeach
                            <hr/>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach


    @include('partials.modals.remarks')
    @include('partials.modals.image-expand')
    @include('partials.modals.set-description-site-wise')

@endsection

@section('scripts')
    <script>
        function pushProduct() {
            $.ajax({
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{csrf_token()}}"
                },
                cache: false,
                contentType: false,
                processData: false,
                url: "{{ url('products/listing/final/pushproduct') }}",
                success: function (html) {
                    swal(html.message);
                }
            })
        }
    </script>
    <style>
        .same-color {
            color: #898989;
            font-size: 14px;
        }
        .sololuxury-button {
            display: inline-block;
            color: #898989;
            font-size: 14px;
            border: 1px solid #898989;
            background: #FFF;
            padding: 5px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script type="text/javascript">
        var categoryJson = <?php echo json_encode($category_array); ?>;
        $(document).on('change', '.category_level_1', function () {
            var this_ = $(this);
            var category_id = $(this).val();
            categoryJson.forEach(function (category, index) {
                if (category.id == category_id) {
                    var html = "";
                    category.child.forEach(function (child, i) {
                        html += '<option value="' + child.id + '">' + child.title + '</option>';
                    });
                    this_.closest('tr').find('.category_level_2').html(html);
                }
            })
        });
        $(document).on('change', '.category_level_2', function () {
            var this_ = $(this);
            var category_id = $(this).val();
            categoryJson.forEach(function (category, index) {
                category.child.forEach(function (children, i) {
                    if (children.id == category_id) {
                        var html = "";
                        children.child.forEach(function (child, i) {
                            html += '<option value="' + child.id + '">' + child.title + '</option>';
                        });
                        this_.closest('tr').find('.quick-edit-category').html(html);
                    }
                });
            })
        });
        var productIds = [
            @foreach ( $products as $product )
            {{ $product->id }},
            @endforeach
        ];
        function removeIdFromArray(id) {
            for (var i = 0; i < productIds.length; i++) {
                if (productIds[i] === id) {
                    productIds.splice(i, 1);
                    $('#product' + id).hide();
                }
            }
            console.log(productIds);
        }
        $(document).on('keyup', '.send-message', function (event) {
            let userId = $(this).data('id');
            let message = $(this).val();
            let sku = $(this).data('sku');
            let self = this;
            if (event.which != 13) {
                return;
            }
            $.ajax({
                url: '{{ action('WhatsAppController@sendMessage', 'vendor') }}',
                type: 'POST',
                data: {
                    vendor_id: userId,
                    message: 'SKU - ' + sku + '-' + message,
                    is_vendor_user: 'yes',
                    status: 1
                },
                success: function () {
                    $(self).val('');
                    toastr['success']('Message sent successfully', 'Success')
                }
            });
        });
        $(document).on('click', '.edit-product-show', function () {
            let id = $(this).data('id');
            $('#product_' + id).toggleClass('hidden');
        });
        $(document).on('click', '.reject-sequence', function (event) {
            let pid = $(this).data('id');
            $.ajax({
                url: '/reject-sequence/' + pid,
                data: {
                    senior: 1
                },
                success: function () {
                    toastr['success']('Sequence rejected successfully!', 'Success');
                    removeIdFromArray(pid);
                },
                error: function () {
                    toastr['error']('Error rejecting sequence', 'Success');
                }
            });
        });
        $(document).on('click', '.crop-approval-confirmation', function (event) {
            let pid = $(this).data('id');
            $.ajax({
                url: '/products/auto-cropped/' + pid + '/crop-approval-confirmation',
                data: {
                    _token: "{{csrf_token()}}",
                },
                type: 'GET',
                success: function () {
                    toastr['success']('Crop approval successfully confirmed!', 'Success');
                    $('#approve_cropping_' + pid).hide();
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled');
                }
            });
        });
        $(document).on('change', '.reject-cropping', function (event) {
            let pid = $(this).data('id');
            let remark = $(this).val();
            if (remark == 0 || remark == '0') {
                return;
            }
            let self = this;
            $.ajax({
                url: '/products/auto-cropped/' + pid + '/reject',
                data: {
                    remark: remark,
                    _token: "{{csrf_token()}}",
                    senior: 1
                },
                type: 'GET',
                success: function () {
                    toastr['success']('Crop rejected successfully!', 'Success');
                    removeIdFromArray(pid);
                    $(self).removeAttr('disabled');
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled');
                }
            });
        });
        {{--$(document).on('change', '.reject-listing', function (event) {--}}
        {{--    let pid = $(this).data('id');--}}
        {{--    let remark = $(this).val();--}}
        {{--    if (remark == 0 || remark == '0') {--}}
        {{--        return;--}}
        {{--    }--}}
        {{--    let self = this;--}}
        {{--    $.ajax({--}}
        {{--        url: '{{action('ProductController@addListingRemarkToProduct')}}',--}}
        {{--        data: {--}}
        {{--            product_id: pid,--}}
        {{--            remark: remark,--}}
        {{--            rejected: 1,--}}
        {{--            senior: 1--}}
        {{--        },--}}
        {{--        success: function (response) {--}}
        {{--            toastr['success']('Product rejected successfully!', 'Rejected');--}}
        {{--            $(self).removeAttr('disabled');--}}
        {{--            $(self).val();--}}
        {{--            removeIdFromArray(pid);--}}
        {{--        },--}}
        {{--        beforeSend: function () {--}}
        {{--            $(self).attr('disabled');--}}
        {{--        }, error: function () {--}}
        {{--            $(self).removeAttr('disabled');--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
        var page = 1;
        var isLoadingProducts;
        $(document).ready(function () {
            // $('ul.pagination').hide();
            // $(function () {
                // $('.infinite-scroll').jscroll({
                //     autoTrigger: true,
                //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                //     padding: 2500,
                //     nextSelector: '.pagination li.active + li a',
                //     contentSelector: 'div.infinite-scroll',
                //     callback: function () {
                        // $('ul.pagination').remove();
                        // $('.dropify').dropify();
                        // $('.quick-edit-category').each(function (item) {
                        //     product_id = $(this).siblings('input[name="product_id"]').val();
                        //     category_id = $(this).siblings('input[name="category_id"]').val();
                        //     sizes = $(this).siblings('input[name="sizes"]').val();
                        //     selected_sizes = sizes.split(',');
                        //
                        //     $(this).attr('data-id', product_id);
                        //     var this_ = $(this);
                        //     categoryJson.forEach(function (category, index) {
                        //         if (category.id == category_id) {
                        //             this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true)
                        //             this_.closest('tr').find('.category_level_1').trigger("change");
                        //         }
                        //
                        //         category.child.forEach(function (children, i) {
                        //             if (children.id == category_id) {
                        //                 this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true);
                        //                 this_.closest('tr').find('.category_level_1').trigger("change");
                        //                 this_.closest('tr').find('.category_level_2').find('option[value="' + category_id + '"]').prop('selected', true);
                        //                 this_.closest('tr').find('.category_level_2').trigger("change");
                        //             }
                        //
                        //             children.child.forEach(function (child, i) {
                        //                 if (child.id == category_id) {
                        //                     this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true);
                        //                     this_.closest('tr').find('.category_level_1').trigger("change");
                        //                     this_.closest('tr').find('.category_level_2').find('option[value="' + children.id + '"]').prop('selected', true);
                        //                     this_.closest('tr').find('.category_level_2').trigger("change");
                        //                 }
                        //             });
                        //         });
                        //     });
                        //
                        //     $(this).find('option[value="' + category_id + '"]').prop('selected', true);
                        //
                        //     updateSizes(this, category_id);
                        //
                        //     for (var i = 0; i < selected_sizes.length; i++) {
                        //         console.log(selected_sizes[i]);
                        //         // $(this).closest('tr').find('.quick-edit-size option[value="' + selected_sizes[i] + '"]').attr('selected', 'selected');
                        //         $(this).closest('tr').find(".quick-edit-size option[value='" + selected_sizes[i] + "']").attr('selected', 'selected');
                        //     }
                        // });
                    // }
                // });
            // });
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMoreProducts();
                }
            });

            function loadMoreProducts() {
                if (isLoadingProducts)
                    return;
                isLoadingProducts = true;
                if(!$('.pagination li.active + li a').attr('href'))
                return;

                var $loader = $('.infinite-scroll-products-loader');
                $.ajax({
                    url: $('.pagination li.active + li a').attr('href'),
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                        $('ul.pagination').remove();
                    }
                })
                .done(function(data) {
                    if('' === data.trim())
                        return;

                    $loader.hide();

                    $('.infinite-scroll-data').append(data);

                    isLoadingProducts = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
            }
            $('.dropify').dropify();
            // $(".select-multiple").multiselect();
            $(".select-multiple").select2({
                minimumResultsForSearch: -1
            });
            $("body").tooltip({selector: '[data-toggle=tooltip]'});
        });
        var category_tree = {!! json_encode($category_tree) !!};
        var categories_array = {!! json_encode($categories_array) !!};
        var id_list = {
            41: ['34', '34.5', '35', '35.5', '36', '36.5', '37', '37.5', '38', '38.5', '39', '39.5', '40', '40.5', '41', '41.5', '42', '42.5', '43', '43.5', '44'], // Women Shoes
            5: ['34', '34.5', '35', '35.5', '36', '36.5', '37', '37.5', '38', '38.5', '39', '39.5', '40', '40.5', '41', '41.5', '42', '42.5', '43', '43.5', '44'], // Men Shoes
            40: ['36-36S', '38-38S', '40-40S', '42-42S', '44-44S', '46-46S', '48-48S', '50-50S'], // Women Clothing
            12: ['36-36S', '38-38S', '40-40S', '42-42S', '44-44S', '46-46S', '48-48S', '50-50S'], // Men Clothing
            63: ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXL'], // Women T-Shirt
            31: ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXL'], // Men T-Shirt
            120: ['24-24S', '25-25S', '26-26S', '27-27S', '28-28S', '29-29S', '30-30S', '31-31S', '32-32S'], // Women Sweat Pants
            123: ['24-24S', '25-25S', '26-26S', '27-27S', '28-28S', '29-29S', '30-30S', '31-31S', '32-32S'], // Women Pants
            128: ['24-24S', '25-25S', '26-26S', '27-27S', '28-28S', '29-29S', '30-30S', '31-31S', '32-32S'], // Women Denim
            130: ['24-24S', '25-25S', '26-26S', '27-27S', '28-28S', '29-29S', '30-30S', '31-31S', '32-32S'], // Men Denim
            131: ['24-24S', '25-25S', '26-26S', '27-27S', '28-28S', '29-29S', '30-30S', '31-31S', '32-32S'], // Men Sweat Pants
            42: ['60', '65', '70', '75', '80', '85', '90', '95', '100', '105', '110', '115', '120'], // Women Belts
            14: ['60', '65', '70', '75', '80', '85', '90', '95', '100', '105', '110', '115', '120'], // Men Belts
        };
        var product_id = '';
        var category_id = '';
        var sizes = '';
        var selected_sizes = [];
        // $('.quick-edit-category').each(function (item) {
        //     product_id = $(this).siblings('input[name="product_id"]').val();
        //     category_id = $(this).siblings('input[name="category_id"]').val();
        //     sizes = $(this).siblings('input[name="sizes"]').val();
        //     selected_sizes = sizes.split(',');
        //
        //     $(this).attr('data-id', product_id);
        //
        //     var this_ = $(this);
        //     categoryJson.forEach(function (category, index) {
        //         if (category.id == category_id) {
        //             this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true)
        //             this_.closest('tr').find('.category_level_1').trigger("change");
        //         }
        //
        //         category.child.forEach(function (children, i) {
        //             if (children.id == category_id) {
        //                 this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true);
        //                 this_.closest('tr').find('.category_level_1').trigger("change");
        //                 this_.closest('tr').find('.category_level_2').find('option[value="' + category_id + '"]').prop('selected', true);
        //                 this_.closest('tr').find('.category_level_2').trigger("change");
        //             }
        //
        //             children.child.forEach(function (child, i) {
        //                 if (child.id == category_id) {
        //                     this_.closest('tr').find('.category_level_1').find('option[value="' + category.id + '"]').prop('selected', true);
        //                     this_.closest('tr').find('.category_level_1').trigger("change");
        //                     this_.closest('tr').find('.category_level_2').find('option[value="' + children.id + '"]').prop('selected', true);
        //                     this_.closest('tr').find('.category_level_2').trigger("change");
        //                 }
        //             });
        //         });
        //     });
        //
        //     $(this).find('option[value="' + category_id + '"]').prop('selected', true);
        //
        //     updateSizes(this, category_id);
        //
        //     for (var i = 0; i < selected_sizes.length; i++) {
        //         $(this).closest('tr').find(".quick-edit-size option[value='" + selected_sizes[i] + "']").attr('selected', 'selected');
        //     }
        // });
        $(document).on('click', '.edit-task-button', function () {
            var task = $(this).data('task');
            var url = "{{ url('development') }}/" + task.id + "/edit";
            @if(auth()->user()->checkPermission('development-list'))
            $('#user_field').val(task.user_id);
            @endif
            $('#priority_field').val(task.priority);
            $('#task_field').val(task.task);
            $('#task_subject').val(task.subject);
            $('#cost_field').val(task.cost);
            $('#status_field').val(task.status);
            $('#estimate_time_field').val(task.estimate_time);
            $('#start_time_field').val(task.start_time);
            $('#end_time_field').val(task.end_time);
            $('#editTaskForm').attr('action', url);
        });
        $(document).on('click', '.quick-edit-name', function () {
            var id = $(this).data('id');
            $(this).closest('td').find('.quick-name').addClass('hidden');
            $(this).closest('td').find('.quick-edit-name-input').removeClass('hidden');
            $(this).closest('td').find('.quick-edit-name-input').focus();
            $(this).closest('td').find('.quick-edit-name-input').keypress(function (e) {
                var key = e.which;
                var thiss = $(this);
                if (key == 13) {
                    e.preventDefault();
                    var name = $(thiss).val();
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('products') }}/" + id + '/updateName',
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: name,
                        }
                    }).done(function () {
                        $(thiss).addClass('hidden');
                        $(thiss).siblings('.quick-name').text(name);
                        $(thiss).siblings('.quick-name').removeClass('hidden');
                    }).fail(function (response) {
                        console.log(response);
                        alert('Could not update name');
                    });
                }
            });
        });
        $(document).on('click', '.btn-composition', function () {
            var id = $(this).data('id');
            var composition = $(this).data('value');
            var thiss = $(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('products') }}/" + id + '/updateComposition',
                data: {
                    _token: "{{ csrf_token() }}",
                    composition: composition,
                }
            }).done(function () {
                $(thiss).addClass('hidden');
                $(thiss).siblings('.quick-composition').text(composition);
                $(thiss).siblings('.quick-composition').removeClass('hidden');
            }).fail(function (response) {
                console.log(response);
                alert('Could not update composition');
            });
        });
        $(document).on('change', '.quick-edit-composition-select', function () {
            var id = $(this).data('id');
            var $this = $(this);
            $.ajax({
                type: 'POST',
                url: "{{ url('products') }}/" + id + '/updateComposition',
                data: {
                    _token: "{{ csrf_token() }}",
                    composition: $(this).val(),
                }
            }).done(function () {
                $this.addClass('hidden');
                $this.siblings('.quick-composition').text(composition);
                $this.siblings('.quick-composition').removeClass('hidden');
            }).fail(function (response) {
                alert('Could not update composition');
            });
        });
        $(document).on('click', '.quick-edit-composition', function () {
            var id = $(this).data('id');
            $(this).closest('td').find('.quick-composition').addClass('hidden');
            $(this).closest('td').find('.quick-edit-composition-input').removeClass('hidden');
            $(this).closest('td').find('.quick-edit-composition-input').focus();
            $(this).closest('td').find('.quick-edit-composition-input').keypress(function (e) {
                var key = e.which;
                var thiss = $(this);
                if (key == 13) {
                    e.preventDefault();
                    var composition = $(thiss).val();
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('products') }}/" + id + '/updateComposition',
                        data: {
                            _token: "{{ csrf_token() }}",
                            composition: composition,
                        }
                    }).done(function () {
                        $(thiss).addClass('hidden');
                        $(thiss).siblings('.quick-composition').text(composition);
                        $(thiss).siblings('.quick-composition').removeClass('hidden');
                    }).fail(function (response) {
                        console.log(response);
                        alert('Could not update composition');
                    });
                }
            });
        });
        $(document).on('change', '.quick-edit-color', function () {
            var color = $(this).val();
            var id = $(this).data('id');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('products') }}/" + id + '/updateColor',
                data: {
                    _token: "{{ csrf_token() }}",
                    color: color
                }
            }).done(function () {
                $(thiss).css({border: "2px solid green"});
                setTimeout(function () {
                    $(thiss).css({border: "1px solid #ccc"});
                }, 2000);
            }).fail(function (response) {
                alert('Could not update the color');
                console.log(response);
            });
        });
        $(document).on('click', '.ai-btn-color', function () {
            var color = $(this).data('value');
            var id = $(this).data('id');
            var btnclicked = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('products') }}/" + id + '/updateColor',
                data: {
                    _token: "{{ csrf_token() }}",
                    color: color
                }
            }).done(function () {
                $(btnclicked).css({border: "2px solid green"});
                $('#quick-edit-color-' + id).val(color);
                setTimeout(function () {
                    $(btnclicked).css({border: "1px solid #ccc"});
                }, 3000);
            }).fail(function (response) {
                alert('Could not update the color');
                console.log(response);
            });
        });
        $(document).on('change', '.quick-edit-category', function () {
            var category = $(this).val();
            var id = $(this).data('id');
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('products') }}/" + id + '/updateCategory',
                data: {
                    _token: "{{ csrf_token() }}",
                    category: category
                }
            }).done(function () {
                $(thiss).css({border: "2px solid green"});
                setTimeout(function () {
                    $(thiss).css({border: "1px solid #ccc"});
                }, 2000);
            }).fail(function (response) {
                alert('Could not update the category');
                console.log(response);
            });
            updateSizes(thiss, $(thiss).val());
        });
        $(document).on('click', '.ai-btn-category', function () {
            var category = $(this).data('category');
            var id = $(this).data('id');
            var btnclicked = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('products') }}/" + id + '/updateCategory',
                data: {
                    _token: "{{ csrf_token() }}",
                    category: category
                }
            }).done(function () {
                $(btnclicked).css({border: "2px solid green"});
                $('#quick-edit-category-' + id).val(category);
                setTimeout(function () {
                    $(btnclicked).css({border: "1px solid #ccc"});
                }, 3000);
            }).fail(function (response) {
                alert('Could not update the category');
                console.log(response);
            });
            updateSizes(thiss, $(thiss).val());
        });
        $(document).on('click', '.quick-edit-size-button', function () {
            var size = $(this).siblings('.quick-edit-size').val();
            // var other_size = $(this).siblings('input[name="other_size"]').val();
            var data_ = $(this).closest('td').find('input[name="measurement"]').val();
            data_ = data_.slice('x');
            var lmeasurement = data_[0];
            var hmeasurement = data_[1];
            var dmeasurement = data_[2];
            var id = $(this).data('id');
            var thiss = $(this);
            console.log(size);
            $.ajax({
                type: "POST",
                url: "{{ url('products') }}/" + id + '/updateSize',
                data: {
                    _token: "{{ csrf_token() }}",
                    size: size,
                    lmeasurement: lmeasurement,
                    hmeasurement: hmeasurement,
                    dmeasurement: dmeasurement
                },
                beforeSend: function () {
                    $(thiss).text('Saving...');
                }
            }).done(function () {
                $(thiss).text('Save');
                $(thiss).css({color: "green"});
                setTimeout(function () {
                    $(thiss).css({color: "inherit"});
                }, 2000);
            }).fail(function (response) {
                $(thiss).text('Save');
                alert('Could not update the category');
                console.log(response);
            });
        });
        $(document).on('dblclick', '.quick-edit-price', function () {
            var id = $(this).data('id');
            $(this).find('.quick-price').addClass('hidden');
            $(this).find('.quick-edit-price-input').removeClass('hidden');
            $(this).find('.quick-edit-price-input').focus();
            $(this).find('.quick-edit-price-input').keypress(function (e) {
                var key = e.which;
                var thiss = $(this);
                if (key == 13) {
                    e.preventDefault();
                    var price = $(thiss).val();
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('products') }}/" + id + '/updatePrice',
                        data: {
                            _token: "{{ csrf_token() }}",
                            price: price,
                        }
                    }).done(function (response) {
                        $(thiss).addClass('hidden');
                        $(thiss).siblings('.quick-price').text(price);
                        $(thiss).siblings('.quick-price').removeClass('hidden');
                        $(thiss).siblings('.quick-price-inr').text(response.price_inr);
                        $(thiss).siblings('.quick-price-special').text(response.price_special);
                    }).fail(function (response) {
                        console.log(response);
                        alert('Could not update price');
                    });
                }
            });
        });
        $(document).on('click', '.quick-images-upload', function () {
            var id = $(this).data('id');
            var thiss = $(this);
            var images = $(this).closest('td').find('input[type="file"]').prop('files');
            var images_array = [];
            var form_data = new FormData();
            console.log(images);
            console.log($(this).closest('td').find('input[type="file"]'));
            form_data.append('_token', "{{ csrf_token() }}");
            Object.keys(images).forEach(function (index) {
                form_data.append('images[]', images[index]);
            });
            $.ajax({
                type: 'POST',
                url: "{{ url('products') }}/" + id + '/quickUpload',
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',
                data: form_data
            }).done(function (response) {
                $(thiss).closest('tr').find('.quick-image-container').attr('src', response.image_url);
                $(thiss).closest('td').find('.dropify-clear').click();
                $(thiss).parent('div').find('img').remove();
                $(thiss).parent('div').append('<img src="/images/1.png" class="ml-1" alt="">');
            }).fail(function (response) {
                console.log(response);
                alert('Could not upload images');
            });
        });
        $(document).on('click', '.read-more-button', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.short-description-container').toggleClass('hidden');
                $(this).find('.long-description-container').toggleClass('hidden');
            }
        });
        $(document).on('click', '.quick-description-edit-textarea', function (e) {
            e.stopPropagation();
        });
        $(document).on('click', '.quick-edit-description', function (e) {
            e.stopPropagation();
            var id = $(this).data('id');
            $(this).siblings('.long-description-container').removeClass('hidden');
            $(this).siblings('.short-description-container').addClass('hidden');
            $(this).siblings('.long-description-container').find('.description-container').addClass('hidden');
            $(this).siblings('.long-description-container').find('.quick-description-edit-textarea').removeClass('hidden');
            $(this).siblings('.long-description-container').find('.quick-description-edit-textarea').keypress(function (e) {
                var key = e.which;
                var thiss = $(this);
                if (key == 13) {
                    e.preventDefault();
                    var description = $(thiss).val();
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('products') }}/" + id + '/updateDescription',
                        data: {
                            _token: "{{ csrf_token() }}",
                            description: description,
                        }
                    }).done(function () {
                        $(thiss).addClass('hidden');
                        $(thiss).siblings('.description-container').text(description);
                        $(thiss).siblings('.description-container').removeClass('hidden');
                        $(thiss).siblings('.quick-description-edit-textarea').addClass('hidden');
                        $('#description' + id).hide();
                        $('#description' + id).html(description);
                        $('#description' + id).show(1000);
                        var short_description = description.substr(0, 100);
                        $(thiss).closest('.long-description-container').siblings('.short-description-container').text(short_description);
                    }).fail(function (response) {
                        console.log(response);
                        alert('Could not update description');
                    });
                }
            });
        });
        function updateSizes(element, category_value) {
            var found_id = 0;
            var found_final = false;
            var found_everything = false;
            var category_id = category_value;
            $(element).closest('tr').find('.quick-edit-size').empty();
            $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                value: '',
                text: 'Select Category'
            }));
            console.log('PARENT ID', categories_array[category_id]);
            if (categories_array[category_id] != 0) {
                Object.keys(id_list).forEach(function (id) {
                    if (id == category_id) {
                        $(element).closest('tr').find('.quick-edit-size').empty();
                        $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                            value: '',
                            text: 'Select Category'
                        }));
                        id_list[id].forEach(function (value) {
                            $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                                value: value,
                                text: value
                            }));
                        });
                        found_everything = true;
                        // $(element).closest('tr').find('.quick-edit-size').removeClass('hidden');
                        $(element).closest('tr').find('.lmeasurement-container').addClass('hidden');
                        $(element).closest('tr').find('.hmeasurement-container').addClass('hidden');
                        $(element).closest('tr').find('.dmeasurement-container').addClass('hidden');
                    }
                });
                if (!found_everything) {
                    Object.keys(category_tree).forEach(function (key) {
                        Object.keys(category_tree[key]).forEach(function (index) {
                            if (index == categories_array[category_id]) {
                                found_id = index;
                                return;
                            }
                        });
                    });
                    console.log('FOUND ID', found_id);
                    if (found_id != 0) {
                        Object.keys(id_list).forEach(function (id) {
                            if (id == found_id) {
                                $(element).closest('tr').find('.quick-edit-size').empty();
                                $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                                    value: '',
                                    text: 'Select Category'
                                }));
                                id_list[id].forEach(function (value) {
                                    $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                                        value: value,
                                        text: value
                                    }));
                                });
                                // $(element).closest('tr').find('input[name="other_size"]').addClass('hidden');
                                // $(element).closest('tr').find('.quick-edit-size').removeClass('hidden');
                                $(element).closest('tr').find('.lmeasurement-container').addClass('hidden');
                                $(element).closest('tr').find('.hmeasurement-container').addClass('hidden');
                                $(element).closest('tr').find('.dmeasurement-container').addClass('hidden');
                                found_final = true;
                            }
                        });
                    }
                }
                if (!found_final) {
                    // $(element).closest('tr').find('input[name="other_size"]').removeClass('hidden');
                    // $(element).closest('tr').find('.quick-edit-size').addClass('hidden');
                    $(element).closest('tr').find('.lmeasurement-container').removeClass('hidden');
                    $(element).closest('tr').find('.hmeasurement-container').removeClass('hidden');
                    $(element).closest('tr').find('.dmeasurement-container').removeClass('hidden');
                }
            }
        }
        $(document).on('click', '.use-description', function () {
            var id = $(this).data('id');
            var description = $(this).data('description');
            url = "{{ url('products') }}/" + id + '/updateDescription';
            $('#description' + id).hide();
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    description: description,
                    _token: "{{ csrf_token() }}",
                }
            }).done(function (response) {
                $('#description' + id).html(description);
                $('#span_description_' + id).html(description);
                $('#textarea_description_' + id).text(description);
                $('#description' + id).show(1000);
            });
        });
        $(document).on('click', '#upload-all', function () {
            $(self).hide();
            var ajaxes = [];
            for (var i = 0; i < productIds.length; i++) {
                url = "{{ url('products') }}/" + productIds[i] + '/listMagento';
                ajaxes.push($.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                    }
                }).done(function (response) {
                    $('#product' + productIds[i]).hide();
                }));
            }
            $.when.apply($, ajaxes)
                .done(function () {
                    //location.reload();
                });
        });
        $(document).on('click', '.upload-magento', function () {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var thiss = $(this);
            var url = '';
            if (type == 'approve') {
                url = "{{ url('products') }}/" + id + '/approveProduct';
            } else if (type == 'list') {
                url = "{{ url('products') }}/" + id + '/listMagento';
            } else if (type == 'enable') {
                url = "{{ url('products') }}/" + id + '/approveMagento';
            } else if (type == 'submit_for_approval') {
                url = "{{ url('products') }}/" + id + '/submitForApproval';
            } else {
                url = "{{ url('products') }}/" + id + '/updateMagento';
            }
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    // $(thiss).text('Loading...');
                    $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                }
            }).done(function (response) {
                if (response.result != false && response.status == 'is_approved') {
                    $(thiss).closest('tr').remove();
                } else if (response.result != false && response.status == 'listed') {
                    // $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                } else if (response.result != false && response.status == 'approved') {
                    // $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                } else {
                    // $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                }
            }).fail(function (response) {
                console.log(response);
                if (type == 'approve') {
                    // $(thiss).text('Approve');
                } else if (type == 'list') {
                    // $(thiss).text('List');
                } else if (type == 'enable') {
                    // $(thiss).text('Enable');
                } else {
                    // $(thiss).text('Update');
                }
                alert('Could not update product on magento');
            });
        });
        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "productlistings"
                },
            }).done(response => {
                var html = '';
                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br/> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });
        $('#addRemarkButton').on('click', function () {
            alert('adding remark...');
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'productlistings'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');
                var html = ' <p> ' + remark + ' <br/> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);
                alert('Could not fetch remarks');
            });
        });
        $(document).on('click', '.delete-thumbail-img', function (e) {
            e.preventDefault();
            var conf = confirm("Are you sure you want to delete this image ?");
            if (conf == true) {
                var $this = $(this);
                $.ajax({
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('product.deleteImages') }}',
                    data: {
                        product_id: $this.data("product-id"),
                        media_id: $this.data("media-id"),
                        media_type: $this.data("media-type")
                    },
                }).done(response => {
                    if (response.code == 1) {
                        $this.closest(".thumbnail-pic").remove();
                    }
                });
            }
        });
        function bigImg(img) {
            $('#large-image').attr("src", img);
            $('#imageExpand').modal('show');
        }
        function normalImg() {
            $('#imageExpand').modal('hide');
        }
        function cropImage(img, id) {
            $('#image-tag' + id).hide();
            $('#image' + id).removeAttr("style");
            $('#image' + id).prop("onclick", null).off("click");
            $('#image' + id).height('336');
            var example = $('#image' + id).cropme();
            example.cropme('bind', {
                url: img,
            });
            example.cropme('reload', {
                zoom: {
                    min: 0.01,
                    max: 1,
                    enable: true,
                    mouseWheel: true,
                    slider: true,
                }
            });
        }
        function crop(img, id, gridImage) {
            style = $('.cropme-container img').attr("style");
            $.ajax({
                url: '/products/listing/final-crop-image',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    style: style,
                    img, img,
                    id, id,
                },
            })
                .done(function () {
                    var d = new Date();
                    var n = d.toLocaleTimeString();
                    newurl = img + '?version=' + n;
                    html = '<div onclick="bigImg(\'' + url + '\')" style=" margin-bottom: 5px; width: 300px;height: 300px; background-image: url(\'' + newurl + '\'); background-size: 300px" id="image' + id + '"><img style="width: 300px;" src="/images/' + gridImage + '" class="quick-image-container img-responive" alt="" data-toggle="tooltip" data-placement="top" title="ID: ' + id + '" id="image-tag' + id + '"></div><button onclick="cropImage(\'' + img + '\',' + id + ')" class="btn btn-secondary">Crop Image</button><button onclick="crop(\'' + img + '\',' + id + ',\'' + gridImage + '\')" class="btn btn-secondary">Crop</button>';
                    $('#col-large-image' + id).empty().append(html);
                    alert('Image Cropped and Saved Successfully');
                })
                .fail(function () {
                    console.log("error");
                });
        }
        function replaceThumbnail(id, url, gridImage) {
            html = '<div onclick="bigImg(\'' + url + '\')" style=" margin-bottom: 5px; width: 300px;height: 300px; background-image: url(\'' + url + '\'); background-size: 300px" id="image' + id + '"><img style="width: 300px;" src="/images/' + gridImage + '" class="quick-image-container img-responive" alt="" data-toggle="tooltip" data-placement="top" title="ID: ' + id + '" id="image-tag' + id + '"></div><button onclick="cropImage(\'' + url + '\',' + id + ')" class="btn btn-secondary">Crop Image</button><button onclick="crop(\'' + url + '\',' + id + ',\'' + gridImage + '\')" class="btn btn-secondary">Crop</button>';
            $('#col-large-image' + id).empty().append(html);
        }
        $(document).on("click", ".set-description-site", function () {
            var $this = $(this);
            var modal = $("#set-description-site-wise");
            modal.find("#store-product-id").val($this.data("id"));
            modal.find("#store-product-description").val($this.data("description"));
            modal.find("#show-description-summery").html($this.data("description"));
            modal.modal("show");
        });
        $(document).on("click", ".btn-save-store", function (e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: '/product/store-website-description',
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                data: form.serialize(),
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-preview").hide();
                if (response.code == 200) {
                    $("#set-description-site-wise").modal("hide");
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }
            }).fail(function () {
                $("#loading-image-preview").hide();
                console.log("error");
            });
        });
        $(".post-remark").select2();
        $(".quick-edit-category").select2();
        // $("#main_checkbox").click(function(){
        //     $('.affected_checkbox').not(this).prop('checked', this.checked);
        // });
        {{--$(document).on('click', '.delete_checked_products', function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    var conf = confirm("Are you sure you want to delete selected products ?");--}}
        {{--    if (conf == true) {--}}
        {{--        var $this = $(this);--}}
        {{--        $.ajax({--}}
        {{--            type: 'GET',--}}
        {{--            headers: {--}}
        {{--                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')--}}
        {{--            },--}}
        {{--            url: '{{ route('products.delete') }}',--}}
        {{--            data: {--}}
        {{--                product_id: $this.data("product-id"),--}}
        {{--                media_id: $this.data("media-id"),--}}
        {{--                media_type: $this.data("media-type")--}}
        {{--            },--}}
        {{--        }).done(response => {--}}
        {{--            if (response.code == 1) {--}}
        {{--                $this.closest(".thumbnail-pic").remove();--}}
        {{--            }--}}
        {{--        });--}}
        {{--    }--}}
        {{--});--}}
        $('#main_checkbox').on('click', function (e) {
            if ($(this).is(':checked', true)) {
                $(".affected_checkbox").prop('checked', true);
            } else {
                $(".affected_checkbox").prop('checked', false);
            }
        });
        $('.mass_action').on('click', function (e) {
            var allVals = [];
            $(".affected_checkbox:checked").each(function () {
                allVals.push($(this).attr('data-id'));
            });
            if (allVals.length <= 0) {
                alert("Please select row.");
            } else {
                if (this.className == 'btn btn-secondary text-left mass_action delete_checked_products') {
                    var check = confirm("Are you sure you want to delete this row?");
                    var final_url = '{{route('products.mass.delete')}}';
                } else {
                    var check = confirm("Are you sure you want to approve this row?");
                    var final_url = '{{route('products.mass.approve')}}';
                }
                if (check == true) {
                    var join_selected_values = allVals.join(",");
                    $.ajax({
                        url: final_url,
                        type: 'get',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: 'ids=' + join_selected_values,
                        success: function (data) {
                            console.log(data);
                            if (data['status']) {
                                alert(data['success']);
                                $(".affected_checkbox:checked").each(function () {
                                    if (data['result']) {
                                        console.log((data['status']));
                                        $(".affected_checkbox:checked").prop('checked', false);
                                    } else {
                                        $(this).parents("tr").remove();
                                    }
                                });
                            } else if (data['error']) {
                                alert(data['error']);
                            } else {
                                alert('Whoops Something went wrong!!');
                            }
                        },
                        error: function (data) {
                            // alert(data.responseText);
                            console.log(data);
                        }
                    });
                } else {
                    $(".affected_checkbox:checked").prop('checked', false);
                    $("#main_checkbox:checked").prop('checked', false);
                }
            }
        });
        $(document).on('click', '.quick-description', function () {
            var id = $(this).data('id');
            $(this).closest('td').find('.quick-description').addClass('hidden');
            $(this).closest('td').find('.quick-edit-description-textarea').removeClass('hidden');
            $(this).closest('td').find('.quick-edit-description-textarea').focus();
        });
        $(document).on('keypress', '.quick-edit-description-textarea', function (e) {
            var id = $(this).parents('.quick-edit-description').data('id');
            var key = e.which;
            var thiss = $(this);
            if (key == 13) {
                e.preventDefault();
                var description = $(thiss).val();
                $(thiss).addClass('hidden');
                $(thiss).siblings('.quick-description').text(description.substring(0, 20) + (description.length > 20 ? '...' : ''));
                $(thiss).siblings('.quick-description').removeClass('hidden');
                $.ajax({
                    type: 'POST',
                    url: "{{ url('products') }}/" + id + '/updateDescription',
                    data: {
                        _token: "{{ csrf_token() }}",
                        description: description,
                    }
                }).done(function () {
                }).fail(function (response) {
                    alert('Could not update description');
                });
            }
        });
        $(document).on('change', '.post-remark', function () {
            const data = {
                _token: "{{ csrf_token() }}",
                rejected: 1,
                product_id: $(this).data('id'),
                remark: $(this).val(),
                senior: 1
            };
            $.ajax({
                type: 'POST',
                url: "{{ url('products') }}/" + $(this).data('id') + '/addListingRemarkToProduct',
                data: data
            }).done(function () {
            }).fail(function (response) {
                alert('Could not update status');
            });
        });
        $(document).on('change', '.approved_by', function () {
            const data = {
                _token: "{{ csrf_token() }}",
                product_id: $(this).data('id'),
                user_id: $(this).val(),
            };
            $.ajax({
                type: 'POST',
                url: "{{ url('products') }}/" + $(this).data('id') + '/updateApprovedBy',
                data: data
            }).done(function () {
            }).fail(function (response) {
                alert('Could not update status');
            });
        });
        $(document).on('click', '.upload-single', function () {
            $(self).hide();
            $this = $(this);
            var ajaxes = [];
            // for (var i = 0; i < productIds.length; i++) {
            var id = $(this).data('id');
            var thiss = $(this);
            $(this).addClass('fa-spinner').removeClass('fa-upload')
            url = "{{ url('products') }}/" + id + '/listMagento';
            ajaxes.push($.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    // $(thiss).text('Loading...');
                    // $(thiss).html('<i class="fa fa-spinner" aria-hidden="true"></i>');
                }
            }).done(function (response) {
                thiss.removeClass('fa-spinner').addClass('fa-upload')
                toastr['success']('Request Send successfully', 'Success')
                $('#product' + id).hide();
            }));

            // }
            $.when.apply($, ajaxes)
                .done(function () {
                    //location.reload();
                });
        });
        $(document).on('click', '.product-slider-arrow', function () {
            var active_ele = $(this).parents('.modal-body').find('.product-slider.d-block');
            if (active_ele.length !== 0) {
                if (active_ele.next().length !== 0 && active_ele.next().hasClass('product-slider')) {
                    active_ele.addClass('d-none').removeClass('d-block');
                    active_ele.next().addClass('d-block').removeClass('d-none');
                    console.log(active_ele.next().hasClass('.product-slider'), 'next');
                }
            }
        })
        $(document).on('click', '.product-slider-arrow-left', function () {
            var active_ele = $(this).parents('.modal-body').find('.product-slider.d-block');
            if (active_ele.length !== 0) {
                if (active_ele.prev().length !== 0 && active_ele.prev().hasClass('product-slider')) {
                    active_ele.addClass('d-none').removeClass('d-block');
                    active_ele.prev().addClass('d-block').removeClass('d-none');
                    console.log(active_ele.prev().hasClass('product-slider'), 'prev');
                }
            }
        })
        $(document).on('click', '[name="image_status"]', function(){
            const data = {
                _token: "{{ csrf_token() }}",
                product_id: $(this).data('product_id'),
                site_id: $(this).data('site_id'),
                status: $(this).val(),
            };
            $.ajax({
                type: 'POST',
                url: "/product/crop_rejected_status",
                data: data
            }).done(function () {
            }).fail(function (response) {
                alert('Could not update status');
            });
        })
    </script>
@endsection
