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
                    <?php echo $products->appends(request()->except("page"))->links(); ?>