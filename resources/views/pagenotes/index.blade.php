@extends('layouts.app')

@section('title', 'Page Notes')

@section("styles")
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Page Notes</h2>
  </div>
  <div class="col-md-12">
    <div class="table-responsive">
      <table cellspacing="0" role="grid" class="table table-striped table-bordered datatable mdl-data-table dataTable" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Note</th>
                <th>User Name</th>
                <th>Created at</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div> 
  </div>
</div> 
<div id="erp-notes" class="modal fade" role="dialog">
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
            order: [[ 0, "desc" ]],
            ajax: '{{ route('pageNotesRecords') }}',
            columns: [
              {data: 'id', name: 'id'},
              {data: 'category_name', name: 'category_name'},
              {
                  data: 'note',
                  render : function ( data, type, row ) {
                      
                     var data_note =  row.note.replaceAll("&lt;", "<").replaceAll("&gt;",'>');
                      return data_note;
                  },
              },
              {data: 'name', name: 'name'},
              {data: 'created_at', name: 'created_at'},
              {
                  data: null,
                  render : function ( data, type, row ) {
                      // Combine the first and last names into a single table field
                      return '<a href="javascript:;" data-note-id = "'+data.id+'" class="editor_edit btn btn-image"><img src="/images/edit.png"></a><a data-note-id = "'+data.id+'" href="javascript:;" class="editor_remove btn btn-image"><img src="/images/delete.png"></a>';
                  },
                  className: "center"
              }
          ]
        });
  });

  //START - Purpose : Add editor - DEVTASK-4289
  $('#erp-notes').on('show.bs.modal', function() {
    $('#note').richText();
  });
  //END - DEVTASK-4289

  $(document).on('click', '.editor_edit', function () {

       var $this = $(this);
        $.ajax({
            type: "GET",
            data : {
              id : $this.data("note-id")
            },
            url: "{{ route('editPageNote') }}"
        }).done(function (data) {
           $("#erp-notes").find(".modal-body").html(data);
           
           $("#erp-notes").modal("show");
        }).fail(function (response) {
            console.log(response);
        });
    });

    $(document).on('click', '.update-user-notes', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $form  = $this.closest("form");
      $.ajax({
            type: "POST",
            data : $form.serialize(),
            url: "{{ route('updatePageNote') }}"
        }).done(function (data) {
           if(data.code == 1) {
               $("#erp-notes").find(".modal-body").html("");
               $("#erp-notes").modal("hide");
               location.reload(true);
           }else{
              alert(data.message);
           } 
        }).fail(function (response) {
            console.log(response);
        });
    });
     $(document).on('click', '.editor_remove', function () {
      var r = confirm("Are you sure you want to delete this notes?");
      if (r == true) {
        var $this = $(this);
          $.ajax({
              type: "GET",
              data : {
                id : $this.data("note-id")
              },
              url: "{{ route('deletePageNote') }}"
          }).done(function (data) {
             $("#erp-notes").find(".modal-body").html("");
             $("#erp-notes").modal("hide");
             location.reload(true);
          }).fail(function (response) {
              console.log(response);
          });
      }
    });
  </script>
@endsection
