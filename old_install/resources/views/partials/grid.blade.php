@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>{{ $roletype }}</h2>
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="productGrid" id="productGrid">

    </div>

    <script>

        Array.prototype.groupBy = function(prop) {
            return this.reduce(function(groups, item) {
                const val = item[prop]
                groups[val] = groups[val] || []
                groups[val].push(item)
                return groups
            }, {})
        };

        const products = [
            @foreach ($products as $product)
				<?php
                $r = explode(' ',$product->created_at);

		        if($roletype == 'Searcher')
			        $link = route('productsearcher.edit',$product->id);
		        else if($roletype == 'Selection')
			        $link = route('productselection.edit',$product->id);
		        else if($roletype == 'Supervisor')
			        $link = route('productsupervisor.edit',$product->id);
		        ?>
            {   'sku': '{{ $product->sku }}',
                'id' : '{{ $product->id }}',
                'size' : '{{ $product->size}}',
                'price' : '{{ $product->price }}',
                'image' : '{{ $product->getMedia(config('constants.media_tags'))->first()
                              ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
                              : ''
                           }}',
                'created_at': '{{ $r[0]  }}',
                'link' : '{{ $link }}',
                'isApproved' : '{{ $product->isApproved }}',
            },
            @endforeach
        ];

        const groupedByTime = products.groupBy('created_at');

        jQuery(document).ready(function () {

            Object.keys(groupedByTime).forEach(function(key) {

                let html = '<h4>'+getTodayYesterdayDate(key)+'</h4><div class="row">';

                groupedByTime[key].forEach( function (product) {

                    html +=  `
                        <div class="col-md-3 col-xs-6 text-center">
                        <a href="`+product['link']+`">
                            <img src="`+product['image']+`" class="img-responsive grid-image" alt="">
                                            <p>Sku : `+product['sku']+`</p>
                                            <p>Id : `+product['id']+`</p>
                                            <p>Size : `+product['size']+`</p>
                                            <p>Price : `+product['price']+`</p>
                                            @if($roletype == 'Supervisor')
                                                @can('supervisor-edit')
                                                    <button data-id="`+product['id']+`"
                                                            class="btn btn-approve btn-primary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                                            `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                                                    </button>
                                                @endcan
                                            @endif
                        </a>
                        </div>
                    `;
                });

                jQuery('#productGrid').append(html+'</div>');
            });

            @if($roletype == 'Supervisor')
            @can('supervisor-edit')
                attactApproveEvent();
            @endcan
            @endif
        });

    </script>

    {!! $products->links() !!}

@endsection