@extends('layouts.app')

@section('title', 'Erp Leads')

@section("styles")
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Erp Leads <a class="btn btn-secondary editor_create" href="javascript:;">+</a></h2>
      
  </div>
  <div class="col-md-12">
    <div class="table-responsive">
      <table cellspacing="0" role="grid" class="table table-striped table-bordered datatable mdl-data-table dataTable" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Status</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Color</th>
                <th>Size</th>
                <th>Min Price</th>
                <th>Max Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
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

@endsection

@section('scripts')
  <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('leads.erpLeadsResponse') }}',
            columns: [
              {data: 'id', name: 'id'},
              {data: 'status_name', name: 'status_name'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'product_name', name: 'product_name'},
              {data: 'brand_name', name: 'brand_name'},
              {data: 'cat_title', name: 'cat_title'},
              {data: 'color', name: 'color'},
              {data: 'size', name: 'size'},
              {data: 'min_price', name: 'min_price'},
              {data: 'max_price', name: 'max_price'},
              {
                  data: null,
                  render : function ( data, type, row ) {
                      // Combine the first and last names into a single table field
                      return '<a href="javascript:;" data-lead-id = "'+data.id+'" class="editor_edit btn btn-image"><img src="/images/edit.png"></a><a data-lead-id = "'+data.id+'" href="javascript:;" class="editor_remove btn btn-image"><img src="/images/delete.png"></a>';
                  },
                  className: "center"
              }
          ]
        });
  });

    $(document).on('click', '.editor_create', function () {
       var $this = $(this);
        $.ajax({
            type: "GET",
            url: "{{ route('leads.erpLeads.create') }}"
        }).done(function (data) {
           $("#erp-leads").find(".modal-body").html(data);
           productSelect();
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
           $("#erp-leads").find(".modal-body").html("");
           $("#erp-leads").modal("hide");
           location.reload(true);
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
          minimumInputLength: 5,
          templateResult: formatProduct,
          templateSelection: (product) => product.name || product.sku,

      });

       $("#select2-product").trigger({
            type: 'select2:select',
            params: {}
        });
    };

    function formatProduct (product) {
        if (product.loading) {
            return product.sku;
        }

        return "<p> <b>Id:</b> " +product.id  + (product.name ? " <b>Name:</b> "+product.name : "" ) +  " <b>Sku:</b> "+product.sku+" </p>";
    }

    

  </script>
@endsection
