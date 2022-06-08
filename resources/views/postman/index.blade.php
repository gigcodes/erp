@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
      .multiselect {
            width: 100%;
        }
        .multiselect-container li a {
          line-height: 3;
        }
    </style>
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
            <th>User Permission</th>
            <th>Folder Name</th>
            <th>Request Name</th>
            <th>request type</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($postmans as $key => $postman)
            @php
              $userAccessArr = explode(",",$postman->user_permission);
              array_push($userAccessArr, $addAdimnAccessID)
            @endphp
            @if (in_array($userID, $userAccessArr))
              <tr>
                <td>{{$postman->id}}</td>
                <td>
                      <?php 
                        $useNames = '';
                        foreach($users as $user){
                          if(in_array($user->id,$userAccessArr)) { 
                            //$selected = 'selected';
                            //echo '<option value="'.$user->id.'" '.$selected.' data-folder_name="'.$user->name.'">'.$user->name.'</option>';
                            $useNames .= '<b>'.$user->id.'</b>-'.$user->name.' <b>Email => </b>'.$user->email.', <br/>';
                          }
                        }
                      ?>
                    
                    <a href="#" id="see_users" data-user_details="{{$useNames}}" data-toggle="modal" data-target="#postmanUserDetailsModel">See</a>
                  </td>
                <td>{{$postman->name}}</td>
                <td>{{$postman->request_name}}</td>
                <td>{{$postman->request_type}}</td>
                <td>
                  <a title="Send Request" class="btn btn-image postman-send-request-btn pd-5 btn-ht" data-id="{{ $postman->id }}" href="javascript:;">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                  </a>
                  <a class="btn btn-image edit-postman-btn" data-id="{{ $postman->id }}"><img data-id="{{ $postman->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                  <a class="btn delete-postman-btn"  data-id="{{ $postman->id }}" href="#"><img  data-id="{{ $postman->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                  <a class="btn postman-history-btn"  data-id="{{ $postman->id }}" href="#"><i class="fa fa-history" aria-hidden="true"></i></a>
                  <a title="Preview Response" data-id="{{ $postman->id }}" class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                  <a title="Preview Requested" data-id="{{ $postman->id }}" class="btn btn-image preview_requested pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye" aria-hidden="true"></i></a>
                </td>
              </tr>
            @endif
            
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

<div id="postmanRequesteHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Postman Request History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayPostmanRequestHistory">
                </tbody>
              </table>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<div id="postmanUserDetailsModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Users</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body postmanUserDetailsModelBody">
              
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div id="postmanResponseHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Postman Response History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Response</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayPostmanResponseHistory">
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
                      <label for="title">User Name</label>
                      <select name="user_permission[]" multiple class="folder_name form-control multiselect" id="user_permission" required>
                        <option>--Users--</option>
                        <?php 
                          foreach($users as $user){
                              echo '<option value="'.$user->id.'" data-folder_name="'.$user->name.'">'.$user->name.'</option>';
                          }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="title">Folder Name</label>
                      <input type="hidden" name="folder_real_name" id="folder_real_name" >
                      <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                        <option>--Folder--</option>
                        <?php 
                          $ops = 'id';
                          foreach($folders as $folder){
                              echo '<option value="'.$folder->id.'" data-folder_name="'.$folder->name.'">'.$folder->name.'</option>';
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
                      <select name="request_types" value="" class="form-control" id="request_types" >
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                      </select>
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
                      <select name="authorization_type" value="" class="form-control" id="authorization_type" >
                        <option value="Bearer Token">Bearer Token</option>
                        <option value="Basic Auth">Basic Auth</option>
                      </select>
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
                      <select name="body_type" value="" class="form-control" id="body_type" >
                        <option value="raw">Raw</option>
                      </select>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="body_json">Body Json
                        <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#addPostmanJsonModel">Add Json</button>
                      </label>
                      <?php
                        $postJsonVer = \App\PostmanRequestJsonHistory::all();
                      ?>
                      <select name="body_json" value="" class="form-control" id="body_json" >
                        <option value="">select Json</option>
                        @foreach ($postJsonVer as $jsonVer)
                            <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.'  '.$jsonVer->request_data}}</option>
                        @endforeach
                      </select>
                      {{-- <input type="text" name="body_json" value="" class="form-control" id="body_json" placeholder="Enter body json Ex.  {'name': 'hello', 'type':'not'}"> --}}
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
                                  <label for="title">User Name</label>
                                  <input type="hidden" name="folder_real_name" id="folder_real_name" >
                                  <select name="user_permission[]" multiple class="form-control folder_name" id="user_permission" required>
                                    <option>--Users--</option>
                                    <?php 
                                      foreach($users as $user){
                                          echo '<option value="'.$user->id.'" data-folder_name="'.$user->name.'">'.$user->name.'</option>';
                                      }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="title">Folder Name</label>
                                  <input type="hidden" name="folder_real_name" id="folder_real_name" >
                                  <select name="folder_name" class="form-control folder_name" id="folder_name" required>
                                    <option>--Folder--</option>
                                    <?php 
                                      $ops = 'id';
                                      foreach($folders as $folder){
                                          echo '<option value="'.$folder->id.'" data-folder_name="'.$folder->name.'">'.$folder->name.'</option>';
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
                                  <select name="request_types" value="" class="form-control" id="request_types" >
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                  </select>
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
                                  <select name="authorization_type" value="" class="form-control" id="authorization_type" >
                                    <option value="Bearer Token">Bearer Token</option>
                                    <option value="Basic Auth">Basic Auth</option>
                                  </select>
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
                                  <select name="body_type" value="" class="form-control" id="body_type" >
                                    <option value="raw">Raw</option>
                                  </select>
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="body_json">Body Json
                                    <a href="#" id="addJson"> Add</a>
                                  </label>
                                  <?php
                                    $postJsonVer = \App\PostmanRequestJsonHistory::all();
                                  ?>
                                  <select name="body_json" class="form-control" id="body_json" >
                                    <option value="">select Json</option>
                                    @foreach ($postJsonVer as $jsonVer)
                                        <option value="{{$jsonVer->request_data}}">{{$jsonVer->version_json.'  '.$jsonVer->request_data}}</option>
                                    @endforeach
                                  </select>
                                  {{-- <input type="text" name="body_json" value="" class="form-control" id="body_json" placeholder="Enter body json Ex.  {'name': 'hello', 'type':'not'}"> --}}
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
            </div>
           
        </div>
      </div>
    </div>
</div>


<div id="addPostmanJsonModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add Json</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="postmanform" method="post">
                @csrf
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="jsonVersion">Tests</label>
                    <input type="text" name="jsonVersion" required value="" class="form-control" id="jsonVersion" placeholder="Enter Json Here">
                  </div>
                </div>
              </form> 
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-secondary postman-addJson">Save</button>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="/js/bootstrap-multiselect.min.js"></script>
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
    
    $('.multiselect').multiselect({
        enableClickableOptGroups: true
    });
    $(document).on("click",".openmodeladdpostman",function(e){
      $('#titleUpdate').html("Add");
        $('#postmanform').find("input[type=text], textarea").val("");
    });

    $(document).on("click","#see_users",function(e){
        e.preventDefault();
        //debugger;
        var $this = $(this);
        var id = $this.data('user_details');
          $('.postmanUserDetailsModelBody').html(id);
    });

    $(document).on("change",".folder_name",function(e){
        e.preventDefault();
          var folder_name = $(this).find(':selected').attr('data-folder_name');
          //debugger;
          $('#folder_real_name').val(folder_name);
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
            //location.reload();
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
                  $.each(v.split(","), function(i,e){
                      $("#user_permission option[value='" + e + "']").prop("selected", true);
                  });
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

    $(document).on("click",".postman-send-request-btn",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/send/request",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            toastr['success']('Postman requested successfully!!!', 'success'); 
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".preview_response",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/response/history/",
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
              t += '<td>'+v.response+'</td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayPostmanResponseHistory").html(t);
            $('#postmanResponseHistoryModel').modal('show');
            toastr['success']('Postman response listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanResponseHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".preview_requested",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/postman/requested/history/",
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
            $(".tbodayPostmanRequestHistory").html(t);
            $('#postmanRequesteHistoryModel').modal('show');
            toastr['success']('Postman Requeste listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanRequesteHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    $(document).on("click","addJson",function(e){
      $('#addPostmanJsonModel').modal('show');
    });
    $(document).on("click",".postman-addJson",function(e){
        e.preventDefault();
        var jsonData = $('#jsonVersion').val();;
        $.ajax({
          url: "postman/add/json/version",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            json_data:jsonData
          }
        }).done(function(response) {
          if(response.code = '200') {
            $('#body_json').append(`<option value="${response.request_data}">
                                       ${response.data.version_json+' '+response.data.request_data}
                                  </option>`);
            toastr['success']('Json Added successfully!!!', 'success'); 
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           toastr['error'](errObj.message, 'error');
        });
    });
    
  </script>
@endsection
