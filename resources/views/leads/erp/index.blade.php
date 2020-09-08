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
<?php $base_url = URL::to('/');?>
  <div class="pull-left cls_filter_box">
                <!-- <form class="form-inline" action="{{ route('vendors.index') }}" method="POST"> -->
                <form class="form-inline" method="POST">
                @csrf
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Status</label>
                        <select style="width:100px; font-size: 12px; border-radius: 2px;" name="status_id[]" class="lead_status multi_lead_status" multiple="">
                          <option value="">Status</option>
                          @foreach($erpLeadStatus as $status)
                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Customer</label>
                        <input placeholder="Customer" type="text" name="customer" value="" class="form-control-sm cls_commu_his form-control input-size">
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <label for="with_archived">Brand</label>
                    
                       <select name="brand_id[]" class="lead_brand multi_brand" multiple="" style="width: 100px; border-radius: 2px;">
                    <option value="">Brand</option>
                    @foreach($brands as $brand_item)
                      <option value="{{$brand_item['id']}}">{{$brand_item['name']}}</option>
                    @endforeach
                  </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <label for="with_archived">Brand Segment</label>
                       <input placeholder="Brand Segment" type="text" name="brand_segment" value="" class="form-control-sm cls_commu_his form-control input-size">
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Category</label>
                        <input placeholder="Category" type="text" name="category" value="" class="form-control-sm cls_commu_his form-control input-size">
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_updated_by">Color</label>
                        <input placeholder="Color" type="text" name="color" value="" class="form-control-sm cls_commu_his form-control input-size">
                    </div>
                    <div class="form-group ml-3 cls_filter_checkbox">
                    <label for="with_archived">Size</label>
                       <input placeholder="Size" type="text" name="size" value="" class="form-control-sm cls_commu_his form-control input-size">
                    </div>
                    <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image" id="btnFileterErpLeads"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                </form>
            </div>

            <div class="col-lg-12 margin-tb">
            <div class="pull-right mt-3">
                <!-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#conferenceModal">Conference Call</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createVendorCategorytModal">Create Category</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</button> -->
                <label style="margin-right: 13px;">
                    <input type="checkbox" class="all_customer_check"> Select This Page
                  </label>
                  <label style="margin-right: 13px;">
                    <input type="checkbox" class="all_page_check"> Select All Page
                  </label>
                <a class="btn btn-secondary create_broadcast" href="javascript:;">Create Broadcast</a>
                <a href="javascript:;" class="btn btn-image px-1 images_attach"><img src="/images/attach.png"></a>
            </div>
        </div> 
    <div class="table-responsive">
      <table style="font-size: 12px;" cellspacing="0" role="grid" class="table table-striped table-bordered datatable mdl-data-table dataTable" `:100%">
        <thead>
            <!-- <tr style="background: #f9f9f9;"> -->
            <tr class="tbl-head">
                <th style="text-align: center;" width="2%">#</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Customer</th>
                <th width="140px" style="text-align: center;">Image</th>
                <th style="text-align: center;">Brand</th>
                <th style="text-align: center;">Brand Segment</th>
                <th style="text-align: center;">Category</th>
                <th style="text-align: center;">Color</th>
                <th style="text-align: center;">Size</th>
            </tr>
            <!-- <tr>
                <th></th>
                <th>
                  <select style="width:100px; font-size: 12px; border-radius: 2px;" name="status_id[]" class="lead_status multi_lead_status" multiple="">
                    <option value="">Status</option>
                    @foreach($erpLeadStatus as $status)
                      <option value="{{$status['id']}}">{{$status['name']}}</option>
                    @endforeach
                  </select>
                </th>
                <th><input type="text" style="width: 100px; width: 100px;
    border-radius: 4px;
    border: 1px solid #aaa;
    line-height: 29px;" class="field_search lead_customer" name="lead_customer" placeholder="" /></th>
                <th style="width: 100px; border-radius: 2px;" ></th>
                <th>
                  <select name="brand_id[]" class="lead_brand multi_brand" multiple="" style="width: 100px; border-radius: 2px;">
                    <option value="">Brand</option>
                    @foreach($brands as $brand_item)
                      <option value="{{$brand_item['id']}}">{{$brand_item['name']}}</option>
                    @endforeach
                  </select>
                </th>
                <th><input style="width: 100px; width: 100px;
    border-radius: 4px;
    border: 1px solid #aaa;
    line-height: 29px;" type="text" class="field_search brand_segment" name="brand_segment"/></th>
                <th><input style="width: 100px; width: 100px;
    border-radius: 4px;
    border: 1px solid #aaa;
    line-height: 29px;" type="text" class="field_search lead_category" name="lead_category" /></th>
                <th><input style="width: 100px; width: 100px;
    border-radius: 4px;
    border: 1px solid #aaa;
    line-height: 29px;" type="text" class="field_search lead_color" name="lead_color" /></th>
                <th><input style="width: 100px; width: 100px;
    border-radius: 4px;
    border: 1px solid #aaa;
    line-height: 29px;" type="text" class="field_search lead_shoe_size" name="lead_shoe_size"/></th>
            </tr> -->
        </thead>
        <tbody>
        </tbody>
        <!-- <thead> -->
            <!-- <tr>
                <th colspan="8">
                  <label>
                    <input type="checkbox" class="all_customer_check"> Select This Page
                  </label>
                  <label>
                    <input type="checkbox" class="all_page_check"> Select All Page
                  </label> 
                  <a class="btn btn-secondary create_broadcast" href="javascript:;">Create Broadcast</a>
                  <a href="javascript:;" class="btn btn-image px-1 images_attach"><img src="/images/attach.png"></a>
                </h2>
              </th>
            </tr> -->
        <!-- </thead> -->
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
            <form enctype="multipart/form-data" id="send_message" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                      <strong> Selected Product :</strong>
                      <select name="selected_product[]" class="ddl-select-product form-control" multiple="multiple"></select>
                      <strong> Attach Image :</strong>
                      <div class='input-group date' id='schedule-datetime'>
                        <input type='file' class="form-control" name="image" id="image" value=""/>
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-file"></span>
                        </span>
                      </div>

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
    var customers = [];
    var allLeadCustomersId = [];
    $(document).ready(function() {
      $('.multi_brand').select2();
      $('.multi_lead_status').select2();
      
      $(".all_customer_check").click(function(){
          $('.customer_message').prop('checked', this.checked);
          $(".customer_message").each(function() {
              if ($(this).prop("checked") == true) {
                if (customers.indexOf($(this).val()) === -1) {
                  customers.push($(this).val());
                }
              } else {
                var tmpCustomers = [];
                for (var k in customers) {
                  if (customers[k] != $(this).val()) {
                    tmpCustomers.push(customers[k]);
                  }
                }
                customers = tmpCustomers;
              } 
          });
      });

      $('#btnFileterErpLeads').click(function(e){
        e.preventDefault();
        alert('hjsd')
        var token = "{{ csrf_token() }}";
        var url = "{{ route('erp-leads.filterErpLeads') }}";
   
        var form_data = new FormData();
        form_data.append('_token', token);

        var status = $("select[name='status_id[]']").val();//alert(status)
        var brand = $("select[name='brand_id[]'").val();
        var customer = $("input[name='customer']").val()
        var brandSegment = $("input[name='brand_segment']").val()
        var category = $("input[name='category']").val()
        var color = $("input[name='color']").val()
        var size = $("input[name='size']").val()
        // alert(customer)
        form_data.append('status_id', status)
        form_data.append('brand', brand)
        form_data.append('customer', customer)
        form_data.append('brand_segment', brandSegment)
        form_data.append('category', category)
        form_data.append('color', color)
        form_data.append('size', size)

        $.ajax({
            type: 'POST',
            url: url,
            processData: false,
            contentType: false,
            // enctype: 'multipart/form-data',
            data: form_data,
            dataType :'json',
            success: function(response) {
                console.log('saved');
                // console.log(response);
                // console.log(response.order.id);
                // $('#c_order_id').val(response.order.id);
                // openConformattionMailBox();
               // jQuery("#myModalOrderConfirmation").modal('show');
            }
          });

      })

      $(".all_page_check").click(function(){
          $('.customer_message').prop('checked', this.checked);
          customers = [];
          if (this.checked) {
              for (var k in allLeadCustomersId) {
                if (customers.indexOf(allLeadCustomersId[k]) === -1) {
                  customers.push(allLeadCustomersId[k]);
                }
              }
          }
      });

      $(document).on('change', '.customer_message', function () {
        if ($(this).prop("checked") == true) {
          if (customers.indexOf($(this).val()) === -1) {
            customers.push($(this).val());
          }
        } else {
          var tmpCustomers = [];
          for (var k in customers) {
            if (customers[k] != $(this).val()) {
              tmpCustomers.push(customers[k]);
            }
          }
          customers = tmpCustomers;
        } 
      });

      $(".images_attach").click(function(e){
          e.preventDefault();
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
          var formData = new FormData($(this)[0]);
          
          if (customers.length == 0) {
            alert('Please select costomer');
            return false;
          }

          if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
          }

          /*if ($("#send_message").find(".ddl-select-product").val() == "") {
            alert('Please select product');
            return false;
          }*/

          for (var i in customers) {
            formData.append("customers[]", customers[i]);
          }

          $.ajax({
            type: "POST",
            url: "{{ route('erp-leads-send-message') }}",
            data: formData,
            contentType : false,
            processData:false
          }).done(function() {
            // window.location.reload();
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
                d.brand_segment = $('.brand_segment').val();
                d.lead_status = $('.lead_status').val();
                $('.all_customer_check').prop('checked', false);
              },
              dataSrc : function ( response ) {
                allLeadCustomersId = response.allLeadCustomersId;
                return response.data;
              }
            },
            columns: [
              {
                data: 'id',
                render : function ( data, type, row ) {
                      // Combine the first and last names into a single table field
                      return '<div class="checkbox"><label class="checkbox-inline"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'">'+data+'aa</label></div>';
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
                      return data.media_url ? '<img class="lazy" alt="" src="' + data.media_url + '" style="width:50px;">' : '';
                  }
              },
              {data: 'brand_name', name: 'brand_name'},
              {data: 'brand_segment', name: 'brand_segment'},
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
           customerSearch();
           $('.multi_brand_select').select2({width: '100%'});
           $('.brand_segment_select').select2({width: '100%'});
           
           $(".multi_brand_select").change(function() {
                var brand_segment = [];
                $(this).find(':selected').each(function() {
                    if ($(this).data('brand-segment') && brand_segment.indexOf($(this).data('brand-segment')) == '-1') {
                      brand_segment.push($(this).data('brand-segment'));
                    }
                })
                $(".brand_segment_select").val(brand_segment).trigger('change');
            });

           $('#category_id').select2({width: '100%'});
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
           customerSearch();
           $("#erp-leads").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on('click', '.lead-button-submit-form', function (e) {
      e.preventDefault();
      var $this = $(this);
      var formData = new FormData(document.getElementById("lead_create"));
      $.ajax({
            type: "POST",
            data : formData,
            url: "{{ route('leads.erpLeads.store') }}",
            contentType: false,
            processData: false
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
