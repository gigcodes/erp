@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">

                <!--roletype-->
                <h2>{{ $roletype }}</h2>

                <!--pending products count-->
                @can('admin')
                  @if( $roletype != 'Selection' && $roletype != 'Sale' )
                      <div class="pt-2 pb-3">
                          <a href="{{ route('pending',$roletype) }}"><strong>Pending
                                  : </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
                      </div>
                  @endif
                @endcan

                <!--attach Product-->
                @if( isset($doSelection) )
                    <p><strong> {{ strtoupper($model_type)  }} ID : {{ $model_id }} </strong></p>
                @endif

                <!--Product Search Input -->
                <form action="{{ route('search') }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                        {{-- <div class="row"> --}}
                            {{-- <div class="col-md-4"> --}}
                                <input name="term" type="text" class="form-control" id="product-search"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="sku,brand,category,status,stage">
                                <input hidden name="roletype" type="text" value="{{ $roletype }}">
                                {{--@if( $roletype == 'Sale' )
                                    <input hidden name="saleId" type="text" value="{{ $sale_id ?? '' }}">
                                @endif--}}
                                @if( isset($doSelection) )
                                    <input hidden name="doSelection" type="text" value="true">
                                    <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                                    <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                                @endif
                              </div>
                            {{-- </div> --}}
                            <div class="form-group mr-3 mb-3">
                              {{-- <strong>Brands</strong> --}}
                              {!! $category_selection !!}

                            </div>

                            <div class="form-group mr-3 mb-3">
                              <strong class="mr-3">Price</strong>
                              {{-- <select class="form-control" name="price">
                                <option value>Select Price Range</option>
                                <option value="1" {{ (isset($price) && $price == 1) ? 'selected' : '' }}>Up to 10K</option>
                                <option value="2" {{ (isset($price) && $price == 2) ? 'selected' : '' }}>10K - 30K</option>
                                <option value="3" {{ (isset($price) && $price == 3) ? 'selected' : '' }}>30K - 50K</option>
                                <option value="4" {{ (isset($price) && $price == 4) ? 'selected' : '' }}>50K - 100K</option>
                              </select> --}}

                              <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>
                            </div>

                            <div class="form-group mr-3">

                              @php $brands = \App\Brand::getAll(); @endphp
                              {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

                            <div class="form-group mr-3">
                              @php $colors = new \App\Colors(); @endphp
                              {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

                            <div class="form-group">
                              @php $suppliers = new \App\ReadOnly\SupplierList(); @endphp
                              {!! Form::select('supplier[]',$suppliers->all(), (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control', 'multiple' => true]) !!}
                            </div>
                            {{-- <div class="form-group mr-3">
                              <strong>Category</strong>

                            </div> --}}

                            {{-- <div class="form-group">
                              <strong>Price</strong>

                            </div> --}}
                            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                        {{-- </div>
                    </div> --}}
                </form>


            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    {!! $products->appends(Request::except('page'))->links() !!}

	<?php
	$query = http_build_query( Request::except( 'page' ) );
	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
	?>

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="productGrid" id="productGrid">

    </div>

    {!! $products->appends(Request::except('page'))->links() !!}

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

	<?php $stage = new \App\Stage(); ?>

    <script>

      var searchSuggestions = {!! json_encode($search_suggestions) !!};

      $('#product-search').autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

          response(results.slice(0, 10));
        }
      });

        Array.prototype.groupBy = function (prop) {
            return this.reduce(function (groups, item) {
                const val = item[prop]
                groups[val] = groups[val] || []
                groups[val].push(item)
                return groups
            }, {})
        };

        const products = [
                @foreach ($products as $product)
				<?php
				$r = explode( ' ', $product->created_at );

				switch ( $roletype ) {
					case 'Selection':
						$link = route( 'productselection.edit', $product->id );
						break;
					case 'Searcher':
						$link = route( 'productsearcher.edit', $product->id );
						break;
					case 'Attribute':
						$link = route( 'productattribute.edit', $product->id );
						break;
					case 'Supervisor':
						$link = route( 'products.show', $product->id );
						break;
					case 'ImageCropper':
						$link = route( 'productimagecropper.edit', $product->id );
						break;
					case 'Lister':
						$link = route( 'products.show', $product->id );
						break;
					case 'Approver':
						$link = route( 'products.show', $product->id );
						break;
					case 'Inventory':
						$link = route( 'products.show', $product->id );
						break;
					case 'Sale':
						$link = route( 'products.show', $product->id );
						break;
				}
				?>
            {
                'sku': '{{ $product->sku }}',
                'id': '{{ $product->id }}',
                'size': '{{ $product->size}}',
                'price': '{{ $product->price_special }}',
                'brand': '{{ \App\Http\Controllers\BrandController::getBrandName($product->brand ) }}',
                'image': '{{ $product->getMedia(config('constants.media_tags'))->first()
                              ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
                              : ''
                           }}',
                'created_at': '{{ $r[0]  }}',
                'link': '{{ $link }}',
                'isApproved': '{{ $product->isApproved }}',
                'stage': '{{ $stage->getNameById( $product->stage )}}',

                @if( isset($doSelection) )
                'isAttached': '{{ in_array($product->id, $selected_products) ? 1 : 0 }}',
                @endif
            },
            @endforeach
        ];

        const groupedByTime = products.groupBy('created_at');

        jQuery(document).ready(function () {

            Object.keys(groupedByTime).forEach(function (key) {

                let html = '<h4>' + getTodayYesterdayDate(key) + '</h4><div class="row">';

                groupedByTime[key].forEach(function (product) {

                    html += `
                        <div class="col-md-3 col-xs-6 text-center">
                        <a href="` + product['link'] + `">
                            <img src="` + product['image'] + `" class="img-responsive grid-image" alt="" />
                                            <p>Sku : ` + product['sku'] + `</p>
                                            <p>Id : ` + product['id'] + `</p>
                                            <p>Size : ` + product['size'] + `</p>
                                            <p>Price : ` + product['price'] + `</p>
                                            <!--<p>Brand : ` + product['brand'] + `</p>-->
                                            <p>Status : ` + product['stage'] + `</p>
                                             {{--<p>Status : `+ ( ( product['isApproved'] ===  '1' ) ?
                                                                    'Approved' : ( product['isApproved'] ===  '-1' ) ? 'Rejected' : 'Nil') +`</p>--}}
                            {{--@can('supervisor-edit')
                                <button data-id="`+product['id']+`"
                                        class="btn btn-approve btn-secondary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                        `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                                </button>
                            @endcan--}}
                        </a>
                                            @if( isset($doSelection))

                        <button data-id="` + product['id'] + `" model-type="{{ $model_type }}" model-id="{{ $model_id }}"
                                                            class="btn-attach btn btn-secondary ` + ((product['isAttached'] === '1') ? 'btn-success' : '') + ` ">
                                                            ` + ((product['isAttached'] === '0') ? 'Attach' : 'Attached') + `
                        </button>
                                            @endif
                        </div>
                    `;
                });

                jQuery('#productGrid').append(html + '</div>');
            });

            // $('#product-search').on('keyup', function() {
            //   alert('t');
            // });

            {{--@if($roletype == 'Supervisor')
            @can('supervisor-edit')
                attactApproveEvent();
            @endcan
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
                    url: '/attachProductToModel/'+ model_type + '/' + model_id + '/' + product_id,

                    success: function (response) {

                        if (response.msg === 'success') {
                            btn.toggleClass('btn-success');
                            btn.html(response.action);
                        }
                    }
                });
            });
        });

    </script>
@endsection
