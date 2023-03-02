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
<!-- TIMEDOCTOR ACCOUNT MODEL CONTENT START -->
<div id="time_doctor_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Time Doctor Account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="add_time_doctor_account">
              <div class="modal-body">
                  <div class="form-group field_spacing">
                      <strong>Email:</strong>
                      <input type="text" name="email" class="form-control" id="email">
                      <label class="error"></label>
                  </div>
                  <div class="form-group field_spacing">
                      <strong>Password:</strong>
                      <input type="text" name="password" class="form-control" id="password">
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
<!--TIMEDOCTOR ACCOUNT MODEL CONTENT END -->
<!-- TIMEDOCTOR ACCOUNT LISTING MODEL CONTENT START -->
<div id="timedocter_account_listing_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Time Doctor Account List</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-bordered" width="100%">
                    <thead>
                      <tr>
                          <th width="10%">No</th>
                          <th width="20%">Email</th>
                          <th width="10%">Password</th>
                          <th width="50%">Access Token</th>
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
<!--TIMEDOCTOR ACCOUNT SELECTION MODEL CONTENT END -->

<!-- TIMEDOCTOR ACCOUNT SELECTION MODEL CONTENT START -->
<div id="time_doctor_account_select_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Select Time Doctor Account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="select_time_doctor_account">
              <div class="modal-body">
                  <div class="form-group field_spacing">
                      <strong>Email:</strong>    
                      <?php echo Form::select("time_doctor_account",['' => ''],null,["class" => "form-control  time_doctor_account globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_accounts'), 'data-placeholder' => 'Select Account']); ?>
                      <label class="select-error error"></label>
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
<!--TIMEDOCTOR ACCOUNT SELECTION MODEL CONTENT END -->

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif


<div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Members List From TimeDoctor</h2>
</div>

  @if(!empty($members))
  <div class="row">
    <div class="col-md-12 pr-5 pl-5">
    <button type="button" class="btn btn-secondary float-right-addbtn" id="add_account">+ Add Account</button>
    <button type="button" class="btn btn-secondary float-right-addbtn" id="list_account"> List Account</button>
    <button type="button" class="btn btn-danger float-right-addbtn" id="refresh_users"> Refresh Users</button>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>TimeDoctor Id</th>
          <th>TimeDoctor User Id</th>
          <th>TimeDoctor Email</th>
          <th>TimeDoctor Account</th>
          <th>User</th>
        </tr>
      </thead>
      @php  $no=1; @endphp
      @foreach($members as $member)
      <tbody>
        <tr>
          <td style="vertical-align:middle;">{{ $no++ }}</td>
          <td style="vertical-align:middle;">{{ $member->id }}</td>
          <td style="vertical-align:middle;">{{ $member->time_doctor_user_id }}</td>
          <td style="vertical-align:middle;">{{ $member->email }}</td>
          <td style="vertical-align:middle;">{{ $member->account->email }}</td>
          <td style="vertical-align:middle;">
             <div class="form-group"style="margin-top: -10px;margin-bottom:-10px;">
                <select onchange="saveUser(this)"class="form-control">
                 <option value="unassigned">Unassigned</option>
                 @foreach($users as $user)
                 <option value="{{$user->id}}|{{ $member->time_doctor_user_id }}" <?= ($member->user_id == $user->id) ? 'selected' : '' ?>>{{$user->name}}</option>
                 @endforeach
                </select>
            </div>
          </td>
        </tr>
      </tbody>
      @endforeach
    </table>
    <br>
    <hr>
  </div>
  </div>  
  @endif

@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
@section("scripts")
<!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> -->
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript">

  function saveUser(a) {
    var selectedValue = (a.value || a.options[a.selectedIndex].value); //crossbrowser solution =)
    console.log('selectedValue', selectedValue);
    if (selectedValue != 'unassigned') {
      var splitValues = selectedValue.split('|');
      var userId = splitValues[0];
      var timeDoctorUserId = splitValues[1];


      var xhr = new XMLHttpRequest();
      var url = "link_time_doctor_user";
      xhr.open("POST", url, true);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.onreadystatechange = function() {        
        if (xhr.readyState === 4 && xhr.status === 200) {
          var json = JSON.parse(xhr.responseText);
          console.log("EMAIL = "+json.email + ", " + json.password);
        }
      };
      var data = JSON.stringify({
        "user_id": userId,
        "time_doctor_user_id": timeDoctorUserId
      });
      xhr.send(data);
    }
  }

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $(document).on("click", "#add_account" , function(){
    $("#time_doctor_modal").modal('show');
    $("#email").val('');
    $("#password").val('');
    $(".error").html('');
  });

  $(document).on("click", "#list_account" , function(){
    $("#timedocter_account_listing_modal").modal('show');
    $.ajax({
        type: "POST",
        url: "{{ route('time-doctor.display-user') }}",        
        success: function(response) {
          $('#account_list').html( response );
        }
    })
  });

  $(document).on('click', '#refresh_users', function(e){
      e.preventDefault();
      $("#time_doctor_account_select_modal").modal('show');
  });

  $(document).on("click", ".get_token", function(){
      var getId = $(this).attr('data-id');
      $.ajax({
          type: "POST",
          url: "{{ route('time-doctor.getToken') }}",
          data: {id:getId},
          success: function(response) {
            if(response.code == 200){
              toastr['success'](response.message, 'success');
            } else {
              toastr['error'](response.message, 'error');
            }
            window.location.reload();
          }
      })
  })

  $("#add_time_doctor_account").validate({
    rules: {
      email: "required",
      password: "required",
    },
    messages: {
      email: "Please enter email",
      password: "Please enter password",
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element);
    },  
    submitHandler: function (form) {
        var formdata = $('#add_time_doctor_account').serialize();
        $.ajax({
            type: "POST",
            url: "{{ route('time-doctor.adduser') }}",
            data: formdata,
            success: function(response) {
              $('#add_time_doctor_account').trigger("reset");
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

  $("#select_time_doctor_account").validate({
    rules: {
      time_doctor_account: "required",      
    },
    messages: {
      time_doctor_account: "Please select account",      
    },
    errorPlacement: function(error, element) {
      error.insertAfter( $('.select-error') );
    },  
    submitHandler: function (form) {
        var formdata = $('#select_time_doctor_account').serialize();
        $.ajax({
            type: "POST",
            url: "{{ route('time-doctor.refresh-user-by-id') }}",
            data: formdata,
            success: function(response) {
              toastr['success']('Time Doctor users refreshed', 'success');
              window.location.reload();
            }
        })
    }
  });

</script>
@endsection