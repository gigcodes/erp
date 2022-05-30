@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Postman Request</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class="pull-left">
      <form class="form-inline" action="/postman/search" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <select name="folder_name" class="form-control" id="folder_name">
                <option value="">--select folder--</option>
                <?php 
                  $ops = 'id';
                  foreach($folders as $folder){
                      echo '<option value="'.$folder->id.'">'.$folder->name.'</option>';
                  }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="">
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="Request Type" class="form-control" name="request_type" value="">
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
          <a href="/postman" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
        </div>
      </form>
    </div>
    <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#addPostman">Add Postman request</button>
    <a href="/postman/folder" class="btn custom-button float-right mr-3">Add Folder</a>

	</br> 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Folder Name</th>
            <th>Request Name</th>
            <th>request type</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($postmans as $key => $postman)
            <tr>
            <td>{{$postman->id}}</td>
            <td>{{$postman->name}}</td>
            <td>{{$postman->request_name}}</td>
            <td>{{$postman->request_type}}</td>
            <td>
              <a class="btn btn-image edit-postman-btn" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
              <a class="btn delete-postman-btn"  data-id="{{ $postman->id }}" href="#"><img  data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
              <a class="btn postman-history-btn"  data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
              
            </td>
            </tr>
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $postmans->appends(Request::except('page'))->links() !!}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>
@endsection

<div id="postmanHistory" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Postman History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Updated by</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayPostmanHistory">
                </tbody>
              </table>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="addPostman" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="add-mail-content">
          
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><span id="titleUpdate">Add</span> Postman Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="postmanform" method="post">
                  @csrf
                  <div class="form-row">
                    <input type="hidden" id="post_id" name="id" value=""/>
                    <div class="form-group col-md-12">
                      <label for="title">Folder Name</label>
                      <select name="folder_name" class="form-control" id="folder_name">
                        <?php 
                          $ops = 'id';
                          foreach($folders as $folder){
                              echo '<option value="'.$folder->id.'">'.$folder->name.'</option>';
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="request_name">Request Name</label>
                      <input type="text" name="request_name" value="" class="form-control" id="request_name" placeholder="Enter request name">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="request_types">Request Type</label>
                      <input type="text" name="request_types" value="" class="form-control" id="request_types" placeholder="Enter request type">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="request_url">Request Url</label>
                      <input type="text" name="request_url" value="" class="form-control" id="request_url" placeholder="Enter request url">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="params">Params</label>
                      <textarea name="params" value="" class="form-control" id="params" placeholder="Enter params ex. filedName1: value1, filedName2: value2"></textarea>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="authorization_type">Authorization type</label>
                      <input type="text" name="authorization_type" value="" class="form-control" id="authorization_type" placeholder="Enter authorization type">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="authorization_token">Authorization token</label>
                      <input type="text" name="authorization_token" value="" class="form-control" id="authorization_token" placeholder="Enter authorization token">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="request_headers">headers</label>
                      <textarea name="request_headers" value="" class="form-control" id="request_headers" placeholder="Enter headers ex. filedName1: value1, filedName2: value2"></textarea>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="body_type">Body type</label>
                      <input type="text" name="body_type" value="" class="form-control" id="body_type" placeholder="Enter body type">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="body_json">Body Json</label>
                      <input type="text" name="body_json" value="" class="form-control" id="body_json" placeholder="Enter body json">
                    </div>
                    <div class="form-group col-md-12">
                      <label for="pre_request_script">Pre request script</label>
                      <textarea name="pre_request_script" value="" class="form-control" id="pre_request_script" placeholder="Enter pre_request_script"></textarea>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="tests">Tests</label>
                      <input type="text" name="tests" value="" class="form-control" id="tests" placeholder="Enter tests">
                    </div>
                  </div>
                </form> 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary submit-form">Save</button>
              </div>
              
            </div>
           
        </div>
      </div>
    </div>
</div>



<div id="view-domain" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="view-domain-content">
        
        </div>
      </div>
    </div>
</div>

@section('scripts')
  <script type="text/javascript">

    // $('ul.pagination').hide();
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     // debug: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 0,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function () {
    //       $('ul.pagination').first().remove();
    //       $('ul.pagination').hide();
    //     }
		// });
    
    $(document).on("click",".openmodeladdpostman",function(e){
      $('#titleUpdate').html("Add");
        $('#postmanform').find("input[type=text], textarea").val("");
    });
    $(document).on("click",".delete-postman-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/postman/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success']('Postman deleted successfully!!!', 'success'); 
              location.reload();
            } else {
              toastr['error'](response.message, 'error'); 
            }
          }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addPostman").hide();
            toastr['error'](errObj.message, 'error');
          });
          }
      });
    $(document).on("click",".submit-form",function(e){
        e.preventDefault();
        var $this = $(this);
        if($('#titleUpdate').text() == 'Add')
          $("#post_id").val("");
        $.ajax({
          url: "/postman/create/",
          type: "post",
          data:$('#postmanform').serialize()
        }).done(function(response) {
          if(response.code = '200') {
            $('#loading-image').hide();
            $('#addPostman').modal('hide');
            toastr['success']('Postman added successfully!!!', 'success'); 
            location.reload();
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           //$("#addMail").hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    
    $(document).on("click",".edit-postman-btn",function(e){
        e.preventDefault();
        $('#titleUpdate').html("Update");
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/edit/",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            form = $('#postmanform');
            $.each(response.data, function(key, v) {
              if(form.find('[name="'+key+'"]').length){
                  form.find('[name="'+key+'"]').val(v);
              } else if(key == 'request_type'){
                    form.find('[name="'+key+'s"]').val(v);
              }else if(key == 'folder_name'){
                $( "#folder_name" ).val(v);
              }else if(form.find('[name="'+key+'[]"]').length){
                  form.find('[name="'+key+'[]"]').val(v);
              }      
            });
            $("#folder_name").html(response.ops);
            $('#addPostman').modal('show');
            toastr['success']('Postman added successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#addPostman").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".postman-history-btn",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/history/",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            var t = '';
            $.each(response.data, function(key, v) {
              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanHistory").html(t);
            $('#postmanHistory').modal('show');
            toastr['success']('Postman added successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    


  </script>
@endsection
