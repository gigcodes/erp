@extends('layouts.app')

@section('title', 'Products Grid - ERP Sololuxury')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="">

                <!--roletype-->
                <h2 class="page-heading">{{ $roletype }}
                  @if (isset($products_count))
                    ({{ $products_count }})
                  @endif
                </h2>

                <!--pending products count-->
                @can('admin')
                  @if( $roletype != 'Selection' && $roletype != 'Sale' )
                      <div class="pt-2 pb-3">
                          <a href="{{ route('pending',$roletype) }}"><strong>Pending
                                  : </strong> {{ \App\Product::getPendingProductsCount($roletype) }}</a>
                      </div>
                      @if ($roletype == 'Inventory')
                        <form class="form-inline mb-3" action="{{ route('productinventory.import') }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <div class="form-group">
                            <input type="file" name="file" class="form-control-file" required>
                          </div>

                          <button type="submit" class="btn btn-secondary ml-3">Import Inventory</button>
                        </form>
                      @endif
                  @endif
                @endcan

                <!--attach Product-->
                @if( isset($doSelection) )
                    <p><strong> {{ strtoupper($model_type)  }} ID : {{ $model_id }} </strong></p>
                @endif

                <!--Product Search Input -->
                <form action="{{ route('search') }}" method="GET" class="form-inline align-items-start">
                    <div class="form-group mr-3 mb-3">
                                <input name="term" type="text" class="form-control" id="product-search"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="sku,brand,category,status,stage">
                                <input hidden name="roletype" type="text" value="{{ $roletype }}">
                                @if( isset($doSelection) )
                                    <input hidden name="doSelection" type="text" value="true">
                                    <input hidden name="model_id" type="text" value="{{ $model_id ?? '' }}">
                                    <input hidden name="model_type" type="text" value="{{ $model_type ?? '' }}">
                                @endif

                                @if (isset($attachImages) && $attachImages == true)
                                  <input type="hidden" name="model_type" value="broadcast-images">
                                @endif
                              </div>
                            <div class="form-group mr-3 mb-3">
                              {!! $category_selection !!}
                            </div>

                            <div class="form-group mr-3">
                              @php $brands = \App\Brand::getAll(); @endphp
                              {{-- {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                              <select class="form-control select-multiple" name="brand[]" multiple>
                                <optgroup label="Brands">
                                  @foreach ($brands as $key => $name)
                                    <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                                  @endforeach
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              @php $colors = new \App\Colors(); @endphp
                              {{-- {!! Form::select('color[]',$colors->all(), (isset($color) ? $color : ''), ['placeholder' => 'Select a Color','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                              <select class="form-control select-multiple" name="color[]" multiple>
                                <optgroup label="Colors">
                                  @foreach ($colors->all() as $key => $col)
                                    <option value="{{ $key }}" {{ isset($color) && $color == $key ? 'selected' : '' }}>{{ $col }}</option>
                                  @endforeach
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              {{-- @php $suppliers = new \App\ReadOnly\SupplierList(); @endphp --}}
                              @php $suppliers = \Illuminate\Support\Facades\DB::select('
                          				SELECT id, supplier
                          				FROM suppliers

                          				INNER JOIN (
                          					SELECT supplier_id FROM product_suppliers GROUP BY supplier_id
                          					) as product_suppliers
                          				ON suppliers.id = product_suppliers.supplier_id
                          		'); @endphp
                              {{-- {!! Form::select('supplier[]',$suppliers->all(), (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control select-multiple', 'multiple' => true]) !!} --}}
                              <select class="form-control select-multiple" name="supplier[]" multiple>
                                <optgroup label="Suppliers">
                                  @foreach ($suppliers as $key => $item)
                                    <option value="{{ $item->id }}" {{ isset($supplier) && in_array($item->id, $supplier) ? 'selected' : '' }}>{{ $item->supplier }}</option>
                                  @endforeach
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              <select class="form-control select-multiple" name="type[]" multiple>
                                <optgroup label="Type">
                                  <option value="scraped" {{ isset($type) && $type == 'scraped' ? 'selected' : '' }}>Scraped</option>
                                  <option value="imported" {{ isset($type) && $type == 'imported' ? 'selected' : '' }}>Imported</option>
                                  <option value="uploaded" {{ isset($type) && $type == 'uploaded' ? 'selected' : '' }}>Uploaded</option>
                              </optgroup>
                              </select>
                            </div>

                            <div class="form-group mr-3">
                              <input name="size" type="text" class="form-control"
                                     value="{{ isset($size) ? $size : '' }}"
                                     placeholder="Size">
                            </div>

                            <div class="form-group mr-3">
                              <strong class="mr-3">Price</strong>

                              <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="400000" data-slider-step="1000" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '400000' }}]"/>
                            </div>

                            <div class="form-group ml-3">
                              <div class='input-group date' id='filter-date'>
                                  <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" placeholder="Date" />

                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                            </div>

                            @if (isset($customer_id) && $customer_id != null)
                              <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                            @endif

                            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </form>

                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#updateBulkProductModal" id="bulk-update-btn">Update Products</button>
            </div>
        </div>
    </div>


    @include('partials.flash_messages')

    {!! $products->appends(Request::except('page'))->links() !!}

	<?php
	$query = http_build_query( Request::except( 'page' ) );
	$query = url()->current() . ( ( $query == '' ) ? $query . '?page=' : '?' . $query . '&page=' );
	?>

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;" class="form-control">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="productGrid" id="productGrid">

    </div>

    @if (isset($attachImages) && $attachImages)
      <form class="text-center" action="{{ route('broadcast.images.link') }}" method="POST">
        @csrf
        <input type="hidden" name="moduleid" value="{{ $model_id }}">
        <input type="hidden" name="products" value="" id="linked_products">

        <button type="submit" class="btn btn-secondary" id="linkProductsSubmit">Link Products</button>
      </form>
    @endif

    @if (isset($customer_id) && $customer_id != null)
      <div class="row">
        <div class="col text-center">
          <a href="{{ route('customer.show', $customer_id) }}" class="btn btn-secondary">Go to Customer Page</a>
        </div>
      </div>
    @endif

    {!! $products->appends(Request::except('page'))->links() !!}

    <div class="row">
        <div class="col-2">
            <div class="form-group">
                Goto :
                <select onchange="location.href = this.value;" class="form-control">
                    @for($i = 1 ; $i <= $products->lastPage() ; $i++ )
                        <option value="{{ $query.$i }}" {{ ($i == $products->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div id="updateBulkProductModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Update Bulk</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <form action="{{ route('products.bulk.update') }}" method="POST" enctype="multipart/form-data">


          <div class="modal-body">
            @csrf
            <input type="hidden" name="selected_products" id="selected_products" value="">

            <div class="form-group">
                <strong>Category:</strong>
                {!! $category_selection !!}
                @if ($errors->has('category'))
                    <div class="alert alert-danger">{{$errors->first('category')}}</div>
                @endif
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary" id="bulkUpdateButton">Update</button>
          </div>
          </form>
        </div>

      </div>
    </div>

	<?php $stage = new \App\Stage(); ?>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script>

    $(document).ready(function() {
       $(".select-multiple").multiselect();
    });

    $('#filter-date').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    var select_products_edit_array = [];

    $(document).on('click', '.select-product-edit', function() {
      var id = $(this).data('id');

      if ($(this).prop('checked')) {
        select_products_edit_array.push(id);
      } else {
        var index = select_products_edit_array.indexOf(id);

        select_products_edit_array.splice(index, 1);
      }

      console.log(select_products_edit_array);
    });

    $(document).on('click', '#linkProductsSubmit', function(e) {
      e.preventDefault();

      if (select_products_edit_array.length > 0) {
        $('#linked_products').val(JSON.stringify(select_products_edit_array));

        $(this).closest('form').submit();
      } else {
        alert('Please select some products');
      }
    });

    $('#bulk-update-btn').on('click', function(e) {
      if (select_products_edit_array.length == 0) {
        e.stopPropagation();

        alert('Please select atleast 1 product!');
      }
    });

    $('#bulkUpdateButton').on('click', function() {
      $('#selected_products').val(JSON.stringify(select_products_edit_array));

      $(this).closest('form').submit();
    });



    // $('#product-search').autocomplete({
    //   source: function(request, response) {
    //     var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
    //
    //     response(results.slice(0, 10));
    //   }
    // });

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
          // $link = route( 'productsearcher.edit', $product->id );
          $link = route( 'productattribute.edit', $product->id );
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
              'sku': '{{ strlen($product->sku) > 18 ? substr($product->sku, 0, 15) . '...' : $product->sku }}',
              'id': '{{ $product->id }}',
              'size': '{{ strlen($product->size) > 17 ? substr($product->size, 0, 14) . '...' : $product->size }}',
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
              'is_scraped': {{ $product->is_scraped ?? 0 }},
              'is_imported': {{ $product->status == 2 ? 1 : 0 }},
              'supplier' : "{{ $product->supplier }}",
              @php
                $supplier_list = '';
              @endphp
              @foreach ($product->suppliers as $key => $supplier)
                @php
                  if ($key == 0) {
                    $supplier_list .= "$supplier->supplier";
                  } else {
                    $supplier_list .= ", $supplier->supplier";
                  }
                @endphp
              @endforeach
              'suppliers' : "{{ $supplier_list }}",
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
                var is_scraped = product['is_scraped'] == 1 ? '<p><span class="badge">Scraped</span></p>' : '';
                var is_imported = product['is_imported'] == 1 ? '<p><span class="badge">Imported</span></p>' : '';

                  html += `
                      <div class="col-md-3 col-xs-6 text-center mb-5">
                      <a href="` + product['link'] + `">
                          <img src="` + product['image'] + `" class="img-responsive grid-image" alt="" />
                                          <p>Sku : ` + product['sku'] + `</p>
                                          <p>Id : ` + product['id'] + `</p>
                                          <p>Size : ` + product['size'] + `</p>
                                          <p>Price : ` + product['price'] + `</p>
                                          <!--<p>Brand : ` + product['brand'] + `</p>-->
                                          <p>Status : ` + product['stage'] + `</p>

                                          <p>Supplier : ` + product['supplier'] + `</p>
                                          <p>Suppliers : ` + product['suppliers'] + `</p>
                                          ` + is_scraped + is_imported + `

                                          <input type="checkbox" class="select-product-edit" name="product_id" data-id="` + product['id'] + `">
                                           {{--<p>Status : `+ ( ( product['isApproved'] ===  '1' ) ?
                                                                  'Approved' : ( product['isApproved'] ===  '-1' ) ? 'Rejected' : 'Nil') +`</p>--}}
                          {{--@can('supervisor-edit')
                              <button data-id="`+product['id']+`"
                                      class="btn btn-approve btn-secondary `+ ( ( product['isApproved'] ===  '1' ) ? 'btn-success' : '' ) +` ">
                                      `+ ( ( product['isApproved'] ===  '1' ) ? 'Approved' : 'Approve' ) +`
                              </button>
                          @endcan--}}
                      </a>
                                          @if( isset($doSelection) && $doSelection == true)

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
                    // 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                      'X-CSRF-TOKEN': "{{ csrf_token() }}"
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
