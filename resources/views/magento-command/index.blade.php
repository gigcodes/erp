@extends('layouts.app')

@section('title', 'Magento Command')

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
            <h2 class="page-heading">Magento Commands</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class=" row ">
      <form class="form-inline" action="/magento/command/search" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <select name="website" class="form-control" id="website">
                <option value="">--Select Website--</option>
                  <option value="ERP" @if(request('website') == 'ERP') selected @endif>ERP</option>
                    <?php
                      $ops = 'id';
                    ?>
                  @foreach($websites as $website)
                    <option @if($website->id == request('website')) selected @endif value="{{$website->id}}">{{$website->title}}</option>
                  @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <?php $magentoComArr = []; ?>
              @foreach ($magentoCommand as $key => $magentoCom)
                  <?php array_push($magentoComArr,$magentoCom->command_name);?>
              @endforeach
              <?php $magentoComArr = array_unique($magentoComArr); ?>
              <select name="command_name"  class="form-control" id="command_name" >
                <option value="">--Select Command Name--</option>
                @foreach ($magentoComArr as $key => $comName)
                    <option @if($comName == request('command_name')) selected @endif value="{{$comName}}">{{$comName}}</option>
                @endforeach
              </select>
              {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <select name="user_id"  class="form-control select2" id="user_id" >
                <option value="">--Select User Name--</option>
                @foreach ($users as $key => $user)
                    <option @if($user->id == request('user_id')) selected @endif value="{{$user->id}}">{{$user->name}}</option>
                @endforeach
              </select>
              {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
          <a href="/magento/command" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
        </div>
      </form>
      <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#addPostman">Add Command</button>
     
    </div>
    
	</br> 
  <div class="row m-0" >
    <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
	<div class="table-responsive mt-2" style="overflow-x: auto !important;">
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr>
            <th style="width: 3%;">ID</th>
            <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
            <th style="width: 22%;overflow-wrap: anywhere;">Websites</th>
            <th style="width: 22%;overflow-wrap: anywhere;">Command name</th>
            <th style="width: 10%;overflow-wrap: anywhere;">Action</th>
          </tr>
        </thead>
        <tbody>
			  @foreach ($magentoCommand as $key => $magentoCom)
              <tr>
                <td>{{$magentoCom->id}}</td>
                <td class="expand-row-msg" data-name="userName" data-id="{{$magentoCom->id}}">
                  <span class="show-short-userName-{{$magentoCom->id}}">{{ str_limit($magentoCom->user->name, 5, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-userName-{{$magentoCom->id}} hidden">{{$magentoCom->user->name}}</span>
                </td>
                <td class="expand-row-msg" data-name="websites" data-id="{{$magentoCom->id}}">
                    @if($magentoCom->website_ids == 'ERP')
                        <span class="show-short-websites-{{$magentoCom->id}}">ERP</span>
                        <span style="word-break:break-all;" class="show-full-websites-{{$magentoCom->id}} hidden">ERP</span>
                    @else
                        <span class="show-short-websites-{{$magentoCom->id}}">@if($magentoCom->website){{ str_limit($magentoCom->website->title, 20, '..')}}@endif</span>
                        <span style="word-break:break-all;" class="show-full-websites-{{$magentoCom->id}} hidden">@if($magentoCom->website){{$magentoCom->website->title}}@endif</span>
                    @endif
                </td>
                <td class="expand-row-msg" data-name="command_name" data-id="{{$magentoCom->id}}">
                  <span class="show-short-command_name-{{$magentoCom->id}}">{{ Str::limit($magentoCom->command_name, 20, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-command_name-{{$magentoCom->id}} hidden">{{$magentoCom->command_name}}</span>
                </td>
                <td>
                  <a title="Run Command" class="btn btn-image magentoCom-run-btn pd-5 btn-ht" data-id="{{ $magentoCom->id }}" href="javascript:;">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                  </a>
                  <a class="btn btn-image edit-magentoCom-btn" data-id="{{ $magentoCom->id }}"><img data-id="{{ $magentoCom->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                  <a class="btn delete-magentoCom-btn"  data-id="{{ $magentoCom->id }}" href="#"><img  data-id="{{ $magentoCom->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                  <a title="Preview Response" data-id="{{ $magentoCom->id }}" class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
                  
                </td>
              </tr>
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {{-- {!! $magentoCommand->appends(Request::except('page'))->links() !!} --}}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>
  </div>
@endsection
<div id="commandHistory" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Command History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <tr>
                      <th style="width: 3%;">ID</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">Websites</th>
                      <th style="width: 4%;overflow-wrap: anywhere;">Command name</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                      <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
                    </tr>
                  </tr>
                </thead>
                <tbody class="tbodaycommandHistory">
                </tbody>
              </table>  
            </div>
          </div>
      </div>
    </div>
  </div>
</div>


<div id="commandResponseHistoryModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Command Response History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 3%;">ID</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">Command Name</th>
                      <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                      <th style="width: 4%;overflow-wrap: anywhere;">Date</th>
                  </tr>
                </thead>
                <tbody class="tbodayCommandResponseHistory">
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
                <h5 class="modal-title"><span id="titleUpdate">Add</span> Command</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="magentoForm" method="post">
                  @csrf
                  <div class="form-row">
                    <input type="hidden" id="command_id" name="id" value=""/>
                    
                    <div class="form-group col-md-12">
                      <label for="title">Website</label>
                      <div class="dropdown-sin-1">
                        <?php $websites = \App\StoreWebsite::get(); ?>
                        <select name="websites_ids[]" class="websites_ids form-control dropdown-mul-1" style="width: 100%;" id="websites_ids" required>
                          <option>--Website--</option>
                            <option value="ERP">ERP</option>
                          <?php 
                            foreach($websites as $website){
                                echo '<option value="'.$website->id.'" data-website="'.$website->website.'">'.$website->title.'</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group col-md-12">
                      <label for="command_name">Command Name</label>
                      {{-- <input type="text" name="command_name" value="" class="form-control" id="command_name_search" placeholder="Enter Command name"> --}}
                      <?php $magentoComArr = []; ?>
                        @foreach ($magentoCommand as $key => $magentoCom)
                            <?php array_push($magentoComArr,$magentoCom->command_name);?>
                        @endforeach
                        <?php $magentoComArr = array_unique($magentoComArr); ?>
                        <select name="command_name"  class="form-control" id="command_name_search" style="width: 100%">
                          <option value="">--Select Command Name--</option>
                          @foreach ($magentoComArr as $key => $comName)
                              <option @if($comName == request('command_name')) selected @endif value="{{$comName}}">{{$comName}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                      <label for="command_type">Command</label>
                      {{-- <input type="text" name="command_type" value="" class="form-control" id="command_type" placeholder="Enter request type"> --}}
                      <?php $commandTypeArr = []; ?>
                        @foreach ($magentoCommand as $key => $magentoComT)
                            <?php array_push($magentoComArr,$magentoComT->command_type);?>
                        @endforeach
                        <?php $magentoComArr = array_unique($magentoComArr); ?>
                        <select name="command_type"  class="form-control" id="command_type" style="width: 100%">
                          <option value="">--Select Command Name--</option>
                          @foreach ($magentoComArr as $key => $comType)
                              <option @if($comType == request('command_type')) selected @endif value="{{$comType}}">{{$comType}}</option>
                          @endforeach
                        </select>
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

<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}">
@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="/js/bootstrap-multiselect.min.js"></script>

    <script src="{{asset('js/mock.js')}}"></script>
    <script src="{{asset('js/jquery.dropdown.min.js')}}"></script>
    <script src="{{asset('js/jquery.dropdown.js')}}"></script>
    

    <script>
      var Random = Mock.Random;
      var json1 = Mock.mock({
      "data|10-50": [{
        name: function () {
          //return Random.name(true)
        },
        "id|+1": 1,
        "disabled|1-2": true,
        groupName: 'Group Name',
        "groupId|1-4": 1,
        "selected": true
      }]
    });
    //   $('.dropdown-mul-1').dropdown({
    //   //data: json1.data,
    //   limitCount: 40,
    //   multipleMode: 'label',
    //   choice: function () {
    //     // console.log(arguments,this);
    //   }
    // });

    //  $('.dropdown-sin-11').dropdown({
    //   readOnly: true,
    //   input: '<input type="text" maxLength="25" placeholder="Search">'
    // });

    </script>
  </div>
 
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

    $(document).on("click",".delete-magentoCom-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/magento/command/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success']('Command deleted successfully!!!', 'success'); 
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
          $("#command_id").val("");
        $.ajax({
          url: "/magento/command/add",
          type: "post",
          data:$('#magentoForm').serialize()
        }).done(function(response) {
          if(response.code = '200') {
            $('#loading-image').hide();
            $('#addCommand').modal('hide');
            toastr['success']('Command added successfully!!!', 'success'); 
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
    
    $(document).on("click",".edit-magentoCom-btn",function(e){
        e.preventDefault();
        $('#titleUpdate').html("Update");
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/magento/command/edit",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            form = $('#magentoForm');
            $.each(response.data, function(key, v) {

              if(key == "website_ids") {
                var Values = new Array();
                $.each(v.split(","), function(i,e){
                    console.log(e);
                     //$(".websites_ids option[value='" + e + "']").prop("selected", true);
                     Values.push(e);
                });
                $('.websites_ids').val(Values).trigger('change');
              }
              if(form.find('[name="'+key+'"]').length){
                  form.find('[name="'+key+'"]').val(v);
                  if(key == 'command_name'){
                    $('#command_name_search').val(v).trigger('change');
                  }
                  if(key == 'command_type'){
                    $('#command_type').val(v).trigger('change');
                  }
                  
              }else if(form.find('[name="'+key+'[]"]').length){
                  //form.find('[name="'+key+'[]"]').val(response.ops);
                  //debugger;
                  
                    
                  $.each(v.split(","), function(i,e){
                    console.log(e);
                     // $("#websites_ids option[value='" + e + "']").prop("selected", true);
                  });
              }      
            });
            $('#addPostman').modal('show');
            toastr['success']('Command Listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#addPostman").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".magento-history-btn",function(e){
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
            toastr['success']('Command added successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#postmanHistory").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click", ".magentoCom-run-btn", function (e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
            url: "/magento/command/run",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id
            }
        }).done(function (response) {
            if (response.code = '200') {
                toastr['success']('Command Run successfully!!!', 'success');
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function (errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".preview_response",function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
          url: "/magento/command/history/",
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
              var responseString  = '';
              if(v.response)
                responseString = v.response.substring(0,10);
              var request_data_val  = '';
              if(v.request_data)
                request_data_val = v.request_data.substring(0,10);
              var request_url_val  = '';
              if(v.request_data)
              request_url_val = v.request_url.substring(0,10)
              var commandString  = '';
              if(v.command_name)
                commandString = v.command_name.substring(0,10);
              

              t += '<tr><td>'+v.id+'</td>';
              t += '<td>'+v.userName+'</td>';
              t += '<td  class="expand-row-msg" data-name="command" data-id="'+v.id+'" ><span class="show-short-command-'+v.id+'">'+commandString+'...</span>    <span style="word-break:break-all;" class="show-full-command-'+v.id+' hidden">'+v.command_name+'</span></td>';
              t += '<td  class="expand-row-msg" data-name="response" data-id="'+v.id+'" ><span class="show-short-response-'+v.id+'">'+responseString+'...</span>    <span style="word-break:break-all;" class="show-full-response-'+v.id+' hidden">'+v.response+'</span></td>';
              //t += '<td>'+v.response_code+'</td>';
              //t += '<td  class="expand-row-msg" data-name="request_url" data-id="'+v.id+'" ><span class="show-short-request_url-'+v.id+'">'+request_url_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_url-'+v.id+' hidden">'+v.request_url+'</span></td>';
              //t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
              t += '<td>'+v.created_at+'</td></tr>';
            });
            $(".tbodayCommandResponseHistory").html(t);
            $('#commandResponseHistoryModel').modal('show');
            toastr['success']('Command response listed successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#commandResponseHistoryModel").hide();
           toastr['error'](errObj.message, 'error');
        });
    });


    $(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });
    $(document).ready(function(){
		  $('.select2').select2();
      $("#command_name_search").select2({tags: true});
      $("#command_type").select2({tags: true});
      $(".dropdown-mul-1").select2({});
    });
	
    
  </script>
@endsection
