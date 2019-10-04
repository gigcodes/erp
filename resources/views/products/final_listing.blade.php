@extends('layouts.app')

@section('title', 'Product Listing')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
        .quick-edit-color {
            transition: 1s ease-in-out;
        }

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
    </style>
@endsection

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Approved Product Listing ({{ $products_count }}) <a href="{{ action('ProductController@showSOP') }}?type=ListingApproved" class="pull-right">SOP</a></h2>

            <div class="pull-left">
                <form class="form-inline" action="{{ action('ProductController@approvedListing') }}" method="GET">

                    <div class="form-group mr-3 mb-3">
                        <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="sku,brand,category,status,stage">
                    </div>

                    <div class="form-group mr-3 mb-3">
                        {{-- {!! $category_search !!} --}}
                        <select class="form-control" name="category[]">
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
                                    <option style="background-color: {{ $color }};" value="{{ $children['id'] }}" {{ in_array($children['id'], $selected_categories) ? 'selected' : '' }}>&nbsp;&nbsp;{{ $children['title'] }}</option>
                                    @foreach ($children['child'] as $child)
                                        <option style="background-color: {{ $color }};" value="{{ $child['id'] }}" {{ in_array($child['id'], $selected_categories) ? 'selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;{{ $child['title'] }}</option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select class="form-control select-multiple" name="brand[]" multiple data-placeholder="Brand..">
                            <optgroup label="Brands">
                                @foreach ($brands as $key => $name)
                                    <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select class="form-control select-multiple" name="color[]" multiple data-placeholder="Color..">
                            <optgroup label="Colors">
                                @foreach ($colors as $key => $col)
                                    <option value="{{ $key }}" {{ isset($color) && $color == $key ? 'selected' : '' }}>{{ $col }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select class="form-control select-multiple" name="supplier[]" multiple data-placeholder="Supplier..">
                            <optgroup label="Suppliers">
                                @foreach ($suppliers as $key => $item)
                                    <option value="{{ $item->id }}" {{ isset($supplier) && in_array($item->id, $supplier) ? 'selected' : '' }}>{{ $item->supplier }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select class="form-control" name="type">
                            <option value="">Select Type</option>
                            <option value="Not Listed" {{ isset($type) && $type == "Not Listed" ? 'selected' : ''  }}>Not Listed</option>
                            <option value="Listed" {{ isset($type) && $type == "Listed" ? 'selected' : ''  }}>Listed</option>
                            {{--              <option value="Approved" {{ isset($type) && $type == "Approved" ? 'selected' : ''  }}>Approved</option>--}}
                            {{--              <option value="Image Cropped" {{ isset($type) && $type == "Image Cropped" ? 'selected' : ''  }}>Image Cropped</option>--}}
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        <select class="form-control" name="user_id" id="user_id">
                            @foreach($users as $user)
                                <option value="">Select user...</option>
                                <option value="{{$user->id}}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form>
            </div>
        </div>
    </div>

    {{-- @include('development.partials.modal-task')
    @include('development.partials.modal-quick-task') --}}

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="infinite-scroll mt-5">
                <table class="table table-bordered table-striped" style="max-width: 100% !important;">
                    <tr>
                        <th width="10%" style="max-width: 100px;">Thumbnail</th>
                        <th width="10%">Name</th>
                        <th width="10%">Description</th>
                        <th width="10%">Category</th>
                        <th width="2%">Sizes</th>
                        <th width="5%">Composition</th>
                        <th width="10%">Color</th>
                        <th width="5%">Price</th>
                        <th width="10%">Action</th>
                        <th width="20%">Remarks</th>
                    </tr>
                    @foreach ($products as $key => $product)
                        <tr id="product{{ $product->id }}">
                            <td colspan="10">
                                <div class="row">
                                    <div class="col-md-1">
                                        @php
                                            $product = \App\Product::find($product->id)
                                        @endphp
                                        @if ($product->hasMedia(config('constants.media_tags')))
                                            @foreach($product->getMedia('gallery') as $media)
                                                @if(stripos($media->filename, 'crop') !== false)
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
                                                    ?>
                                                    <div class="thumbnail-pic">
                                                        <div class="thumbnail-edit"><a class="delete-thumbail-img" data-product-id="{{ $product->id }}" data-media-id="{{ $media->id }}" data-media-type="gallery" href="javascript:;"><i class="fa fa-trash fa-lg"></i></a></div>
                                                        <span class="notify-badge {{$badge}}">{{ $width."X".$height}}</span>
                                                        <img style="display:block; width: 70px; height: 80px; margin-top: 5px;" src="{{ $media->getUrl() }}" class="quick-image-container img-responive" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product->id }}">
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        @if ($product->hasMedia(config('constants.media_tags')))
                                            <div>
                                                <img src="{{ $product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="quick-image-container img-responive" style="width: 100%;" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product->id }}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <strong class="same-color">{{ $product->brands ? $product->brands->name : 'N/A' }}</strong>
                                        <p class="same-color">{{ strtoupper($product->name) }}</p>
                                        <br/>
                                        <p class="same-color" style="font-size: 18px;">
                                            <span style="text-decoration: line-through">Rs. {{ number_format($product->price_inr) }}</span> Rs. {{ number_format($product->price_special) }}
                                        </p>
                                        <br/>
                                        <p>
                                            <strong class="same-color" style="text-decoration: underline">Description</strong>
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
                                                        <button class="btn btn-default btn-sm use-description" data-id="{{ $product->id }}" data-description="{{ str_replace('"', "'", html_entity_decode($description->description)) }}">Use this description ({{ $description->website }})</button>
                                                    </p>
                                                @endif
                                            @endforeach
                                            <hr/>
                                        @endif

                                        <p>
                                            <strong class="same-color" style="text-decoration: underline;">Composition</strong>
                                            <br/>
                                            <span class="same-color flex-column">
                                                {{ strtoupper($product->composition) }}
                                            </span>
                                        </p>

                                        <p>
                                        <span>
                                            <strong>Color</strong>: {{ strtoupper($product->color) }}<br/>
                                        </span>
                                        </p>

                                        <p>
                                            <strong>Sizes</strong>: {{ $product->size }}<br/>
                                            <strong>Dimension</strong>: {{ \App\Helpers\ProductHelper::getMeasurements($product) }}<br/>
                                        </p>
                                        <p>
                                            <span class="sololuxury-button">ADD TO BAG</span>
                                            <span class="sololuxury-button"><i class="fa fa-heart"></i> ADD TO WISHLIST</span>
                                        </p>
                                        <p class="same-color">
                                            View All: <strong>{{ isset($product->product_category->id) ? \App\Category::getCategoryPathById($product->product_category->id)  : '' }}</strong>
                                            <br/>
                                            View All: <strong>{{ $product->brands ? $product->brands->name : 'N/A' }}</strong>
                                        </p>
                                        <p class="same-color">
                                            <strong>Style ID</strong>: {{ $product->sku }}
                                            <br/>
                                            <strong class="text-danger">{{ $product->is_on_sale ? 'On Sale' : '' }}</strong>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <h2 class="page-heading">
                                            <a target="_new" href="{{ action('ProductController@show', $product->id) }}">{{ $product->id }}</a>
                                        </h2>
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
                                                    <select style="width: 90px !important;" data-id="{{$product->id}}" class="form-control-sm form-control reject-cropping bg-secondary text-light" name="reject_cropping" id="reject_cropping_{{$product->id}}">
                                                        <option value="0">Select...</option>
                                                        <option value="Images Not Cropped Correctly">Images Not Cropped Correctly</option>
                                                        <option value="No Images Shown">No Images Shown</option>
                                                        <option value="Grid Not Shown">Grid Not Shown</option>
                                                        <option value="Blurry Image">Blurry Image</option>
                                                        <option value="First Image Not Available">First Image Not Available</option>
                                                        <option value="Dimension Not Available">Dimension Not Available</option>
                                                        <option value="Wrong Grid Showing For Category">Wrong Grid Showing For Category</option>
                                                        <option value="Incorrect Category">Incorrect Category</option>
                                                        <option value="Only One Image Available">Only One Image Available</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Sequencing</th>
                                                <td>{{ $product->crop_ordered_at ?? 'N/A' }}</td>
                                                <td>{{ $product->cropOrderer ? $product->cropOrderer->name : 'N/A' }}</td>
                                                <td>
                                                    <button style="width: 90px" data-button-type="sequence" data-id="{{$product->id}}" class="btn btn-secondary btn-sm reject-sequence">Reject</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Approval</th>
                                                <td>{{ $product->listing_approved_at ?? 'N/A' }}</td>
                                                <td>{{ $product->approver ? $product->approver->name : 'N/A' }}</td>
                                                <td>
                                                    <select style="width: 90px !important;" data-id="{{$product->id}}" class="form-control-sm form-control reject-listing bg-secondary text-light" name="reject_listing" id="reject_listing_{{$product->id}}">
                                                        <option value="0">Select Remark</option>
                                                        <option value="Category Incorrect">Category Incorrect</option>
                                                        <option value="Price Not Incorrect">Price Not Correct</option>
                                                        <option value="Price Not Found">Price Not Found</option>
                                                        <option value="Color Not Found">Color Not Found</option>
                                                        <option value="Category Not Found">Category Not Found</option>
                                                        <option value="Description Not Found">Description Not Found</option>
                                                        <option value="Details Not Found">Details Not Found</option>
                                                        <option value="Composition Not Found">Composition Not Found</option>
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
                                                        <button style="width: 90px" class="btn btn-secondary btn-sm" data-toggle="modal" id="linkAiModal{{ $product->id }}" data-target="#aiModal{{ $product->id }}">AI result</button>
                                                        <div class="modal fade" id="aiModal{{ $product->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">{{ strtoupper($product->name) }}</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <iframe id="aiModalLoad{{ $product->id }}" frameborder="0" border="0" width="100%" height="800"></iframe>
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
                                        <p class="text-right mt-5">
                                            <button class="btn btn-xs btn-default edit-product-show" data-id="{{$product->id}}">Toggle Edit</button>
                                            @if ($product->is_approved == 0)
                                                <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="approve">Approve</button>
                                            @elseif ($product->is_approved == 1 && $product->isUploaded == 0)
                                                <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List</button>
                                            @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)
                                                <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="enable">Enable</button>
                                            @else
                                                <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>
                                            @endif
                                        </p>
                                        <div>
                                            <input class="form-control send-message" data-sku="{{$product->sku}}" type="text" placeholder="Message..." id="message_{{$product->approved_by}}" data-id="{{$product->approved_by}}">
                                        </div>
                                        @php
                                            $logScrapers = \App\Loggers\LogScraper::where('sku', $product->sku)->where('validated', 1)->get();
                                        @endphp
                                        @if ($logScrapers)
                                            <div>
                                                Successfully scraped on the following sites:<br/>
                                                <ul>
                                                    @foreach($logScrapers as $logScraper)
                                                        @if($logScraper->url != "N/A")
                                                            <li><a href="<?= $logScraper->url ?>" target="_blank"><?= $logScraper->website ?></a></li>
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
                            @if (!Auth::user()->hasRole('ImageCropers'))
                                <td style="word-break: break-all; word-wrap: break-word">
                                    @if ($product->is_approved == 1)
                                        <img src="/images/1.png" alt="">
                                    @endif

                                    @php $product = \App\Product::find($product->id) @endphp
                                    @if ($product->hasMedia(config('constants.media_tags')))
                                        <a href="{{ route('products.show', $product->id) }}" target="_blank">
                                            <img src="{{ $product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="quick-image-container img-responive" style="width: 70px;" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product->id }}">
                                        </a>
                                    @else
                                        <img src="" class="quick-image-container img-responive" style="width: 70px;" alt="">
                                    @endif

                                    {{--                {{ (new \App\Stage)->getNameById($product->stage) }}--}}
                                    <br/>
                                    SKU: {{ $product->sku }}
                                </td>
                                <td class="table-hover-cell" data-id="{{ $product->id }}">
                                    <span class="quick-name">{{ $product->name }}</span>
                                    {{-- <input type="text" name="name" class="form-control quick-edit-name-input hidden" placeholder="Product Name" value="{{ $product->name }}"> --}}
                                    <textarea name="name" class="form-control quick-edit-name-input hidden" placeholder="Product Name" rows="8" cols="80">{{ $product->name }}</textarea>

                                    <button type="button" class="btn-link quick-edit-name" data-id="{{ $product->id }}">Edit</button>
                                </td>

                                {{--              <td>--}}
                                {{--                {{ $product->crop_count }}--}}
                                {{--              </td>--}}

                                <td class="read-more-button table-hover-cell">
                                    <span id="span_description_{{ $product->id }}" class="short-description-container">{{ substr($product->short_description, 0, 100) . (strlen($product->short_description) > 100 ? '...' : '') }}</span>

                                    <span class="long-description-container hidden">
                  <span id="span_description_{{ $product->id }}" class="description-container">{{ $product->short_description }}</span>

                  <textarea name="description" id="textarea_description_{{ $product->id }}" class="form-control quick-description-edit-textarea hidden" rows="8" cols="80">{{ $product->short_description }}</textarea>
                </span>

                                    <button type="button" class="btn-link quick-edit-description" data-id="{{ $product->id }}">Edit</button>
                                </td>

                                <td class="table-hover-cell">
                                    {{-- {!! $category_selection !!} --}}
                                    {{--                  {{ $product->pr->title }}--}}
                                    <select id="quick-edit-category-{{ $product->id }}" class="form-control quick-edit-category" name="category" data-id="">
                                        @foreach ($category_array as $data)
                                            <option value="{{ $data['id'] }}">{{ $data['title'] }}</option>
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
                                                <option style="background-color: {{ $color }};" value="{{ $children['id'] }}">&nbsp;&nbsp;{{ $children['title'] }}</option>

                                                @foreach ($children['child'] as $child)
                                                    <option style="background-color: {{ $color }};" value="{{ $child['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $child['title'] }}</option>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="category_id" value="{{ $product->category }}">
                                    <input type="hidden" name="sizes" value='{{ $product->size }}'>

                                    @if ( isset($product->log_scraper_vs_ai) && $product->log_scraper_vs_ai->count() > 0 )
                                        @foreach ( $product->log_scraper_vs_ai as $resultAi )
                                            @php $resultAi = json_decode($resultAi->result_ai); @endphp
                                            @if ( !empty($resultAi->category) )
                                                <button id="ai-category-{{ $product->id }}" data-id="{{ $product->id }}" data-category="{{ \App\LogScraperVsAi::getCategoryIdByKeyword( $resultAi->category, $resultAi->gender, null ) }}" class="btn btn-default btn-sm mt-2 ai-btn-category">{{ ucwords(strtolower($resultAi->category)) }} (AI)</button>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>

                                <td class="table-hover-cell">
                                    <select class="form-control quick-edit-size select-multiple" name="size[]" multiple data-placement="Sizes" style="width: 80px;">
                                        <option value="">Size</option>
                                    </select>

                                    {{-- <input type="text" name="other_size" class="form-control mt-3 hidden" placeholder="Manual Size" value="{{ is_array(explode(',', $product->size)) && count(explode(',', $product->size)) > 1 ? '' : $product->size }}"> --}}
                                    <span class="lmeasurement-container">
                  <strong>L:</strong>
                  <input type="number" name="lmeasurement" class="form-control mt-1" placeholder="Length" min="0" max="999" value="{{ $product->lmeasurement }}">
                </span>

                                    <span class="hmeasurement-container">
                  <strong>H:</strong>
                  <input type="number" name="hmeasurement" class="form-control mt-1" placeholder="Height" min="0" max="999" value="{{ $product->hmeasurement }}">
                </span>

                                    <span class="dmeasurement-container">
                  <strong>D:</strong>
                  <input type="number" name="dmeasurement" class="form-control mt-1" placeholder="Depth" min="0" max="999" value="{{ $product->dmeasurement }}">
                </span>

                                    <button type="button" class="btn-link quick-edit-size-button" data-id="{{ $product->id }}">Save</button>
                                </td>
                                <td class="table-hover-cell" data-id="{{ $product->id }}">
                                    <span class="quick-composition">{{ $product->composition }}</span>

                                    @php
                                        $arrComposition = ['100% Cotton', '100% Leather', '100% Silk', '100% Wool', '100% Polyester', '100% Acetate', '100% Polyamide', 'Cotton', 'Leather', 'Silk', 'Wool', 'Polyester'];
                                        $i=1;
                                    @endphp
                                    @foreach ($arrComposition as $compositionValue)
                                        <button id="composition-dd-{{$i}}" class="btn btn-default btn-sm mt-2 btn-composition" data-id="{{ $product->id }}" data-value="{{ $compositionValue }}">{{ $compositionValue }}</button>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach

                                    <button id="ai-color" class="btn btn-default btn-sm mt-2 ai-btn-color" data-id="{{ $product->id }}" data-value="Multi">Multi</button>

                                    {{-- <input type="text" name="composition" class="form-control quick-edit-composition-input hidden" placeholder="Composition" value="{{ $product->composition }}"> --}}
                                    <textarea name="composition" class="form-control quick-edit-composition-input hidden" placeholder="Composition" rows="8" cols="80">{{ $product->composition }}</textarea>

                                    <button type="button" class="btn-link quick-edit-composition" data-id="{{ $product->id }}">Edit</button>
                                </td>

                                <td class="table-hover-cell">
                                    <select id="quick-edit-color-{{ $product->id }}" class="form-control quick-edit-color" name="color" data-id="{{ $product->id }}">
                                        <option value="">Select a Color</option>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color }}" {{ $product->color == $color ? 'selected' : '' }}>{{ $color }}</option>
                                        @endforeach
                                    </select>

                                    <button id="ai-color" class="btn btn-default btn-sm mt-2 ai-btn-color" data-id="{{ $product->id }}" data-value="Multi">Multi</button>

                                    @if ( isset($product->log_scraper_vs_ai) && $product->log_scraper_vs_ai->count() > 0 )
                                        @foreach ( $product->log_scraper_vs_ai as $resultAi )
                                            @php $resultAi = json_decode($resultAi->result_ai); @endphp
                                            @if ( !empty($resultAi->color) )
                                                <button id="ai-color" class="btn btn-default btn-sm mt-2 ai-btn-color" data-id="{{ $product->id }}" data-value="{{ ucwords($resultAi->color) }}">{{ ucwords($resultAi->color) }}</button>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>

                                <td class="table-hover-cell quick-edit-price" data-id="{{ $product->id }}">
                                    <span class="quick-price">{{ $product->price }}</span>
                                    <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $product->price }}">

                                    <span class="quick-price-inr">{{ $product->price_inr }}</span>
                                    <span class="quick-price-special">{{ $product->price_special }}</span>
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

                                <td>
                                    {{ $product->isUploaded }} {{ $product->isFinal }}

                                    @if ($product->is_approved == 0)
                                        <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="approve">Approve</button>
                                    @elseif ($product->is_approved == 1 && $product->isUploaded == 0)
                                        <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List</button>
                                    @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)
                                        <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="enable">Enable</button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>
                                    @endif

                                    @if ($product->product_user_id != null)
                                        {{ \App\User::find($product->product_user_id)->name }}
                                    @endif
                                </td>
                                <td style="min-width: 80px;">
                                    <input type="checkbox" name="reject_{{$product->id}}" id="reject_{{$product->id}}"> Reject<br/>
                                    <select class="form-control post-remark" id="post_remark_{{$product->id}}" data-id="{{$product->id}}">
                                        <option value="0">Select Remark</option>
                                        <option value="Category Incorrect">Category Incorrect</option>
                                        <option value="Price Not Incorrect">Price Not Correct</option>
                                        <option value="Price Not Found">Price Not Found</option>
                                        <option value="Color Not Found">Color Not Found</option>
                                        <option value="Category Not Found">Category Not Found</option>
                                        <option value="Description Not Found">Description Not Found</option>
                                        <option value="Details Not Found">Details Not Found</option>
                                        <option value="Composition Not Found">Composition Not Found</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <textarea name="remark-input-{{$product->id}}" id="remark-input-{{$product->id}}" class="form-control remark-input-post" data-id="{{$product->id}}" style="display: none;"></textarea>
                                    {{--                <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $product->id }}"><img src="/images/remark.png" /></button>--}}
                                </td>
                            @else
                                <td>
                                    @if ($product->is_approved == 1)
                                        <img src="/images/1.png" alt="">
                                    @endif

                                    @php $product = \App\Product::find($product->id) @endphp
                                    @if ($product->hasMedia(config('constants.media_tags')))
                                        <a href="{{ route('products.show', $product['id']) }}" target="_blank">
                                            <img src="{{ $product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="quick-image-container img-responive" style="width: 100px;" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product['id'] }}">
                                        </a>
                                    @else
                                        <img src="" class="quick-image-container img-responive" style="width: 100px;" alt="">
                                    @endif

                                    {{ (new \App\Stage)->getNameById($product->stage) }}
                                    <br/>
                                    SKU: {{ $product->sku }}
                                </td>
                                <td>
                                    <span>{{ $product->name }}</span>
                                </td>

                                <td class="read-more-button">
                                    <span class="short-description-container">{{ substr($product->short_description, 0, 100) . (strlen($product->short_description) > 100 ? '...' : '') }}</span>
                                    <span class="long-description-container hidden">
                                        <span class="description-container">{{ $product->short_description }}</span>
                                    </span>
                                </td>

                                <td>
                                    {{-- {{ $product->product_category->title }} --}}
                                </td>

                                <td>
                                    @if ($product->price != '')
                                        {{ $product->size }}
                                    @else
                                        L-{{ $product->lmeasurement }}, H-{{ $product->hmeasurement }}, D-{{ $product->dmeasurement }}
                                    @endif
                                </td>

                                <td>
                                    <span class="quick-composition">{{ $product->composition }}</span>
                                </td>

                                <td>
                                    {{ $product->color }}
                                </td>

                                <td>
                                    <span>{{ $product->price }}</span>

                                    <span>{{ $product->price_inr }}</span>
                                    <span>{{ $product->price_special }}</span>
                                </td>

                                {{-- <td>
                                  @if ($product->hasMedia(config('constants.media_tags')))
                                    <a href="{{ route('products.quick.download', $product->id) }}" class="btn btn-xs btn-secondary mb-1 quick-download">Download</a>
                                  @endif

                                  <input type="file" class="dropify quick-images-upload-input" name="images[]" value="" data-height="100" multiple>

                                  <button type="button" class="btn btn-xs btn-secondary mt-1 quick-images-upload" data-id="{{ $product->id }}">Upload</button>
                                </td> --}}

                                <td>
                                    {{--                  {{ $product->isUploaded }} {{ $product->isFinal }}--}}

                                    {{--                  @if ($product->is_approved == 0)--}}
                                    {{--                    <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="approve">Approve</button>--}}
                                    {{--                  @elseif ($product->is_approved == 1 && $product->isUploaded == 0)--}}
                                    {{--                    <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List</button>--}}
                                    {{--                  @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)--}}
                                    {{--                    <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="enable">Enable</button>--}}
                                    {{--                  @else--}}
                                    {{--                    <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>--}}
                                    {{--                  @endif--}}

                                    {{--                  @if ($product->product_user_id != null)--}}
                                    {{--                    {{ \App\User::find($product->product_user_id)->name }}--}}
                                    {{--                  @endif--}}
                                </td>

                                <td>
                                    {{--                  <input type="checkbox" name="reject_{{$product->id}}" id="reject_{{$product->id}}">Reject--}}
                                    {{--                  <select class="form-control post-remark" id="post_remark_{{$product->id}}" data-id="{{$product->id}}">--}}
                                    {{--                    <option value="0">Select Remark</option>--}}
                                    {{--                    <option value="Category Incorrect">Category Incorrect</option>--}}
                                    {{--                    <option value="Price Not Incorrect">Price Not Correct</option>--}}
                                    {{--                    <option value="Price Not Found">Price Not Found</option>--}}
                                    {{--                    <option value="Color Not Found">Color Not Found</option>--}}
                                    {{--                    <option value="Category Not Found">Category Not Found</option>--}}
                                    {{--                    <option value="Description Not Found">Description Not Found</option>--}}
                                    {{--                    <option value="Details Not Found">Details Not Found</option>--}}
                                    {{--                    <option value="Composition Not Found">Composition Not Found</option>--}}
                                    {{--                    <option value="Other">Other</option>--}}
                                    {{--                  </select>--}}
                                    {{--                  <textarea name="remark-input-{{$product->id}}" id="remark-input-{{$product->id}}" class="form-control remark-input-post" data-id="{{$product->id}}" style="display: none;"></textarea>--}}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </table>

                <div class="text-center mb-5">
                    <button id="upload-all" class="btn btn-danger btn-lg">UPLOAD ALL</button>
                </div>

                <p class="mb-5">
                    &nbsp;
                </p>
            </div>

        </div>
    </div>
    @include('partials.modals.remarks')

@endsection

@section('scripts')
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
    <script type="text/javascript">

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

        $(document).on('change', '.reject-listing', function (event) {
            let pid = $(this).data('id');
            let remark = $(this).val();

            if (remark == 0 || remark == '0') {
                return;
            }

            let self = this;

            $.ajax({
                url: '{{action('ProductController@addListingRemarkToProduct')}}',
                data: {
                    product_id: pid,
                    remark: remark,
                    rejected: 1,
                    senior: 1
                },
                success: function (response) {
                    toastr['success']('Product rejected successfully!', 'Rejected');
                    $(self).removeAttr('disabled');
                    $(self).val();
                    removeIdFromArray(pid);
                },
                beforeSend: function () {
                    $(self).attr('disabled');
                }, error: function () {
                    $(self).removeAttr('disabled');
                }
            });

        });
        $(document).ready(function () {

            $('ul.pagination').hide();
            $(function () {
                $('.infinite-scroll').jscroll({
                    autoTrigger: true,
                    loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                    padding: 2500,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.infinite-scroll',
                    callback: function () {
                        // $('ul.pagination').remove();
                        $('.dropify').dropify();

                        $('.quick-edit-category').each(function (item) {
                            product_id = $(this).siblings('input[name="product_id"]').val();
                            category_id = $(this).siblings('input[name="category_id"]').val();
                            sizes = $(this).siblings('input[name="sizes"]').val();
                            selected_sizes = sizes.split(',');

                            $(this).attr('data-id', product_id);
                            $(this).find('option[value="' + category_id + '"]').prop('selected', true);

                            updateSizes(this, category_id);

                            for (var i = 0; i < selected_sizes.length; i++) {
                                console.log(selected_sizes[i]);
                                // $(this).closest('tr').find('.quick-edit-size option[value="' + selected_sizes[i] + '"]').attr('selected', 'selected');
                                $(this).closest('tr').find(".quick-edit-size option[value='" + selected_sizes[i] + "']").attr('selected', 'selected');
                            }
                        });
                    }
                });
            });

            $('.dropify').dropify();
            // $(".select-multiple").multiselect();
            $(".select-multiple").select2();
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

        $('.quick-edit-category').each(function (item) {
            product_id = $(this).siblings('input[name="product_id"]').val();
            category_id = $(this).siblings('input[name="category_id"]').val();
            sizes = $(this).siblings('input[name="sizes"]').val();
            selected_sizes = sizes.split(',');

            $(this).attr('data-id', product_id);
            $(this).find('option[value="' + category_id + '"]').prop('selected', true);

            updateSizes(this, category_id);

            for (var i = 0; i < selected_sizes.length; i++) {
                $(this).closest('tr').find(".quick-edit-size option[value='" + selected_sizes[i] + "']").attr('selected', 'selected');
            }
        });

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
            var lmeasurement = $(this).closest('td').find('input[name="lmeasurement"]').val();
            var hmeasurement = $(this).closest('td').find('input[name="hmeasurement"]').val();
            var dmeasurement = $(this).closest('td').find('input[name="dmeasurement"]').val();
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
                    location.reload();
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
                    $(thiss).text('Loading...');
                }
            }).done(function (response) {
                if (response.result != false && response.status == 'is_approved') {
                    $(thiss).closest('tr').remove();
                } else if (response.result != false && response.status == 'listed') {
                    $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                } else if (response.result != false && response.status == 'approved') {
                    $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                } else {
                    $(thiss).text('Update');
                    $(thiss).attr('data-type', 'update');
                }
            }).fail(function (response) {
                console.log(response);

                if (type == 'approve') {
                    $(thiss).text('Approve');
                } else if (type == 'list') {
                    $(thiss).text('List');
                } else if (type == 'enable') {
                    $(thiss).text('Enable');
                } else {
                    $(thiss).text('Update');
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


    </script>
@endsection
