@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">
                <h2>In stock Products</h2>

                <!--Product Search Input -->
                <form action="{{ route('productinventory.instock') }}" method="GET" id="searchForm" class="form-inline align-items-start">
                    {{-- <div class="form-group">
                        <div class="row"> --}}
                            <div class="form-group mr-3 mb-3">
                                <input name="term" type="text" class="form-control" id="product-search"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="sku,brand,category,status">
                                {{-- <input hidden name="roletype" type="text" value="{{ $roletype }}"> --}}
                                {{--@if( $roletype == 'Sale' )
                                    <input hidden name="saleId" type="text" value="{{ $sale_id ?? '' }}">
                                @endif--}}
                                {{-- @if( isset($doSelection) )
                                    <input hidden name="doSelection" type="text" value="true">
                                    <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                                    <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                                    <input hidden name="assigned_user" type="text" value="{{ $assigned_user ?? '' }}">
                                    <input hidden name="status" type="text" value="{{ $status ?? '' }}">
                                @endif --}}
                            </div>
                            <div class="form-group mr-3 mb-3">
                              {{-- <strong>Category</strong> --}}
                              {!! $category_selection !!}
                            </div>

                            <div class="form-group mr-3 mb-3">
                              <strong class="mr-3">Price</strong>
                              <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]"/>

                              {{-- <select class="form-control" name="price">
                                <option value>Select Price Range</option>
                                <option value="1" {{ (isset($price) && $price == 1) ? 'selected' : '' }}>Up to 10K</option>
                                <option value="2" {{ (isset($price) && $price == 2) ? 'selected' : '' }}>10K - 30K</option>
                                <option value="3" {{ (isset($price) && $price == 3) ? 'selected' : '' }}>30K - 50K</option>
                                <option value="4" {{ (isset($price) && $price == 4) ? 'selected' : '' }}>50K - 100K</option>
                              </select> --}}
                            </div>

                            <div class="form-group mr-3">
                              {{-- <strong>Brands</strong> --}}
                              @php $brands = \App\Brand::getAll(); @endphp
            	                {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

                            <div class="form-group mr-3">
                              {{-- <strong>Color</strong> --}}
                              @php $colors = new \App\Colors(); @endphp
                              {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control', 'multiple' => true]) !!}
                            </div>

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

    <div class="row">
      @foreach ($products as $product)
        <div class="col-md-3 col-xs-6 text-center">
          <a href="{{ route('products.show', $product->id) }}">
            <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
              ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
              : ''
            }}" class="img-responsive grid-image" alt="" />
            <p>Sku : {{ $product->sku }}</p>
            <p>Id : {{ $product->id }}</p>
            <p>Size : {{ $product->size}}</p>
            <p>Price : {{ $product->price_special }}</p>
            {!! Form::open(['method' => 'POST','route' => ['products.archive', $product->id],'style'=>'display:inline']) !!}
            <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
            {!! Form::close() !!}

            @can('admin')
              {!! Form::open(['method' => 'DELETE','route' => ['products.destroy', $product->id],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
              {!! Form::close() !!}
            @endcan
          </a>
          {{-- <a href="#" class="btn btn-secondary">Something</a> --}}
        </div>
      @endforeach
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
      var image_array = [];

      $('#product-search').autocomplete({
        source: function(request, response) {
          var results = $.ui.autocomplete.filter(searchSuggestions, request.term);

          response(results.slice(0, 10));
        }
      });

    </script>
@endsection
