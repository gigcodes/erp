@extends('layouts.app')

@section('title', 'Erp Leads')

@section("styles")
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Erp Leads <a class="btn btn-secondary editor_create" href="javascript:;">+</a></h2>

  </div>
  <?php /*
  <div class="col-lg-12 margin-tb">
    <form id="search" method="GET" class="form-inline">
        <input name="term" type="text" class="form-control"
               value="{{request()->get('term')}}"
               placeholder="Search" id="customer-search">

        <div class="form-group ml-3">
            <input placeholder="Shoe Size" type="text" name="shoe_size" value="{{request()->get('shoe_size')}}" class="form-control-sm form-control">
        </div>
        <div class="form-group ml-3">
            <input placeholder="Clothing Size" type="text" name="clothing_size" value="{{request()->get('clothing_size')}}" class="form-control-sm form-control">
        </div>
        <div class="form-group ml-3">
            <select class="form-control" name="shoe_size_group">
                <option value="">Select</option>
                <?php foreach ($shoe_size_group as $shoe_size => $customerCount) {
                    echo '<option value="'.$shoe_size.'" '.($shoe_size == request()->get('shoe_size_group') ? 'selected' : '').'>('.$shoe_size.' Size) '.$customerCount.' Customers</option>';
                } ?>
            </select>
        </div>
        <div class="form-group ml-3">
            <select class="form-control" name="clothing_size_group">
                <option value="">Select</option>
                <?php foreach ($clothing_size_group as $clothing_size => $customerCount) {
                    echo '<option value="'.$clothing_size.'" '.($shoe_size == request()->get('shoe_size_group') ? 'selected' : '').'>('.$clothing_size.' Size) '.$customerCount.' Customers</option>';
                } ?>
            </select>
        </div>
        <input type="hidden" name="lead_customer">
        <input type="hidden" name="lead_brand">
        <input type="hidden" name="lead_category">
        <input type="hidden" name="lead_color">
        <input type="hidden" name="lead_shoe_size">
        <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
    </form>
  </div>
  */?>
  <div class="col-md-12">
    <div class="table-responsive">
      <table cellspacing="0" role="grid" class="table table-striped table-bordered datatable mdl-data-table dataTable" `:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Status</th>
                <th>Customer</th>
                <th width="140px">Image</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Color</th>
                <th>Size</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th><input type="text" style="width: 138px;" class="field_search lead_customer" name="lead_customer" placeholder="Search Customer" /></th>
                <th></th>
                <th>
                  <select name="brand_id[]" class="lead_brand multi_brand" multiple="">
                    <option value="">Brand</option>
                    @foreach($brands as $brand_item)
                      <option value="{{$brand_item['id']}}">{{$brand_item['name']}}</option>
                    @endforeach
                  </select>
                </th>
                <th><input type="text" style="width: 138px;" class="field_search lead_category" name="lead_category" placeholder="Search Category" /></th>
                <th><input type="text" style="width: 138px;" class="field_search lead_color" name="lead_color" placeholder="Search Color" /></th>
                <th><input type="text" style="width: 138px;" class="field_search lead_shoe_size" name="lead_shoe_size" placeholder="Search Size" /></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <thead>
            <tr>
                <th colspan="8">
                  <label>
                    <input type="checkbox" class="all_customer_check"> Select All
                  </label> 
                  <a class="btn btn-secondary create_broadcast" href="javascript:;">Create Broadcast</a>
                  <a href="javascript:;" class="btn btn-image px-1 images_attach"><img src="/images/attach.png"></a>
                </h2>
              </th>
            </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<div id="erp-leads" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

      </div>
    </div>
  </div>
</div>

<div id="create_broadcast" class="modal fade" role="dialog">
  <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Message to Customers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form  id="send_message" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                      <strong> Selected Product :</strong>
                      <select name="selected_product[]" class="ddl-select-product form-control" multiple="multiple"></select>

                      <strong>Schedule Date:</strong>
                      <div class='input-group date' id='schedule-datetime'>
                        <input type='text' class="form-control" name="sending_time" id="sending_time_field" value="{{ date('Y-m-d H:i') }}" required />

                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                        <strong>Message</strong>
                        <textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send Message</button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@section('scripts')
  <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.multi_brand').select2();
      $(".all_customer_check").click(function(){
          $('.customer_message').prop('checked', this.checked);
      });

      $(".images_attach").click(function(e){
          e.preventDefault();
          var customers = [];
          $(".customer_message").each(function() {
              if ($(this).prop("checked") == true) {
                customers.push($(this).val());
              }
          });
          if (customers.length == 0) {
            alert('Please select costomer');
            return false;
          }
          url = "{{ route('attachImages', ['selected_customer', 'CUSTOMER_IDS', 1]) }}";
          url = url.replace("CUSTOMER_IDS", customers.toString());

          window.location.href = url;

      });

      $("#send_message").submit(function(e){
          e.preventDefault();
          var customers = [];
          $(".customer_message").each(function() {
              if ($(this).prop("checked") == true) {
                customers.push($(this).val());
              }
          });
          if (customers.length == 0) {
            alert('Please select costomer');
            return false;
          }

          if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
          }

          if ($("#send_message").find(".ddl-select-product").val() == "") {
            alert('Please select product');
            return false;
          }

          $.ajax({
            type: "POST",
            url: "{{ route('erp-leads-send-message') }}",
            data: {
              _token : "{{ csrf_token() }}",
              products : $("#send_message").find(".ddl-select-product").val(),
              sending_time : $("#send_message").find("#sending_time_field").val(),
              message : $("#send_message").find("#message_to_all_field").val(),
              customers : customers
            }
          }).done(function() {
            window.location.reload();
          }).fail(function(response) {
            $(thiss).text('No');

            alert('Could not say No!');
            console.log(response);
          });
      });
      jQuery('.ddl-select-product').select2({
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
          return product.text || product.name;
        },

      });
     
      var table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            ajax: {
              "url" : '{{ route('leads.erpLeadsResponse') }}',
              data: function ( d ) {
                d.lead_customer = $('.lead_customer').val();
                d.lead_brand = $('.lead_brand').val();
                d.lead_category = $('.lead_category').val();
                d.lead_color = $('.lead_color').val();
                d.lead_shoe_size = $('.lead_shoe_size').val();
              }
            },
            columns: [
              {
                data: 'id',
                render : function ( data, type, row ) {
                      // Combine the first and last names into a single table field
                      return '<input type="checkbox" name="customer_message[]" class="customer_message" value="'+row.customer_id+'"> ' + data;
                }
              },
              {data: 'status_name', name: 'status_name'},
              {
                  data: null,
                  render : function ( data, type, row ) {
                      return '<a href="/customer/' + data.customer_id + '" target="_blank">' + data.customer_name + '</a>';
                  }
              },
              {
                  data: null,
                  render : function ( data, type, row ) {
                      return '<img class="lazy img-responsive grid-image" alt="" src="' + data.media_url + '" style="" width="100%">';
                  }
              },
              {data: 'brand_name', name: 'brand_name'},
              {data: 'cat_title', name: 'cat_title'},
              {data: 'color', name: 'color'},
              {data: 'size', name: 'size'}
          ]
        });

        $( '.field_search' ).on( 'keyup change', function () {
            table.draw();
        });

        $( '.multi_brand' ).on( 'change', function () {
            table.draw();
        });
        
    });

    $(document).on('click', '.create_broadcast', function () {
      var customers = [];
      $(".customer_message").each(function() {
          if ($(this).prop("checked") == true) {
            customers.push($(this).val());
          }
      });
      if (customers.length == 0) {
        alert('Please select costomer');
        return false;
      }
       $("#create_broadcast").modal("show");
    });

    // start to search for customer

    var customerSearch = function() {
        $(".customer-search-box").select2({
          tags : true,
          ajax: {
              url: '/erp-leads/customer-search',
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
          placeholder: 'Search for Customer by id, Name, No',
          escapeMarkup: function (markup) { return markup; },
          minimumInputLength: 2,
          templateResult: formatCustomer,
          templateSelection: (customer) => customer.text || customer.name,

      });
    };

    $(document).on('click', '.editor_create', function () {
       var $this = $(this);
        $.ajax({
            type: "GET",
            url: "{{ route('leads.erpLeads.create') }}"
        }).done(function (data) {
           $("#erp-leads").find(".modal-body").html(data);
           productSelect();
           customerSearch();
           $("#erp-leads").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on('click', '.editor_remove', function () {
      var r = confirm("Are you sure you want to delete this lead?");
      if (r == true) {
        var $this = $(this);
          $.ajax({
              type: "GET",
              data : {
                id : $this.data("lead-id")
              },
              url: "{{ route('leads.erpLeads.delete') }}"
          }).done(function (data) {
             $("#erp-leads").find(".modal-body").html("");
             $("#erp-leads").modal("hide");
             location.reload(true);
          }).fail(function (response) {
              console.log(response);
          });
      }
    });

    $(document).on('click', '.editor_edit', function () {
       var $this = $(this);
        $.ajax({
            type: "GET",
            data : {
              id : $this.data("lead-id")
            },
            url: "{{ route('leads.erpLeads.edit') }}"
        }).done(function (data) {
           $("#erp-leads").find(".modal-body").html(data);
           productSelect();
           customerSearch();
           $("#erp-leads").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on('click', '.lead-button-submit-form', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $form  = $this.closest("form");
      $.ajax({
            type: "POST",
            data : $form.serialize(),
            url: "{{ route('leads.erpLeads.store') }}"
        }).done(function (data) {
           if(data.code == 1) {
               $("#erp-leads").find(".modal-body").html("");
               $("#erp-leads").modal("hide");
               location.reload(true);
           }else{
              alert(data.message);
           }
        }).fail(function (response) {
            console.log(response);
        });
    });

    var productSelect = function()
    {
       $("#select2-product").select2({
          tags : true,
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
          minimumInputLength: 2,
          templateResult: formatProduct,
          templateSelection: (product) => product.text || product.name,

      });
    };

    function formatProduct (product) {
        if (product.loading) {
            return product.sku;
        }

        if(product.sku) {
            return "<p> <b>Id:</b> " +product.id  + (product.name ? " <b>Name:</b> "+product.name : "" ) +  " <b>Sku:</b> "+product.sku+" </p>";
        }

    }

    function formatCustomer (customer) {
        if (customer.loading) {
            return customer.name;
        }

        if(customer.name) {
            return "<p> <b>Id:</b> " +customer.id  + (customer.name ? " <b>Name:</b> "+customer.name : "" ) +  (customer.phone ? " <b>Phone:</b> "+customer.phone : "" ) + "</p>";
        }

    }



  </script>
@endsection
