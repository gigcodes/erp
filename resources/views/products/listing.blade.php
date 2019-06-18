@extends('layouts.app')

@section('title', 'Product Listing')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <style>
    .quick-edit-color {
      transition: 1s ease-in-out;
    }
  </style>
@endsection

@section('content')
  <div class="row">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Product Listing ({{ $products_count }})</h2>

      <div class="pull-left">
        <form class="form-inline" action="{{ route('products.listing') }}" method="GET">
          <div class="form-group mr-3 mb-3">
            <input type="checkbox" name="cropped" id="cropped" {{ isset($cropped) && $cropped == 'on' ? 'checked' : '' }}> <label for="cropped"><strong>Cropped</strong></label>
          </div>

          <div class="form-group mr-3 mb-3">
            <input name="term" type="text" class="form-control"
            value="{{ isset($term) ? $term : '' }}"
            placeholder="sku,brand,category,status,stage">
          </div>

          <div class="form-group mr-3 mb-3">
            {!! $category_search !!}
          </div>

          <div class="form-group mr-3">
            <select class="form-control select-multiple" name="brand[]" multiple>
              <optgroup label="Brands">
                @foreach ($brands as $key => $name)
                  <option value="{{ $key }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <div class="form-group mr-3">
            <select class="form-control select-multiple" name="color[]" multiple>
              <optgroup label="Colors">
                @foreach ($colors as $key => $col)
                  <option value="{{ $key }}" {{ isset($color) && $color == $key ? 'selected' : '' }}>{{ $col }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <div class="form-group mr-3">
            <select class="form-control select-multiple" name="supplier[]" multiple>
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
              <option value="Approved" {{ isset($type) && $type == "Approved" ? 'selected' : ''  }}>Approved</option>
              <option value="Image Cropped" {{ isset($type) && $type == "Image Cropped" ? 'selected' : ''  }}>Image Cropped</option>
            </select>
          </div>

          @if (Auth::user()->hasRole('Admin'))
            <div class="form-group mr-3">
              <input type="checkbox" name="users" id="users" {{ $assigned_to_users == 'on' ? 'checked' : '' }}> <label for="users"><strong>Assigned To Users</strong></label>
            </div>
          @endif

          <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </form>
      </div>

      {{-- <div class="pull-right">
        <button type="button" class="btn btn-secondary mb-3" data-toggle="modal" data-target="#createTaskModal">Add Task</button>
      </div> --}}
    </div>
  </div>

  {{-- @include('development.partials.modal-task')
  @include('development.partials.modal-quick-task') --}}

  @include('partials.flash_messages')

  <div class="infinite-scroll mt-5">
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <tr>
          <th width="10%">Thumbnail</th>
          <th width="10%">Name</th>
          <th width="5%">Crop Count</th>
          <th width="20%">Description</th>
          <th width="10%">Category</th>
          <th width="10%">Sizes</th>
          <th width="10%">Composition</th>
          <th width="10%">Color</th>
          <th width="5%">Price</th>
          <th width="5%">Cropper</th>
          <th width="5%">Action</th>
        </tr>

        @foreach ($products as $key => $product)
          <tr id="product_{{ $product->id }}">
            @if (!Auth::user()->hasRole('ImageCropers'))
              <td>
                @if ($product->is_approved == 1)
                  <img src="/images/1.png" alt="">
                @endif

                @php $special_product = \App\Product::find($product->id) @endphp
                @if ($special_product->hasMedia(config('constants.media_tags')))
                  <a href="{{ route('products.show', $product->id) }}" target="_blank">
                    <img src="{{ $special_product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="quick-image-container img-responive" style="width: 100px;" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product->id }}">
                  </a>
                @else
                  <img src="" class="quick-image-container img-responive" style="width: 100px;" alt="">
                @endif

                {{ (new \App\Stage)->getNameById($product->stage) }}
                <br>
                SKU: {{ $product->sku }}
              </td>
              <td class="table-hover-cell quick-edit-name" data-id="{{ $product->id }}">
                <span class="quick-name">{{ $product->name }}</span>
                <input type="text" name="name" class="form-control quick-edit-name-input hidden" placeholder="Product Name" value="{{ $product->name }}">
              </td>

              <td>
                {{ $product->crop_count }}
              </td>

              <td class="read-more-button table-hover-cell">
                <span class="short-description-container">{{ substr($product->short_description, 0, 100) . (strlen($product->short_description) > 100 ? '...' : '') }}</span>

                <span class="long-description-container hidden">
                  <span class="description-container">{{ $product->short_description }}</span>

                  <textarea name="description" class="form-control quick-description-edit-textarea hidden" rows="8" cols="80">{{ $product->short_description }}</textarea>
                </span>

                <button type="button" class="btn-link quick-edit-description" data-id="{{ $product->id }}">Edit</button>
              </td>

              <td class="table-hover-cell">
                {!! $category_selection !!}
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="category_id" value="{{ $product->category }}">
                <input type="hidden" name="sizes" value='{{ $product->size }}'>
              </td>

              <td class="table-hover-cell">
                <select class="form-control quick-edit-size select-multiple" name="size[]" multiple>
                  <option value="">Select Size</option>
                </select>

                {{-- <input type="text" name="other_size" class="form-control mt-3 hidden" placeholder="Manual Size" value="{{ is_array(explode(',', $product->size)) && count(explode(',', $product->size)) > 1 ? '' : $product->size }}"> --}}
                <span class="lmeasurement-container">
                  <strong>L:</strong>
                  <input type="number" name="lmeasurement" class="form-control mt-1" placeholder="12" min="0" max="999" value="{{ $product->lmeasurement }}">
                </span>

                <span class="hmeasurement-container">
                  <strong>H:</strong>
                  <input type="number" name="hmeasurement" class="form-control mt-1" placeholder="14" min="0" max="999" value="{{ $product->hmeasurement }}">
                </span>

                <span class="dmeasurement-container">
                  <strong>D:</strong>
                  <input type="number" name="dmeasurement" class="form-control mt-1" placeholder="16" min="0" max="999" value="{{ $product->dmeasurement }}">
                </span>

                <button type="button" class="btn-link quick-edit-size-button" data-id="{{ $product->id }}">Save</button>
              </td>
              <td class="table-hover-cell quick-edit-composition" data-id="{{ $product->id }}">
                <span class="quick-composition">{{ $product->composition }}</span>
                <input type="text" name="composition" class="form-control quick-edit-composition-input hidden" placeholder="Composition" value="{{ $product->composition }}">
              </td>

              <td class="table-hover-cell">
                <select class="form-control quick-edit-color" name="color" data-id="{{ $product->id }}">
                  <option value="">Select a Color</option>

                  @foreach ($colors as $color)
                    <option value="{{ $color }}" {{ $product->color == $color ? 'selected' : '' }}>{{ $color }}</option>
                  @endforeach
                </select>
              </td>

              <td class="table-hover-cell quick-edit-price" data-id="{{ $product->id }}">
                <span class="quick-price">{{ $product->price }}</span>
                <input type="number" name="price" class="form-control quick-edit-price-input hidden" placeholder="100" value="{{ $product->price }}">

                <span class="quick-price-inr">{{ $product->price_inr }}</span>
                <span class="quick-price-special">{{ $product->price_special }}</span>
              </td>

              <td>
                @if ($special_product->hasMedia(config('constants.media_tags')))
                  <a href="{{ route('products.quick.download', $product->id) }}" class="btn btn-xs btn-secondary mb-1 quick-download">Download</a>
                @endif

                <input type="file" class="dropify quick-images-upload-input" name="images[]" value="" data-height="100" multiple>

                <div class="form-inline">
                  <button type="button" class="btn btn-xs btn-secondary mt-1 quick-images-upload" data-id="{{ $product->id }}">Upload</button>

                  @if ($product->last_imagecropper != '')
                    <img src="/images/1.png" class="ml-1" alt="">
                  @endif
                </div>
              </td>

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

                {{-- <button type="button" data-toggle="modal" data-target="#editTaskModal" data-task="{{ $task }}" class="btn btn-image edit-task-button"><img src="/images/edit.png" /></button> --}}

                {{-- <button type="button" class="btn btn-image task-delete-button" data-id="{{ $task->id }}"><img src="/images/archive.png" /></button> --}}
              </td>
            @else
              <td>
                @if ($product->is_approved == 1)
                  <img src="/images/1.png" alt="">
                @endif

                @php $special_product = \App\Product::find($product->id) @endphp
                @if ($special_product->hasMedia(config('constants.media_tags')))
                  <a href="{{ route('products.show', $product['id']) }}" target="_blank">
                    <img src="{{ $special_product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="quick-image-container img-responive" style="width: 100px;" alt="" data-toggle="tooltip" data-placement="top" title="ID: {{ $product['id'] }}">
                  </a>
                @else
                  <img src="" class="quick-image-container img-responive" style="width: 100px;" alt="">
                @endif

                {{ (new \App\Stage)->getNameById($product->stage) }}
                <br>
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

              <td>
                @if ($special_product->hasMedia(config('constants.media_tags')))
                  <a href="{{ route('products.quick.download', $product->id) }}" class="btn btn-xs btn-secondary mb-1 quick-download">Download</a>
                @endif

                <input type="file" class="dropify quick-images-upload-input" name="images[]" value="" data-height="100" multiple>

                <button type="button" class="btn btn-xs btn-secondary mt-1 quick-images-upload" data-id="{{ $product->id }}">Upload</button>
              </td>

              <td>
                {{ $product->isUploaded }} {{ $product->isFinal }}

                @if ($product->is_approved == 0)
                  <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="approve">Approve</button>
                @elseif ($product->is_approved == 1 && $product->isUploaded == 0)
                  <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="list">List</button>
                @elseif ($product->is_approved == 1 && $product->isUploaded == 1 && $product->isFinal == 0)
                  <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="enable">Enable</button>
                @else
                  <button disabled type="button" class="btn btn-xs btn-secondary upload-magento" data-id="{{ $product->id }}" data-type="update">Update</button>
                @endif

                @if ($product->product_user_id != null)
                  {{ \App\User::find($product->product_user_id)->name }}
                @endif
              </td>
            @endif
          </tr>
        @endforeach
      </table>
    </div>

    {!! $products->appends(Request::except('page'))->links() !!}
  </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('ul.pagination').hide();
      $(function() {
          $('.infinite-scroll').jscroll({
              autoTrigger: true,
              loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
              padding: 2500,
              nextSelector: '.pagination li.active + li a',
              contentSelector: 'div.infinite-scroll',
              callback: function() {
                  // $('ul.pagination').remove();
                  $('.dropify').dropify();
              }
          });
      });

      $('.dropify').dropify();
      $(".select-multiple").multiselect();
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

    $('.quick-edit-category').each(function(item) {
      product_id = $(this).siblings('input[name="product_id"]').val();
      category_id = $(this).siblings('input[name="category_id"]').val();
      sizes = $(this).siblings('input[name="sizes"]').val();
      selected_sizes = sizes.split(',');

      $(this).attr('data-id', product_id);
      $(this).find('option[value="' + category_id + '"]').prop('selected', true);

      updateSizes(this, category_id);

      for (var i = 0; i < selected_sizes.length; i++) {
        $(this).closest('tr').find('.quick-edit-size option[value="' + selected_sizes[i] + '"]').attr('selected', 'selected');
      }
    });

    $(document).on('click', '.edit-task-button', function() {
      var task = $(this).data('task');
      var url = "{{ url('development') }}/" + task.id + "/edit";

      @can('developer-all')
        $('#user_field').val(task.user_id);
      @endcan
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

    $(document).on('dblclick', '.quick-edit-name', function() {
      var id = $(this).data('id');

      $(this).find('.quick-name').addClass('hidden');
      $(this).find('.quick-edit-name-input').removeClass('hidden');
      $(this).find('.quick-edit-name-input').focus();

      $(this).find('.quick-edit-name-input').keypress(function(e) {
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
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.quick-name').text(name);
            $(thiss).siblings('.quick-name').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update name');
          });
        }
      });
    });

    $(document).on('dblclick', '.quick-edit-composition', function() {
      var id = $(this).data('id');

      $(this).find('.quick-composition').addClass('hidden');
      $(this).find('.quick-edit-composition-input').removeClass('hidden');
      $(this).find('.quick-edit-composition-input').focus();

      $(this).find('.quick-edit-composition-input').keypress(function(e) {
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
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.quick-composition').text(composition);
            $(thiss).siblings('.quick-composition').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update composition');
          });
        }
      });
    });

    $(document).on('change', '.quick-edit-color', function() {
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
      }).done(function() {
        $(thiss).css({border: "2px solid green"});

        setTimeout(function () {
          $(thiss).css({border: "1px solid #ccc"});
        }, 2000);
      }).fail(function(response) {
        alert('Could not update the color');
        console.log(response);
      });
    });

    $(document).on('change', '.quick-edit-category', function() {
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
      }).done(function() {
        $(thiss).css({border: "2px solid green"});

        setTimeout(function () {
          $(thiss).css({border: "1px solid #ccc"});
        }, 2000);
      }).fail(function(response) {
        alert('Could not update the category');
        console.log(response);
      });

      updateSizes(thiss, $(thiss).val());
    });

    $(document).on('click', '.quick-edit-size-button', function() {
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
        beforeSend: function() {
          $(thiss).text('Saving...');
        }
      }).done(function() {
        $(thiss).text('Save');
        $(thiss).css({color: "green"});

        setTimeout(function () {
          $(thiss).css({color: "inherit"});
        }, 2000);
      }).fail(function(response) {
        $(thiss).text('Save');
        alert('Could not update the category');
        console.log(response);
      });
    });

    $(document).on('dblclick', '.quick-edit-price', function() {
      var id = $(this).data('id');

      $(this).find('.quick-price').addClass('hidden');
      $(this).find('.quick-edit-price-input').removeClass('hidden');
      $(this).find('.quick-edit-price-input').focus();

      $(this).find('.quick-edit-price-input').keypress(function(e) {
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
          }).done(function(response) {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.quick-price').text(price);
            $(thiss).siblings('.quick-price').removeClass('hidden');

            $(thiss).siblings('.quick-price-inr').text(response.price_inr);
            $(thiss).siblings('.quick-price-special').text(response.price_special);
          }).fail(function(response) {
            console.log(response);

            alert('Could not update price');
          });
        }
      });
    });

    $(document).on('click', '.quick-images-upload', function() {
      var id = $(this).data('id');
      var thiss = $(this);
      var images = $(this).closest('td').find('input[type="file"]').prop('files');
      var images_array = [];
      var form_data = new FormData();
      console.log(images);
      console.log($(this).closest('td').find('input[type="file"]'));

      form_data.append('_token', "{{ csrf_token() }}");

      Object.keys(images).forEach(function(index) {
        form_data.append('images[]', images[index]);
      });

      $.ajax({
        type: 'POST',
        url: "{{ url('products') }}/" + id + '/quickUpload',
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        data: form_data
      }).done(function(response) {
        $(thiss).closest('tr').find('.quick-image-container').attr('src', response.image_url);
        $(thiss).closest('td').find('.dropify-clear').click();

        $(thiss).parent('div').find('img').remove();
        $(thiss).parent('div').append('<img src="/images/1.png" class="ml-1" alt="">');
      }).fail(function(response) {
        console.log(response);

        alert('Could not upload images');
      });
    });

    $(document).on('click', '.read-more-button', function() {
      var selection = window.getSelection();

      if (selection.toString().length === 0) {
        $(this).find('.short-description-container').toggleClass('hidden');
        $(this).find('.long-description-container').toggleClass('hidden');
      }
    });

    $(document).on('click', '.quick-description-edit-textarea', function(e) {
      e.stopPropagation();
    });

    $(document).on('click', '.quick-edit-description', function(e) {
      e.stopPropagation();

      var id = $(this).data('id');

      $(this).siblings('.long-description-container').removeClass('hidden');
      $(this).siblings('.short-description-container').addClass('hidden');

      $(this).siblings('.long-description-container').find('.description-container').addClass('hidden');
      $(this).siblings('.long-description-container').find('.quick-description-edit-textarea').removeClass('hidden');

      $(this).siblings('.long-description-container').find('.quick-description-edit-textarea').keypress(function(e) {
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
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.description-container').text(description);
            $(thiss).siblings('.description-container').removeClass('hidden');
            $(thiss).siblings('.quick-description-edit-textarea').addClass('hidden');

            var short_description = description.substr(0, 100);

            $(thiss).closest('.long-description-container').siblings('.short-description-container').text(short_description);
          }).fail(function(response) {
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

        Object.keys(id_list).forEach(function(id) {
          if (id == category_id) {
            $(element).closest('tr').find('.quick-edit-size').empty();

            $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
              value: '',
              text: 'Select Category'
            }));

            id_list[id].forEach(function(value) {
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
                $(element).closest('tr').find('.quick-edit-size').empty();

                $(element).closest('tr').find('.quick-edit-size').append($('<option>', {
                  value: '',
                  text: 'Select Category'
                }));

                id_list[id].forEach(function(value) {
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

    $(document).on('click', '.upload-magento', function() {
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
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
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
      }).fail(function(response) {
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
  </script>
@endsection
