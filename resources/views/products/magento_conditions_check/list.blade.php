<table class="table table-bordered table-striped" style="table-layout:fixed;">
    <thead>
    <tr>
        <th style="width:2%"><input type="checkbox" id="main_checkbox" name="choose_all"></th>
        <th style="width:8%">Product ID</th>
        <th style="width:4%">Image</th>
        <th style="width:7%">Brand</th>
        <th style="width:15%">Category</th>
        <th style="width:8%">Title</th>
        <th style="width:9%"> Description</th>
        <th style="width:8%">Composition</th>
        <th style="width:8%">Color</th>
        <th style="width:8%">Dimension</th>
{{--        <th style="width:7%">Sizes</th>--}}
        <th style="width:5%">Price</th>
{{--        <th style="width:8%">Action</th>--}}
        <th style="width:5%">Status</th>
        <th style="width:5%">Log message</th>
        <th style="width:5%">User</th>
    </tr>
    </thead>
    <tbody>
    @if($products->count() == 0)
        <tr>
            <td colspan="15" class="text-center">No records found!</td>
        </tr>
    @endif
    @foreach ($products as $key => $product)
        <tr style="display: none" id="product{{ $product->id }}">
            <td colspan="14">
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
                            <strong class="same-color" style="text-decoration: underline;">HsCode</strong>
                            <br/>
                            <span class="same-color flex-column">{{ strtoupper($hscode) }}</span>
                        </p>
                        <p>
                            <strong>Sizes</strong>: {{ $product->size_eu }}<br/>
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
                                <button class="btn btn-xs btn-default edit-product-show" data-id="{{$product->id}}">Toggle Edit</button>
                                @if ($product->status_id == 9)
                                    <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List
                                    </button>
                                @elseif ($product->status_id == 12)
                                    <button type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>
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
                    </div>
                </div>
            </td>
        </tr>
        <tr id="product_{{ $product->id }}" class="">
            <td> <input type="checkbox" class="affected_checkbox" name="products_to_update[]" data-id="{{$product->id}}"></td>
            @php
                $websiteArraysForProduct = \App\Helpers\ProductHelper::getStoreWebsiteName($product->id);
            @endphp
            <td class="table-hover-cell">
                {{ $product->id }}
                @if($product->croppedImages()->count() == count($websiteArraysForProduct))
                    <span class="badge badge-success" >&nbsp;</span>
                @else
                    <span class="badge badge-warning" >&nbsp;</span>
                @endif
                @if(count($product->more_suppliers()) > 1)
                    <button style="padding:0px;" type="button" class="btn-link"
                            data-id="{{ $product->id }}" data-target="#product_suppliers_{{ $product->id }}"
                            data-toggle="modal">View
                    </button>
                @endif
                <div>
                    @if($product->supplier_link)
                        <a target="_new" title="{{ $product->sku }}" href="{{ $product->supplier_link }}">{{ substr($product->sku, 0, 5) . (strlen($product->sku) > 5 ? '...' : '') }}</a>
                    @else
                        <a title="{{ $product->sku }}" href="javascript:;">{{ substr($product->sku, 0, 5) . (strlen($product->sku) > 5 ? '...' : '') }}</a>
                    @endif
                </div>
            </td>
            <td style="word-break: break-all; word-wrap: break-word">
                <button type="button" class="btn-link quick-view_image__"
                        data-id="{{ $product->id }}" data-target="#product_image_{{ $product->id }}"
                        data-toggle="modal">View
                </button>
            </td>

            <td>
                @if($product->brands)
                    <a title="{{ $product->brands->name }}" href="javascript:;">{{ substr($product->brands->name, 0, 5) . (strlen($product->brands->name) > 5 ? '...' : '') }}</a>
                @else
                    N/A
                @endif
            </td>

            <td class="table-hover-cell">
                <?php
                $cat = [];
                $catM = $product->categories;
                if($catM) {
                    $parentM = $catM->parent;
                    $cat[]   = $catM->title;
                    if($parentM) {
                        $gparentM = $parentM->parent;
                        $cat[]    = $parentM->title;
                        if($gparentM) {
                            $cat[] = $gparentM->title;
                        }
                    }
                }
                ?>
                @if (!$imageCropperRole)
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
                {{ implode(">",array_reverse($cat)) }}
            </td>
            <td class="table-hover-cell quick-edit-name quick-edit-name-{{ $product->id }}" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    <span class="quick-name">{{ $product->name }}</span>
                    <input name="text" class="form-control quick-edit-name-input hidden" placeholder="Product Name" value="{{ $product->name }}">
                @else
                    <span>{{ $product->name }}</span>
                @endif
            </td>
            <td class="table-hover-cell">
                <div class="quick-edit-description quick-edit-description-{{ $product->id }}" data-id="{{ $product->id }}">
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
                </div>
                <div>
                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-description" title="Edit description for specific Website" data-id="{{ $product->id }}" data-target="#description_modal_view_{{ $product->id }}"
                            data-toggle="modal"><i class="fa fa-info-circle"></i></button>
                </div>
            </td>
            <td class="table-hover-cell" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    {{ $product->composition }}
                @else
                    <span class="quick-composition">{{ $product->composition }}</span>
                @endif
            </td>
            <td class="table-hover-cell">

                    {{ $product->color }}
            </td>


            <td class="table-hover-cell">
                @if (!$imageCropperRole)
                    {{ !empty($product->lmeasurement) ? $product->lmeasurement : '' }}x{{ !empty($product->hmeasurement) ? $product->hmeasurement : ' ' }}x{{ !empty($product->dmeasurement) ? $product->dmeasurement : '' }}
                @endif
            </td>
{{--            <td>--}}
{{--                @php--}}
{{--                    $size_array = explode(',', $product->size_eu);--}}
{{--                @endphp--}}

{{--                {{ isset($size_array[0]) ? $size_array[0] : '' }} {{ isset($size_array[1]) ? ', '.$size_array[1] :  '' }}--}}
{{--            </td>--}}
            <td class="table-hover-cell quick-edit-price" data-id="{{ $product->id }}">
                @if (!$imageCropperRole)
                    <span class="quick-price">{{ $product->price }}</span>
                    <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $product->price }}">
                @else
                    <span>EUR {{ $product->price }}</span>
                @endif
            </td>
{{--            <td class="action">--}}
{{--                @if(auth()->user()->isReviwerLikeAdmin('final_listing'))--}}
{{--                    @if ($product->is_approved == 0)--}}
{{--                        <i style="cursor: pointer;" class="fa fa-check upload-magento" title="Approve" data-id="{{ $product->id }}" data-type="approve" aria-hidden="true"></i>--}}
{{--                    @elseif ($product->is_approved == 1 && $product->isUploaded == 0)--}}
{{--                        <i style="cursor: pointer;" class="fa fa-list upload-magento" title="List" data-id="{{ $product->id }}" data-type="list" aria-hidden="true"></i>--}}
{{--                    @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)--}}
{{--                        <i style="cursor: pointer;" class="fa fa-toggle-off upload-magento" title="Enable" data-id="{{ $product->id }}" data-type="enable" aria-hidden="true"></i>--}}
{{--                    @else--}}
{{--                        <i style="cursor: pointer;" class="fa fa-pencil upload-magento" title="Update" data-id="{{ $product->id }}" data-type="update" aria-hidden="true"></i>--}}
{{--                    @endif--}}
{{--                    @if ($product->product_user_id != null)--}}
{{--                        {{ \App\User::find($product->product_user_id)->name }}--}}
{{--                    @endif--}}
{{--                    <i style="cursor: pointer;" class="fa fa-upload upload-single" data-id="{{ $product->id }}" title="push to magento" aria-hidden="true"></i>--}}
{{--                @else--}}
{{--                    <i style="cursor: pointer;" class="fa fa-toggle-off upload-magento" title="Enable" data-id="{{ $product->id }}" data-type="submit_for_approval" aria-hidden="true"></i>--}}
{{--                @endif--}}
{{--                <i style="cursor: pointer;" class="fa fa-tasks" data-toggle="modal" title="Activity"--}}
{{--                   data-target="#product_activity_{{ $product->id }}" aria-hidden="true"></i>--}}
{{--                <a href="javascript:;" data-product-id="{{$product->id}}" class="check-website-should-pushed">--}}
{{--                    <i style="cursor: pointer;" class="fa fa-globe" data-toggle="modal" title="Website" data-target="#product-website-{{ $product->id }}" aria-hidden="true"></i>--}}
{{--                </a>--}}
{{--                <i style="cursor: pointer;" class="fa fa-trash" data-toggle="modal" title="Scrape"--}}
{{--                   data-target="#product_scrape_{{ $product->id }}" aria-hidden="true"></i>--}}
{{--            </td>--}}
            <td>
                {{ $product->product_status }}
            </td>
            <td>
                @if($product->magentoLog)
                    {{ $product->magentoLog->message }}
                @else
                    Product not entered to the queue for conditions check
                @endif
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