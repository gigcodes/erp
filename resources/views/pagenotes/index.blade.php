@extends('layouts.app')

@section('title', 'Page Notes')

@section("styles")
<!-- START - Purpose : Add CSS - DEVTASK-4289 -->
<style type="text/css">
  table tr td{
    overflow-wrap: break-word;
  }
    .page-note{
        font-size: 14px;
    }
    .flex{
        display: flex;
    }

  #loading-image {
      position: fixed;
      top: 50%;
      left: 50%;
      margin: -50px 0px 0px -50px;
      z-index: 60;
  }
  
  .select2.select2-container {
      width: 100% !important;
  }
  
  .select2.select2-container .select2-selection {
      border: 1px solid #ccc;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
      height: 34px;
      margin-bottom: 15px;
      outline: none !important;
      transition: all .15s ease-in-out;
  }
  
  .select2.select2-container .select2-selection .select2-selection__rendered {
      color: #333;
      line-height: 32px;
      padding-right: 33px;
  }
  
  .select2.select2-container .select2-selection .select2-selection__arrow {
      background: #f8f8f8;
      border-left: 1px solid #ccc;
      -webkit-border-radius: 0 3px 3px 0;
      -moz-border-radius: 0 3px 3px 0;
      border-radius: 0 3px 3px 0;
      height: 32px;
      width: 33px;
  }

  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
      background: #f8f8f8;
  }
  
  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
      -webkit-border-radius: 0 3px 0 0;
      -moz-border-radius: 0 3px 0 0;
      border-radius: 0 3px 0 0;
  }

  .select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
      border: 1px solid #34495e;
  }

  .select2.select2-container .select2-selection--multiple {
      height: auto;
      min-height: 34px;
  }

  .select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
      margin-top: 0;
      height: 32px;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__rendered {
      display: block;
      padding: 0 4px;
      line-height: 29px;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__choice {
      background-color: #f8f8f8;
      border: 1px solid #ccc;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      border-radius: 3px;
      margin: 4px 4px 0 0;
      padding: 0 6px 0 22px;
      height: 24px;
      line-height: 24px;
      font-size: 12px;
      position: relative;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
      position: absolute;
      top: 0;
      left: 0;
      height: 22px;
      width: 22px;
      margin: 0;
      text-align: center;
      font-weight: bold;
      font-size: 16px;
  }

  .select2-container .select2-dropdown {
      background: transparent;
      border: none;
      margin-top: -5px;
  }

  .select2-container .select2-dropdown .select2-search {
      padding: 0;
  }

  .select2-container .select2-dropdown .select2-search input {
      outline: none !important;
      border: 1px solid #34495e !important;
      border-bottom: none !important;
      padding: 4px 6px !important;
  }

  .select2-container .select2-dropdown .select2-results {
      padding: 0;
  }

  .select2-container .select2-dropdown .select2-results ul {
      background: #fff;
      border: 1px solid #34495e;
  }

  .select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
      background-color: #3498db;
  }
</style>
<!-- END - DEVTASK-4289 -->
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">

@section('content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
</div>
<div class="row">
  <div class="col-lg-12 margin-tb mb-5">
      <h2 class="page-heading">Page Notes</h2>
      <!-- START - Purpose : Get Page Note - DEVTASK-4289 -->
      <form method="get" action="{{ route('pageNotes.viewList') }}">
          <div class="pull-left">
{{--              <div class="flex">--}}
                  <div class="row">
{{--                      <div class="col-md-4">--}}
{{--                          <input name="search" type="text" class="form-control"--}}
{{--                                 value="{{ isset($term) ? $term : '' }}"--}}
{{--                                 placeholder="Search Program" id="search">--}}
{{--                      </div>--}}
                      <div class="custom-select2 col-md-5">
                          <select class="js-example-basic-multiple js-states"
                                  id="note_title"  name="note_title[]" multiple="multiple">
                              @foreach($title as $val)
                                <option value="{{$val}}" {{ in_array($val, (request('note_title') ?? [])) ? 'selected' : ''}}>{{$val}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-5 custom-select2">
                          <select name="note[]" id="note" class="form-control w-50 js-example-basic-note js-states" multiple="multiple">
                              <option value="">Select Note</option>
                              @foreach($note as $val)
                                  <option value="{{$val}}" {{ in_array($val, (request('note') ?? [])) ? 'selected' : ''}}>{{strip_tags($val)}}</option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-1">
                          <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                              <img src="/images/search.png" style="cursor: default;">
                          </button>
                      </div><div class="col-md-1">
                          <a href="{{route('pageNotes.viewList')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>
                      </div>
                  </div>
{{--                  <div class="col">--}}
{{--                      <?php echo Form::text("search", request("search", null), ["class" => "form-control", "placeholder" => "Enter input here.."]); ?>--}}
{{--                  </div>--}}
{{--              </div>--}}
          </div>
          <div class="pull-right">
              {{--            <a class="btn btn-secondary" href="{{url('/store-website-analytics/create')}}">+</a>--}}
              <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                      data-target="#pageNotesCategoriesModal"
                      title="Add new page notes category"><i class="fa fa-plus"></i>
              </button>
          </div>
      </form>
      <!-- END - DEVTASK-4289 -->
  </div>


 
  <div class="col-md-12">
    <div class="pagenote-scroll"><!-- Purpose : Add Div for Scrolling - DEVTASK-4289 -->
      <div class="table-responsive">
          @include('partials.flash_messages')
        <table cellspacing="0" role="grid" id="pagenote-table" class="page-notes table table-bordered datatable mdl-data-table dataTable page-notes" style="width:100%">
          <thead>
              <tr>
                  <th width="5%">#</th>
                  <th width="8%">Category</th>
                  <th width="10%">Title</th>
                  <th width="50%">Note</th>
                  <th width="7%">User Name</th>
                  <th width="10%">Created at</th>
                  <th width="10%">Action</th>
              </tr>
          </thead>
          <tbody>
          <!-- START - Purpose : Get Data - DEVTASK-4289 -->
          @foreach($records as $key => $value)
              <tr>
                  <td>{{$value->id}}</td>
                  <td>{{$value->category_name}}</td>
                  <td>{{$value->title}}</td>
                  @if (strlen($value->note) > 200)
                      <td style="word-break: break-word;" data-log_message="{!!$value->note !!}" class="page-note-popup">{{ substr($value->note,0,200) }}...</td>
                  @else
                      <td style="word-break: break-word;">{!!$value->note !!}</td>
                  @endif
{{--                      <p class="m-0">{!!$value->note !!}</p>--}}
                  <td>{{$value->name}}</td>
                  <td>{{ date('m-d  H:i', strtotime($value->created_at)) }}</td>
                  <td><a href="javascript:;" data-note-id = "{{$value->id}}" class="editor_edit btn-xs btn btn-image p-2">
                    <img src="/images/edit.png"></a>
                    <a data-note-id = "{{$value->id}}" href="javascript:;" class="editor_remove btn-xs btn btn-image p-2">
                    <img src="/images/delete.png"></a></td>
              </tr>
          @endforeach
          <!-- END - DEVTASK-4289 -->
          </tbody>
        </table>
      </div> 
      {{ $records->appends(Request::except('page'))->links() }}<!-- Purpose : Set Pagination - DEVTASK-4289 -->
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


<!--Log Messages Modal -->
<div id="pageNotesModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Note</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(".note_title").select2();
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            placeholder: "Search Title",
            closeOnSelect: false
        }); 
        $('.js-example-basic-note').select2({
            placeholder: "Search Note",
            closeOnSelect: false
        });
    });
</script>
@endsection

@include('page-notes-categories.add-page-notes-categories-model')
@include('partials.modals.quick-notes')
@section('scripts')
  <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script><!-- Purpose : Add Js for scroll - DEVTASK-4289 -->
  <script type="text/javascript">
      $(document).on('click','.page-note-popup',function(){
          $('#pageNotesModal').modal('show');
          $('#pageNotesModal p').text($(this).data('log_message'));
      })
      
    $(document).ready(function() {
      //START - Purpose : Comment Code - DEVTASK-4289

      // $('.datatable').DataTable({
      //       processing: true,
      //       serverSide: true,
      //       order: [[ 0, "desc" ]],
      //       ajax: '{{ route('pageNotesRecords') }}',
      //       columns: [
      //         {data: 'id', name: 'id'},
      //         {data: 'category_name', name: 'category_name'},
      //         {
      //             data: 'note',
      //             render : function ( data, type, row ) {
                      
      //                var data_note =  row.note.replaceAll("&lt;", "<").replaceAll("&gt;",'>');
      //                 return data_note;
      //             },
      //         },
      //         {data: 'name', name: 'name'},
      //         {data: 'created_at', name: 'created_at'},
      //         {
      //             data: null,
      //             render : function ( data, type, row ) {
      //                 // Combine the first and last names into a single table field
      //                 return '<a href="javascript:;" data-note-id = "'+data.id+'" class="editor_edit btn btn-image"><img src="/images/edit.png"></a><a data-note-id = "'+data.id+'" href="javascript:;" class="editor_remove btn btn-image"><img src="/images/delete.png"></a>';
      //             },
      //             className: "center"
      //         }
      //     ]
      //   });

      //END - DEVTASK-4289
  });

  //START - Purpose : Add editor , scroll - DEVTASK-4289
  $('#erp-notes').on('show.bs.modal', function() {
    $('#note').richText();
  });

  $('.pagenote-scroll').jscroll({

    autoTrigger: true,
    debug: true,
    loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    padding: 20,
    nextSelector: '.pagination li.active + li a',
    contentSelector: 'div.pagenote-scroll',
    callback: function () {
        $('ul.pagination').first().remove();
        $('ul.pagination').hide();
    }
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
