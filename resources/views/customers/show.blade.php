@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h2>Customer Page</h2>
    </div>
    <div class="pull-right">
      <a class="btn btn-secondary" href="{{ route('customer.index') }}">Back</a>
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif

<div id="exTab2" class="container">
  <ul class="nav nav-tabs">
    <li class="active">
      <a href="#1" data-toggle="tab">Customer Info</a>
    </li>
    <li>
      <a href="#2" data-toggle="tab">Leads</a>
    </li>
    <li><a href="#3" data-toggle="tab">Orders</a>
    </li>
  </ul>
</div>

<div class="tab-content ">
  <div class="tab-pane active mt-3" id="1">
    <div class="row">
      <div class="col-md-6 col-12">
        <div class="form-group">
          <strong>Name:</strong> {{ $customer->name }}
        </div>

        <div class="form-group">
          <strong>Email:</strong> {{ $customer->email }}
        </div>

        <div class="form-group">
          <strong>Phone:</strong> {{ $customer->phone }}
        </div>

        <div class="form-group">
          <strong>Instagram Handle:</strong> {{ $customer->instahandler }}
        </div>

        <div class="form-group">
          <strong>Rating:</strong> {{ $customer->rating }}
        </div>

        <div class="form-group">
          <strong>Address:</strong> {{ $customer->address }}
        </div>

        <div class="form-group">
          <strong>City:</strong> {{ $customer->city }}
        </div>

        <div class="form-group">
          <strong>Country:</strong> {{ $customer->country }}
        </div>
      </div>
    </div>
  </div>

  <div class="tab-pane mt-3" id="2">
    @if (count($customer->leads) > 0)
      @foreach ($customer->leads as $key => $lead)
        <h2><a href="{{ route('leads.show', $lead->id) }}" target="_blank">Lead {{ $key + 1 }}</a></h2>
        <form action="{{ route('leads.update', $lead->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="customer">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                  <strong>Brand:</strong>
                  <select multiple="" name="multi_brand[]" class="form-control multi_brand">
                    @php $multi_brand = is_array(json_decode($lead->multi_brand,true) ) ? json_decode($lead->multi_brand,true) : []; @endphp
                      @foreach($brands as $brand_item)
                          <option value="{{$brand_item['id']}}" {{ in_array($brand_item['id'] ,$multi_brand) ? 'Selected=Selected':''}}>{{$brand_item['name']}}</option>
                      @endforeach
                  </select>

              </div>

              <div class="form-group">
                  <strong>Categories</strong>
                  @php
                  $selected_categories = is_array(json_decode( $lead->multi_category,true)) ? json_decode( $lead->multi_category ,true) : [] ;
                  $category_selection = \App\Category::attr(['name' => 'multi_category[]','class' => 'form-control multi_category'])
                                                     ->selected($selected_categories)
                                                     ->renderAsMultiple();
                  @endphp
                  {!! $category_selection  !!}
              </div>

              <div class="form-group">
                  <strong> Selected Product :</strong>

                  <select name="selected_product[]" class="select2{{ $key + 1 }} form-control" multiple="multiple"></select>

                  @if ($errors->has('selected_product'))
                      <div class="alert alert-danger">{{$errors->first('selected_product')}}</div>
                  @endif
              </div>

              <script type="text/javascript">
                $(document).ready(function() {
                  var key = {{ $key + 1 }};
                  jQuery('.select2' + key).select2({
                      ajax: {
                          url: '/productSearch/',
                          dataType: 'json',
                          delay: 750,
                          data: function (params) {
                              return {
                                  q: params.term, // search term
                              };
                          },
                          processResults: function (data,params) {

                              params.page = params.page || 1;

                              return {
                                  results: data,
                                  pagination: {
                                      more: (params.page * 30) < data.total_count
                                  }
                              };
                          },
                      },
                      placeholder: 'Search for Product by id, Name, Sku',
                      escapeMarkup: function (markup) { return markup; },
                      minimumInputLength: 5,
                      width: '100%',
                      templateResult: formatProduct,
                      templateSelection:function(product) {
                        console.log('YRA');
                        console.log(product.id);
                           return product.text || product.name;
                       },

                  });




                    @php
                    $selected_products_array = json_decode( $lead->selected_product );
                    $products_array = [];

                    if ( ! empty( $selected_products_array  ) ) {
                        foreach ($selected_products_array  as $product_id) {
                            $product = \App\Product::find($product_id);

                           $products_array[$product_id] = $product->name ? $product->name : $product->sku;
                        }
                    }
                    @endphp
                    @if(!empty($products_array ))
                      let data = [
                              @forEach($products_array as $key => $value)
                          {
                              'id': '{{ $key }}',
                              'text': '{{$value  }}',
                          },
                          @endforeach
                      ];
                  @endif

                  let productSelect = jQuery('.select2' + key);
                  // create the option and append to Select2

                  data.forEach(function (item) {

                      var option = new Option(item.text,item.id , true, true);
                      productSelect.append(option).trigger('change');

                      // manually trigger the `select2:select` event
                      productSelect.trigger({
                          type: 'select2:select',
                          params: {
                              data: item
                          }
                      });

                  });

                  function formatProduct (product) {
                      if (product.loading) {
                          return product.sku;
                      }

                      return "<p> <b>Id:</b> " +product.id  + (product.name ? " <b>Name:</b> "+product.name : "" ) +  " <b>Sku:</b> "+product.sku+" </p>";
                  }
                });
              </script>

              <div class="form-group">
                  <strong>status:</strong>
                  <Select name="status" class="form-control" id="change_status" data-leadid="{{ $lead->id }}">
                       @foreach($status as $key => $value)
                        <option value="{{$value}}" {{$value == $lead->status ? 'Selected=Selected':''}}>{{$key}}</option>
                        @endforeach
                  </Select>
                  <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>

                  <input type="hidden" class="form-control" name="userid" placeholder="status" value="{{$lead->userid}}"/>

              </div>

               <div class="form-group">
                   <strong>Created by:</strong>

                   <input type="text" class="form-control" name="" placeholder="Created by" value="{{ App\Helpers::getUserNameById($lead->userid) }}" readonly/>
               </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                  <strong>Comments:</strong>
                  <textarea  class="form-control" name="comments" placeholder="comments">{{$lead->comments}} </textarea>
              </div>

              <div class="form-group">
                <strong>Sizes:</strong>
                <input type="text" name="size" value="{{ $lead->size }}" class="form-control" placeholder="S, M, L">
              </div>

                <div class="form-group">
                    <strong>Assigned To:</strong>
                    <Select name="assigned_user" class="form-control">

                            @foreach($users as $user)
                          <option value="{{$user['id']}}" {{$user['id']== $lead->assigned_user ? 'Selected=Selected':''}}>{{$user['name']}}</option>
                          @endforeach
                    </Select>
                </div>

                <?php $images = $lead->getMedia(config('constants.media_tags')) ?>
                @foreach ($images as $key => $image)
                  <div class="old-image{{ $key }}" style="
                       @if ($errors->has('image'))
                          display: none;
                       @endif
                  ">
                    <p>
                      <img src="{{ $image->getUrl() }}" class="img-responsive" style="max-width: 200px;"  alt="">
                      <button class="btn btn-image removeOldImage" data-id="{{ $key }}" media-id="{{ $image->id }}"><img src="/images/delete.png" /></button>

                      <input type="text" hidden name="oldImage[{{ $key }}]" value="{{ $images ? '0' : '-1' }}">
                   </p>
                </div>
                @endforeach

                @if (count($images) == 0)
                  <input type="text" hidden name="oldImage[0]" value="{{ $images ? '0' : '-1' }}">
                @endif

                 <div class="form-group new-image" style="">
                     <strong>Upload Image:</strong>
                     <input  type="file" enctype="multipart/form-data" class="form-control" name="image[]" multiple />
                     @if ($errors->has('image'))
                         <div class="alert alert-danger">{{$errors->first('image')}}</div>
                     @endif
                 </div>
            </div>

              <div class="col-xs-12 text-center">
                  <div class="form-group">
                      <button type="submit" class="btn btn-secondary">Update</button>
                  </div>
              </div>

          </div>
   </form>
      @endforeach
    @else
      There are no leads for this customer
    @endif
  </div>

  <div class="tab-pane mt-3" id="3">
    @if (count($customer->orders) > 0)
      <ul>
        @foreach ($customer->orders as $order)
        <li><a href="{{ route('order.show', $order->id) }}" target="_blank">{{ $order->id }}</a></li>
        @endforeach
      </ul>
    @else
      There are no orders for this customer
    @endif
  </div>
</div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript">
      $('#completion-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

          jQuery(document).ready(function() {

              jQuery('.multi_brand').select2({
                  placeholder: 'Brand',
                  width: '100%'
              });


              jQuery('.multi_category').select2({
                  placeholder: 'Categories',
                  width: '100%'
              });


          });

          $('#change_status').on('change', function() {
            var token = "{{ csrf_token() }}";
            var status = $(this).val();
            var id = $(this).data('leadid');

            $.ajax({
              url: '/leads/' + id + '/changestatus',
              type: 'POST',
              data: {
                _token: token,
                status: status
              }
            }).done( function(response) {
              $('#change_status_message').fadeIn(400);
              setTimeout(function () {
                $('#change_status_message').fadeOut(400);
              }, 2000);
            }).fail(function(errObj) {
              alert("Could not change status");
            });
          });


    </script>

    @endsection
