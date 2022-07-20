@extends('layouts.app')

@section('title', 'Update Log')

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
            <h2 class="page-heading">Update Log</h2>
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
      <form class="form-inline" action="/updateLog/search" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <?php $requestNamrArr = []; ?>
              @foreach ($updateLog as $key => $logD)
                  <?php array_push($requestNamrArr,$logD->api_url);?>
              @endforeach
              <?php $requestNamrArr = array_unique($requestNamrArr); ?>
              <select name="api_url"  class="form-control" id="api_url" >
                <option value="">--Select Request Name--</option>
                @foreach ($requestNamrArr as $key => $reqName)
                <?php $selected  = '';
                if($reqName == request('api_url')) {
                  $selected  = 'selected = "selected"';
                }
                  ?>
                    <option {{$selected}} value="{{$reqName}}">{{$reqName}}</option>
                @endforeach
              </select>
              {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              {{-- <input type="text" placeholder="Request Type" class="form-control" name="api_type" value="{{request('api_type')}}"> --}}
                <select name="api_type" value="" class="form-control" id="api_types" >
                 
                  <option value="">--select Method</option>
                  <option value="GET" <?php if(request('api_type') == 'GET') { echo 'selected'; } ?>>GET</option>
                  <option value="POST" <?php if(request('api_type') == 'POST') { echo 'selected'; } ?>>POST</option>
                  <option value="PUT" <?php if(request('api_type') == 'PUT') { echo 'selected'; } ?>>PUT</option>
                  <option value="PATCH" <?php if(request('api_type') == 'PATCH') { echo 'selected'; } ?>>PATCH</option>
                  <option value="DELETE" <?php if(request('api_type') == 'DELETE') { echo 'selected'; } ?>>DELETE</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
          <a href="/updateLog" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
        </div>
      </form>

    
    </div>
    
	</br> 
  <div class="row m-0" >
    <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
	<div class="table-responsive mt-2" style="overflow-x: auto !important;">
      <table class="table table-bordered text-nowrap">
        <thead>
          <tr>
            <th style="width: 3%;">ID</th>
            <th style="width: 40%;overflow-wrap: anywhere;">API Url</th>
            <th style="width: 5%;overflow-wrap: anywhere;">Device Name</th>
            <th style="width: 5%;overflow-wrap: anywhere;">Api Type</th>
            <th style="width: 25%;overflow-wrap: anywhere;">User Name</th>
            <th style="width: 10%;overflow-wrap: anywhere;">Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($updateLog as $key => $logData)
              <tr>
                <td>{{$logData->id}}</td>
                <td class="expand-row-msg" data-name="url" data-id="{{$logData->id}}">
                    <span class="show-short-url-{{$logData->id}}">{{ str_limit($logData->api_url, 20, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-url-{{$logData->id}} hidden">{{$logData->api_url}}</span>
                </td>
                <td>{{$logData->device}}</td>
                <td>{{$logData->api_type}}</td>
                <td>{{$logData->user_id}}</td>
                <td>
                  <a class="btn delete-updateLog-btn"  data-id="{{ $logData->id }}" href="#"><img  data-id="{{ $logData->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                </td>
              </tr>
           
            
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $updateLog->appends(Request::except('page'))->links() !!}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>
  </div>
@endsection

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
          return Random.name(true)
        },
        "id|+1": 1,
        "disabled|1-2": true,
        groupName: 'Group Name',
        "groupId|1-4": 1,
        "selected": true
      }]
    });
      $('.dropdown-mul-1').dropdown({
      data: json1.data,
      limitCount: 40,
      multipleMode: 'label',
      choice: function () {
        // console.log(arguments,this);
      }
    });

    $('.dropdown-sin-1').dropdown({
      readOnly: true,
      input: '<input type="text" maxLength="20" placeholder="Search">'
    });

    </script>
  </div>
 
  <script type="text/javascript">

    $('.multiselect').multiselect({
        enableClickableOptGroups: true
    });
    $(document).on("click",".openmodeladdpostman",function(e){
      $('#titleUpdate').html("Add");
        $('#postmanform').find("input[type=text], textarea").val("");
    });

    
    $(document).on("click",".delete-updateLog-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/updateLog/delete",
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
    
    $(document).ready(function(){
		  $('#per_user_name').select2();
    });
	
  </script>
@endsection
