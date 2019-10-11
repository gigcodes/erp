@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
<style type="text/css">
  .dis-none {
    display: none;
  }
</style>
@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <div class="">
        <h2 class="page-heading">In stock Products</h2>

        <div class="pull-right">
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#productModal">Upload Products</button>
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#updateBulkProductModal" id="bulk-update-btn">Update Products</button>
        </div>

        <!--Product Search Input -->
        <form action="{{ route('productinventory.instock') }}" method="GET" id="searchForm" class="form-inline align-items-start">
          <input type="hidden" name="type" value="{{ $type }}">
          <input type="hidden" name="date" value="{{ $date }}">
          <div class="form-group mr-3 mb-3">
            <input name="term" type="text" class="form-control" id="product-search" value="{{ isset($term) ? $term : '' }}" placeholder="sku,brand,category,status">
          </div>
          <div class="form-group mr-3 mb-3">
            {!! $category_selection !!}
          </div>

          <div class="form-group mr-3 mb-3">
            <strong class="mr-3">Price</strong>
            <input type="text" name="price" data-provide="slider" data-slider-min="0" data-slider-max="10000000" data-slider-step="10" data-slider-value="[{{ isset($price) ? $price[0] : '0' }},{{ isset($price) ? $price[1] : '10000000' }}]" />
          </div>

          <div class="form-group mr-3">
            @php $brands = \App\Brand::getAll();
            @endphp
            <select data-placeholder="Select brands"  class="form-control select-multiple2" name="brand[]" multiple>
              <optgroup label="Brands">
                @foreach ($brands as $id => $name)
                  <option value="{{ $id }}" {{ isset($brand) && $brand == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <div class="form-group mr-3">
            @php $colors = new \App\Colors();
            @endphp
            <select data-placeholder="Select color"  class="form-control select-multiple2" name="color[]" multiple>
              <optgroup label="Colors">
                @foreach ($colors->all() as $id => $name)
                  <option value="{{ $id }}" {{ isset($color) && $color == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          @if (Auth::user()->hasRole('Admin'))
            <div class="form-group mr-3">
              <select data-placeholder="Select location" class="form-control select-multiple2" name="location[]" multiple>
                <optgroup label="Locations">
                  @foreach ($locations as $name)
                    <option value="{{ $name }}" {{ isset($location) && $location == $name ? 'selected' : '' }}>{{ $name }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>

            <div class="form-group mr-3">
              <input type="checkbox" name="no_locations" id="no_locations" {{ isset($no_locations) ? 'checked' : '' }}>
              <label for="no_locations">With no Locations</label>
            </div>
          @endif

          <div class="form-group">
            <input type="checkbox" id="in_pdf" name="in_pdf"> <label for="in_pdf">Export PDF</label>
          </div>

          <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </form>


      </div>
    </div>
  </div>



  @include('instock.partials.product-modal')
  @include('instock.partials.bulk-upload-modal')

  @include('partials.flash_messages')

  <div class="productGrid" id="productGrid">
    @include('instock.product-items')
  </div>

  <div id="instruction-model" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Dispatch / Location Change</h4>
        </div>
        <form id="store-instruction-stock" action="<?php echo route("productinventory.instruction") ?>" method="post">
          <?php echo csrf_field(); ?>
          <div class="modal-body">
                                      
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary create-instruction-receipt">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>  
      </div>
    </div>
  </div>

  <div id="instruction-model-dynamic" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
          <div class="modal-body">
                                      
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </div>
    </div>
  </div>

  <form action="{{ route('stock.privateViewing.store') }}" method="POST" id="selectProductForm">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
    <input type="hidden" name="products" id="selected_products_private_viewing" value="">
  </form>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script>
    $(document).on('click', '.crt-instruction', function(e) {
      e.preventDefault();

      var $this = $(this);
      var instructionModal = $("#instruction-model");

      $.ajax({
          url: "<?php echo route('productinventory.instruction.create'); ?>",
          data : {
            product_id : $this.data("product-id")
          },
          method : "get"
        }).done(function(data) {

           instructionModal.find(".modal-body").html(data);
           $('.date-time-picker').datetimepicker({
              format: 'YYYY-MM-DD HH:mm'
           });
           instructionModal.modal("show");
        }).fail(function() {
          
        });

      /*var model = $("#instruction-model");
          model.find(".instruction-pr-id").val($(this).data("product-id"));
          model.modal("show");*/
    });

    $(document).on('change', '.instruction-type-select', function(e) {
       if($(this).val() == "dispatch") {
         $("#instruction-model").find(".dispatch-instruction").removeClass("dis-none");
       }else{
         $("#instruction-model").find(".dispatch-instruction").addClass("dis-none");
       }
    });

    $(document).on('click', '.create-instruction-receipt', function(e) {
      e.preventDefault();
      var instructionForm = $("#instruction-model").find("form");
      $.ajax({
          url: "<?php echo route('productinventory.instruction'); ?>",
          method : "post",
          data : instructionForm.serialize()
        }).done(function(data) {
           if(data.code == 0) {
             var errors = "";
             $.each(data.errors,function(kE,vE){
                $.each(vE,function(eK, Ev){
                  errors += Ev+"<br>";
                })
             });
             $("#instruction-model").find(".alert-danger").remove();
             $("#instruction-model").find(".modal-body").prepend('<div class="alert alert-danger" role="alert">'+errors+'</div>');
           }else if(data.code == 1) {
              instructionForm.find(".alert-danger").remove();
              $("#instruction-model").modal("hide");
           }
        }).fail(function() {
          
        });

    });

    $(document).on('click', '.crt-instruction-history', function(e) {
      e.preventDefault();
      var $this = $(this);
      var instructionModal = $("#instruction-model-dynamic");
      instructionModal.find(".modal-title").html("Product Location History");
      $.ajax({
          url: "<?php echo route('productinventory.location.history'); ?>",
          data : {
            product_id : $this.data("product-id")
          },
          method : "get"
        }).done(function(data) {

           instructionModal.find(".modal-body").html(data);
           instructionModal.modal("show");
        }).fail(function() {
          
        });

    });

    

    

    var product_array = [];

    $(document).ready(function() {
       $(".select-multiple").multiselect();
       $(".select-multiple2").select2();
    });

    // $('#product-search').autocomplete({
    //   source: function(request, response) {
    //     var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
    //
    //     response(results.slice(0, 10));
    //   }
    // });

    $(document).on('click', '.pagination a', function(e) {
      e.preventDefault();
      var url = $(this).attr('href');

      getProducts(url);
    });

    function getProducts(url) {
      $.ajax({
        url: url
      }).done(function(data) {
        $('#productGrid').html(data.html);
      }).fail(function() {
        alert('Error loading more products');
      });
    }

    {{--$('#searchForm').on('submit', function(e) {--}}
    {{--  e.preventDefault();--}}

    {{--  var url = "{{ route('productinventory.instock') }}";--}}
    {{--  var formData = $('#searchForm').serialize();--}}

    {{--  $.ajax({--}}
    {{--    url: url,--}}
    {{--    data: formData--}}
    {{--  }).done(function(data) {--}}
    {{--    $('#productGrid').html(data.html);--}}
    {{--  }).fail(function() {--}}
    {{--    alert('Error searching for products');--}}
    {{--  });--}}
    {{--});--}}

    $(document).on('click', '.select-product', function(e) {
      e.preventDefault();
      var product_id = $(this).data('id');

      if ($(this).data('attached') == 0) {
        $(this).data('attached', 1);
        product_array.push(product_id);
      } else {
        var index = product_array.indexOf(product_id);

        $(this).data('attached', 0);
        product_array.splice(index, 1);
      }

      console.log(product_array);

      $(this).toggleClass('btn-success');
      $(this).toggleClass('btn-secondary');
    });

    $(document).on('click', '#privateViewingButton', function() {
      if (product_array.length == 0) {
        alert('Please select some products');
      } else {
        $('#selected_products_private_viewing').val(JSON.stringify(product_array));
        $('#selectProductForm').submit();
      }
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
  </script>
@endsection
