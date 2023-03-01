@extends('layouts.app')
@section('link-css')
<style type="text/css">
  .float-right-addbtn{
    float: right !important;
    margin-top: 1%;
    margin-right: 0.095rem;
  }
  .form-group {
    padding: 10px;
  }
</style>
@endsection
@section('content')
<!-- SENTRY ACCOUNT MODEL CONTENT START -->
<div id="sentry_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Sentry Account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="add_sentry_account">
              <div class="modal-body">
                  <div class="form-group">
                      <strong>Project:</strong>
                      <input type="text" name="project" class="form-control" id="project">
                      <label class="error"></label>
                  </div>
                  <div class="form-group">
                      <strong>Organization:</strong>
                      <input type="text" name="organization" class="form-control" id="organization">
                      <label class="error"></label>
                  </div>
                  <div class="form-group">
                      <strong>Token:</strong>
                      <input type="text" name="token" class="form-control" id="token">
                      <label class="error"></label>
                  </div>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--SENTRY ACCOUNT MODEL CONTENT END -->
<!-- SENTRY ACCOUNT LISTING MODEL CONTENT START -->
<div id="sentry_account_listing_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Sentry Account List</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-bordered" width="100%">
                    <thead>
                      <tr>
                          <th width="10%">No</th>
                          <th width="20%">Organization</th>
                          <th width="10%">Project</th>
                          <th width="50%">Token</th>
                      </tr>
                    </thead>
                    <tbody id="account_list">                      
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--SENTRY ACCOUNT SELECTION MODEL CONTENT END -->

<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">SENTRY logs</h2>
  </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<div class="table-responsive">
    <form action="{{route('sentry-log')}}" method="post">
      @csrf
      <div class="col-md-2">  
        <select class="form-control" id="project_list" name="project_list">
         <option value="">Select</option>
         @foreach($projects as $project)
         <option value="{{$project['id']}}">{{$project['name']}}</option>
         @endforeach
        </select>
      </div>
      <div class="col-md-2">
          <button type="submit" class="btn btn-secondary" id="search_log">Search</button>
          <button type="button" class="btn btn-secondary" id="load_page">Cancel</button>
      </div>
    </form>
<div class="col-md-8">
  <button type="button" class="btn btn-secondary float-right-addbtn" id="add_account">+ Add Account</button>
  <button type="button" class="btn btn-secondary float-right-addbtn" id="list_account"> List Account</button>
  <button type="button" class="btn btn-danger float-right-addbtn" id="refresh_logs"> Refresh Logs</button>
</div>
  <table class="table table-bordered" id="sentry_log_table">
    <thead>
      <tr>
        <th style="width: 5%">#</th>
        <th style="width: 5%">Id</th>
        <th style="width: 40%">Title</th>
        <th style="width: 10%">Issue Type</th>
        <th style="width: 5%">Issue Category</th>
        <th style="width: 5%">Is Unhandled</th>
        <th style="width: 10%">Project</th>
        <th style="width: 10%">First Seen</th>
        <th style="width: 10%">Last Seen</th>
      </tr>
    </thead>
    @foreach ($sentryLogsData as $key => $row)
    <tr>
      <td>{{ $key+1 }}</td>
      <td>{{ $row['id'] }}</td>
      <td>{{ $row['title'] }}</td>
      <td>{{ $row['issue_type'] }}</td>
      <td>{{ $row['issue_category'] }}</td>
      <td>{{ ($row['is_unhandled']) ? "true":"false" }}</td>
      <td>{{ $row['project'] }}</td>
      <td>{{ date("d-m-y H:i:s", strtotime($row['first_seen'])) }}</td>
      <td>{{ date("d-m-y H:i:s", strtotime($row['last_seen'])) }}</td>
    </tr>
    @endforeach
  </table>
</div>
@endsection
@section("scripts")
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#add_sentry_account").validate({
      rules: {
        project: "required",
        organization: "required",
        token: "required",
      },
      messages: {
        project: "Please enter project",
        organization: "Please enter organization",
        token: "Please enter token",
      },
      errorPlacement: function(error, element) {
        error.insertAfter(element);
      },  
      submitHandler: function (form) {
          var formdata = $('#add_sentry_account').serialize();
          $.ajax({
              type: "POST",
              url: "{{ route('sentry.adduser') }}",
              data: formdata,
              success: function(response) {
                $('#add_sentry_account').trigger("reset");
                if(response.code == 200){
                  toastr['success'](response.message, 'success');
                } else {
                  toastr['error'](response.message, 'error');
                }
                window.location.reload();
              }
          })
      }
    });

    $(document).on("click", "#add_account" , function(){        
      $("#sentry_modal").modal('show');
      $("#project").val('');
      $("#organization").val('');
      $("#token").val('');
      $(".error").html('');
    });

    $(document).on("click", "#list_account" , function(){
      $("#sentry_account_listing_modal").modal('show');
      $.ajax({
          type: "POST",
          url: "{{ route('sentry.display-user') }}",        
          success: function(response) {
            $('#account_list').html( response );
          }
      })
    });

    $(document).on('click', '#refresh_logs', function(e){
        $.ajax({
            type: "POST",
            url: "{{ route('sentry.refresh-logs') }}",          
            success: function(response) {
              toastr['success'](response.message, 'success');
              $("#load_page").trigger('click');
            }
        });
    });

    $(document).on('click', '#load_page', function(){
      url = "{{ route('sentry-log') }}";
      window.location.href = url;
    });

  });
</script>
@endsection