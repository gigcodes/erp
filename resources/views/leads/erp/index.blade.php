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
                <form class="form-inline" action="{{ route('erp-leads.erpLeads') }}" method="GET">
                
                @csrf
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Status</label>
                        <!-- <select style="width:100px; font-size: 12px; border-radius: 2px;" name="status_id[]" class="lead_status multi_lead_status" multiple="">

                         
                          <option value="">Status</option>
                          @foreach($erpLeadStatus as $status)
                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                          @endforeach
                        </select> -->
                        <select class="form-control lead_status multi_lead_status" name="status_id[]" multiple="" style="width: 161px; border-radius: 2px;">
                            <option value="">Select Category</option>
                            <option value="">Status</option>
                          @foreach($erpLeadStatus as $status)
                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                          @endforeach
                        </select>
                    </div>

                    
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Customer</label>
                        <!-- <input placeholder="Customer" type="text" name="customer" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                        <input type="text" class="form-control-sm cls_commu_his form-control field_search lead_customer input-size" name="lead_customer" placeholder="Customer" />

                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <label for="with_archived">Brand</label>
                    
                       <!--  <select name="brand_id[]" class="lead_brand multi_brand" multiple="" style="width: 100px; border-radius: 2px;">
                          <option value="">Brand</option>
                          @foreach($brands as $brand_item)
                            <option value="{{$brand_item['id']}}">{{$brand_item['name']}}</option>
                          @endforeach
                        </select> -->
                         <select class="form-control lead_brand multi_lead_status input-size" name="brand_id[]" multiple="" style="width: 161px; border-radius: 2px;">
                            <option value="">Brand</option>
                          @foreach($brands as $brand_item)
                            <option value="{{$brand_item['id']}}">{{$brand_item['name']}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <label for="with_archived">Brand Segment</label>
                       <!-- <input placeholder="Brand Segment" type="text" name="brand_segment" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                       <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search brand_segment" name="brand_segment" placeholder="Brand Segment"/>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Category</label>
                        <!-- <input placeholder="Category" type="text" name="category" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                        <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search lead_category" name="lead_category" placeholder="Category"/>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_updated_by">Color</label>
                        <!-- <input placeholder="Color" type="text" name="color" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                        <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search lead_color" name="lead_color" placeholder="Color"/>

                    </div>
                    <div class="form-group ml-3 cls_filter_checkbox">
                    <label for="with_archived">Size</label>
                       <!-- <input placeholder="Size" type="text" name="size" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                       <input type="text" class="field_search lead_shoe_size form-control-sm cls_commu_his form-control input-size" name="lead_shoe_size" placeholder="Size"/>
                    </div>
                    <!-- <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image" id="btnFileterErpLeads"><img src="<?php //echo $base_url;?>/images/filter.png"/></button> -->
                    <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image" id="btnFileterErpLeads"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                </form>
                
            </div>

            <div class="col-lg-12 margin-tb">
            <div class="pull-right mt-3" style="margin-bottom: 12px ">
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
        <div></div>
        <br>
        <div class="infinite-scroll">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="vendor-table">
            <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="5%">Status</th>
                <th width="10%">Customer</th>
                <th width="7%">Image</th>
                <th width="13%">Brand</th>
                <th width="10%">Brand Segment</th>
                <th width="10%">Category</th> 
               
                <th width="5%">Color</th>
                <th width="2%">Size</th>
            </tr>
            </thead>

            <tbody id="vendor-body">

              @foreach ($sourceData as $source)
                <tr>
                  <!-- <td>{{$source['id']}}</td> -->
                  <td class="tblcell">
                    
                    <div class="checkbox"><label class="checkbox-inline"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'">{{$source['id']}}aa</label></div>
                  </td>
                  <td class="tblcell">{{$source['status_name']}}</td>
                  <td class="tblcell"><a href="/customer/' + data.customer_id + '" target="_blank">{{$source['customer_name']}}</a></td>

                  <td class="tblcell">@if($source['media_url']) <img class="lazy" alt="" src="' + data.media_url + '" style="width:50px;"> @else {{''}} @endif
</td>
                  <td class="tblcell">{{$source['brand_name']}}</td>
                  <td class="tblcell">{{$source['brand_segment']}}</td>
                  <td class="tblcell">{{$source['cat_title']}}</td>
                  <td class="tblcell">{{$source['color']}}</td>
                  <td class="tblcell">{{$source['size']}}</td>
                </tr>
              @endforeach

            </tbody>
        </table>
    </div>
    {{ $sourceData->appends(Request::except('page'))->links() }}

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
  <script src="https://cdn.datatables.net/scroller/2.0.2/js/dataTables.scroller.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
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

      $('.infinite-scroll').jscroll({

          autoTrigger: true,
          // debug: true,
          loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
          padding: 20,
          nextSelector: '.pagination li.active + li a',
          contentSelector: 'div.infinite-scroll',
          callback: function () {
              $('ul.pagination').first().remove();
              $('ul.pagination').hide();
          }
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
     
      // var table = $('.dataTable').DataTable({
      //       processing: true,
      //       serverSide: true,
      //       searching: false,
      //       ordering: false,
      //       deferRender:    true,
      //   scrollY:        200,
      //   scrollCollapse: true,
      //   scroller:       true,
      //       // bScrollInfinite: true,
      //       // bScrollCollapse: true,
      //       // sScrollY: "200px",
      //       ajax: {
      //         "url" : '{{ route('leads.erpLeadsResponse') }}',
      //         data: function ( d ) {
      //           console.log(d, "opopop");
      //           d.lead_customer = $('.lead_customer').val();
      //           d.lead_brand = $('.lead_brand').val();
      //           d.lead_category = $('.lead_category').val();
      //           d.lead_color = $('.lead_color').val();
      //           d.lead_shoe_size = $('.lead_shoe_size').val();
      //           d.brand_segment = $('.brand_segment').val();
      //           d.lead_status = $('.lead_status').val();
      //           $('.all_customer_check').prop('checked', false);
      //         },
      //         dataSrc : function ( response ) {
      //           allLeadCustomersId = response.allLeadCustomersId;
      //           return response.data;
      //         }
      //       },
      //       columns: [
      //         {
      //           data: 'id',
      //           render : function ( data, type, row ) {
      //                 // Combine the first and last names into a single table field
      //                 return '<div class="checkbox"><label class="checkbox-inline"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'">'+data+'aa</label></div>';
      //          }       
      //         },
      //         {data: 'status_name', name: 'status_name'},
      //         {
      //             data: null,
      //             render : function ( data, type, row ) {
      //                 return '<a href="/customer/' + data.customer_id + '" target="_blank">' + data.customer_name + '</a>';
      //             }
      //         },
      //         {
      //             data: null,
      //             render : function ( data, type, row ) {
      //                 return data.media_url ? '<img class="lazy" alt="" src="' + data.media_url + '" style="width:50px;">' : '';
      //             }
      //         },
      //         {data: 'brand_name', name: 'brand_name'},
      //         {data: 'brand_segment', name: 'brand_segment'},
      //         {data: 'cat_title', name: 'cat_title'},
      //         {data: 'color', name: 'color'},
      //         {data: 'size', name: 'size'}
      //     ]
      //   });

        // $( '.field_search' ).on( 'keyup change', function () {
        //     table.draw();
        // });btnFileterErpLeads
        // $( '#btnFileterErpLeads' ).on( 'click', function () {
        //     table.draw();
        // });

        // $( '.multi_brand' ).on( 'change', function () {
        //     table.draw();
        // });
        
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
