@extends('layouts.app')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<style>
  .checkbox_select{
    display: none;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col">
    <h2 class="page-heading">quick Sell</h2>
  </div>
</div>

  {{-- @include('quicksell.partials.modal-image') --}}



<form action="{{ route('quicksell.search') }}" method="POST" id="searchForm" class="form-inline align-items-start">
  @csrf
  {{-- <div class="form-group">
      <div class="row"> --}}
  <input type="hidden" name="selected_products" id="selected_products" value="">
  <div class="form-group mr-3 mb-3">
    <input name="term" type="text" class="form-control" id="product-search"
           value="{{ isset($term) ? $term : '' }}"
           placeholder="sku,brand,category,status,stage">
    </div>
    <div class="form-group mr-3">
      @php $category = \App\Category::all(); @endphp
      <select class="form-control select-multiple" name="category[]" multiple data-placeholder="Category...">
        <optgroup label="Category">
          @foreach ($category as $key => $name)
            <option value="{{ $key }}">{{ $name->title }}</option>
          @endforeach
        </optgroup>
      </select>
    </div>

  <div class="form-group mr-3">
    @php $brands = \App\Brand::getAll(); @endphp
    <select class="form-control select-multiple" name="brand[]" multiple data-placeholder="Brands...">
      <optgroup label="Brands">
        @foreach ($brands as $key => $name)
          <option value="{{ $key }}">{{ $name }}</option>
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
    <select class="form-control select-multiple" name="supplier[]" multiple data-placeholder="Supplier...">
      <optgroup label="Suppliers">
        @foreach ($suppliers as $key => $supp)
          <option value="{{ $supp->supplier }}" {{ isset($supplier) && $supplier == $supp->id ? 'selected' : '' }}>{{ $supp->supplier }}</option>
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
  <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
  <button type="button" class="btn btn-image"><a href="/quickSell"><img src="/images/icons-refresh.png"/></a></button>
  {{-- </div>
</div> --}}
</form>
<br>
<div>
  {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#imageModal">Upload</button> --}}
  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productModal">Upload</button>
  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productGroup">Create Group</button>
  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productGroupExist">Add Existing Group</button>
  <button type="button" class="btn btn-secondary" id="multiple">Send Multiple Images</button>
  <a href="{{ url('/quickSell/pending') }}"><button type="button" class="btn btn-secondary">Product Pending</button></a>
  <button type="button" class="btn btn-secondary" id="attached-all-quick">Attached-ALL</button>
</div>

@include('partials.flash_messages')

<div class="row mt-6" style="margin: 10px;">
  @foreach ($products as $index => $product)
  <div class="col-md-3 col-xs-6 text-left">
    <input type="checkbox" class="checkbox_select" name="quick" value="{{ $product->id }}"/>
    {{-- <a href="{{ route('leads.show', $lead['id']) }}"> --}}
    <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
              ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
              : '' }}" class="img-responsive grid-image" alt="" />
    <p>Supplier : {{ $product->supplier }}</p>
    <p>Price : {{ $product->price }}</p>
    <p>Size : {{ $product->size }}</p>
    <p>Brand : {{ $product->brand ? $brands[$product->brand] : '' }}</p>
    <p>Category : {{ $product->category ? $categories[$product->category] : '' }}</p>
    @if($product->groups)

    <p>Group : @foreach($product->groups as $group) {{ $group->quicksell_group_id }}, @endforeach</p>

    @endif
    <button type="button" class="btn btn-image sendWhatsapp" data-id="{{ $product->id }}"><img src="/images/send.png" /></button>

    <a href class="btn btn-image edit-modal-button" data-toggle="modal" data-target="#editModal" data-product="{{ $product }}"><img src="/images/edit.png" /></a>
    {!! Form::open(['method' => 'POST','route' => ['products.archive', $product->id],'style'=>'display:inline']) !!}
    <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
    {!! Form::close() !!}

     @if(auth()->user()->isAdmin())
    {!! Form::open(['method' => 'DELETE','route' => ['products.destroy', $product->id],'style'=>'display:inline']) !!}
    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
    {!! Form::close() !!}
    @endif
    {{-- </a> --}}
  </div>
  @endforeach
</div>

{!! $products->links() !!}

@include('quicksell.partials.modal-product')
@include('quicksell.partials.modal-create-group')
@include('quicksell.partials.modal-add-existing-group')
@include('quicksell.partials.modal-whats-app')
@include('quicksell.partials.modal-multiple-whats-app')

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
  <script type="text/javascript">
    $(".select-multiple").multiselect();

    $(document).on("click","#attached-all-quick",function(){
        if($(this).html() == "Attached-ALL") {
          $(this).html('Uncheck Attached-ALL');
          $("input[name='quick']").attr('checked','checked');
        }else{
          $(this).html('Attached-ALL');
          $("input[name='quick']").removeAttr('checked');
        }
    });

    $(document).on('click', '.edit-modal-button', function() {
      var product = $(this).data('product');
      var url = '/quickSell/' + product.id + '/edit';

      $('#updateForm').attr('action', url);
      $('#supplier_select').val(product.supplier);
      $('#price_field').val(product.price);
      $('#price_special_field').val(product.price_special);
      $('#size_field').val(product.size);
      $('#brand_field').val(product.brand);
      @if (Auth::user()->hasRole('Admin'))
        $('#location_field').val(product.location);
      @endif
      $('#category_selection').val(product.category);
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

    $('#product-category').on('change', function() {
      updateSizes($(this).val());
    });

    function updateSizes(category_value) {
      var found_id = 0;
      var found_final = false;
      var found_everything = false;
      var category_id = category_value;

      $('#size-selection').empty();

      $('#size-selection').append($('<option>', {
        value: '',
        text: 'Select Category'
      }));
      console.log('PARENT ID', categories_array[category_id]);
      if (categories_array[category_id] != 0) {

        Object.keys(id_list).forEach(function(id) {
          if (id == category_id) {
            $('#size-selection').empty();

            $('#size-selection').append($('<option>', {
              value: '',
              text: 'Select Category'
            }));

            id_list[id].forEach(function(value) {
              $('#size-selection').append($('<option>', {
                value: value,
                text: value
              }));
            });

            found_everything = true;
            $('#size-manual-input').addClass('hidden');
          }
        });

        if (!found_everything) {
          Object.keys(category_tree).forEach(function(key) {
            Object.keys(category_tree[key]).forEach(function(index) {
              if (index == categories_array[category_id]) {
                found_id = index;

                return;
              }
            });
          });

          console.log('FOUND ID', found_id);

          if (found_id != 0) {
            Object.keys(id_list).forEach(function(id) {
              if (id == found_id) {
                $('#size-selection').empty();

                $('#size-selection').append($('<option>', {
                  value: '',
                  text: 'Select Category'
                }));

                id_list[id].forEach(function(value) {
                  $('#size-selection').append($('<option>', {
                    value: value,
                    text: value
                  }));
                });

                $('#size-manual-input').addClass('hidden');
                found_final = true;
              }
            });
          }
        }

        if (!found_final) {
          $('#size-manual-input').removeClass('hidden');
        }
      }
    }

    $(document).ready(function() {
      $('.sendWhatsapp').on('click', function(e) {
        e.preventDefault(e);
        id = $(this).attr("data-id");
        $('#quicksell_id').val(id);
        $("#whatsappModal").modal();
      });
    });

    $(document).ready(
            function(){
              $("#multiple").click(function () {
                $(".checkbox_select").toggle();
                $(this).text("Please Select Checkbox");
                $(this).click(function () {
                  $('#multipleWhatsappModal').modal('show');
                  val = $('input[name="quick"]:checked');
                  $("#selected_checkbox").text(val.length);
                  var list = [];
                  $('input[name="quick"]:checked').each(function() {
                    list.push(this.value);
                  });
                  $("#products").val(list);
                });
              });

            });

    $(function() {
      $('.selectpicker').selectpicker();
    });
  </script>
@endsection
