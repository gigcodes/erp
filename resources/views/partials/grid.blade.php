@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">

                <!--roletype-->
                <h2>{{ $roletype }}</h2>

                <!--pending products count-->
                @if( $roletype != 'Selection' && $roletype != 'Sale' )
                    <div class="pt-2 pb-3">
                        <a href="{{ route('pending',$roletype) }}"><strong>Pending
                                : </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
                    </div>
                @endif

                <!--attach Product-->
                @if( isset($doSelection) )
                    <p><strong> {{ strtoupper($model_type)  }} ID : {{ $model_id }} </strong></p>
                @endif

                <!--Product Search Input -->
                <form action="{{ route('search') }}" method="GET">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="term" type="text" class="form-control"
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
                            <div class="col-md-4">
                                <button hidden type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
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

	<?php $stage = new \App\Stage(); ?>

    <script>

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
                'price': '{{ $product->price }}',
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
                                        class="btn btn-approve btn-primary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                        `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                                </button>
                            @endcan--}}
                        </a>
                                            @if( isset($doSelection))

                        <button data-id="` + product['id'] + `" model-type="{{ $model_type }}" model-id="{{ $model_id }}"
                                                            class="btn-attach btn btn-primary ` + ((product['isAttached'] === '1') ? 'btn-success' : '') + ` ">
                                                            ` + ((product['isAttached'] === '0') ? 'Attach' : 'Attached') + `
                        </button>
                                            @endif
                        </div>
                    `;
                });

                jQuery('#productGrid').append(html + '</div>');
            });

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