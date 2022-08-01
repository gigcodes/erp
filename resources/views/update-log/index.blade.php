@extends('layouts.app')

@section('title', 'Update Log')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
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

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <div class="card d-normal">
        <div class="card-header">
          <h4>
            <a class="collapsed card-link" data-toggle="collapse" href="#collapseSearch" aria-expanded="true">
              <i class="fa fa-arrow-up"></i>
              <i class="fa fa-arrow-down"></i>
              Filter
            </a>
          </h4>
        </div>
        <div id="collapseSearch" class="collapse show">
          <div class="card-body">
            <form action="/updateLog/search" method="GET">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">API Urls</label>
                        <select class="form-control select2" name="api_url">
                          <option value=""></option>
                          {!! makeDropdown($listApiUrls ?? [], request('api_url')) !!}
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">Devices</label>
                        <select class="form-control select2" name="device">
                          <option value=""></option>
                          {!! makeDropdown($listDevices ?? [], request('device')) !!}
                        </select>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="form-label">API Methods</label>
                        <select class="form-control select2" name="api_type">
                          <option value=""></option>
                          {!! makeDropdown($listApiMethods ?? [], request('api_type')) !!}
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary">Search</button>
                  <a href="/updateLog" class="btn btn-outline-secondary">Clear</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">

      <div class="table-responsive mt-2" style="overflow-x: auto !important;">
        <table class="table table-bordered" style="border: 1px solid #ddd !important;">
          <thead>
            <tr>
              <th style="width: 4%;">ID</th>
              <th style="width: 10%;">API Url</th>
              <th style="width: 8%;">Device</th>
              <th style="width: 6%;">Api Type</th>
              <th style="width: 20%;word-break: break-word;">Request Body</th>
              <th style="width: 8%;">Response Code</th>
              <th style="width: 20%;">Response Body</th>
              <th style="width: 6%;">App Version</th>
              <th style="width: 8%;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($updateLog->count()) { ?>
              <?php foreach ($updateLog as $key => $logData) { ?>
                <tr>
                  <td>{{$logData->id}}</td>
                  <td class="expand-row-msg" data-name="url" data-id="{{$logData->id}}">
                    <span class="show-short-url-{{$logData->id}}">{{ str_limit($logData->api_url, 20, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-url-{{$logData->id}} hidden">{{$logData->api_url}}</span>
                  </td>
                  <td>{{$logData->device}}</td>
                  <td>{{$logData->api_type}}</td>
                  <td style="word-break: break-word;" >{!! $logData->request_body ?: '-' !!}</td>
                  <td>{{$logData->response_code}}</td>
                  <td style="word-break: break-word;" >{!! $logData->response_body ?: '-' !!}</td>
                  <td>{{$logData->app_version}}</td>
                  <td>
                    <a class="btn delete-updateLog-btn" data-id="{{ $logData->id }}" href="#"><img data-id="{{ $logData->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                  </td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="10">No records found.</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <div class="text-center">
          {!! $updateLog->appends(Request::except('page'))->links() !!}
        </div>
      </div>
    </div>
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
      name: function() {
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
    choice: function() {
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
  $(document).on("click", ".openmodeladdpostman", function(e) {
    $('#titleUpdate').html("Add");
    $('#postmanform').find("input[type=text], textarea").val("");
  });


  $(document).on("click", ".delete-updateLog-btn", function(e) {
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
        data: {
          id: id
        }
      }).done(function(response) {
        if (response.code = '200') {
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

  $(document).ready(function() {
    $('#per_user_name').select2();
  });

  jQuery(document).ready(function() {
    applySelect2(jQuery('.select2'));
  });
</script>
@endsection