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

<!--TIMEDOCTOR ACCOUNT ADD PROJECT SECTION START -->
<div id="time_doctor_create_project" class="modal fade" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Create Time Doctor Project</span></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="col-md-12">
               <form id="create_time_doctor_project">
                  <?php echo csrf_field(); ?>
                  <div class="form-group">
                     <label for="time_doctor_project_name">Project Name</label>
                     <input type="text" class="form-control" name="time_doctor_project_name">
                  </div>
                  <div class="form-group">
                     <label for="time_doctor_project_description">Project Description</label>
                     <textarea name="time_doctor_project_description" class="form-control"></textarea>
                  </div>
                  <div class="form-group">
                      <strong>Account:</strong>    
                      <?php echo Form::select("time_doctor_account",['' => ''],null,["class" => "form-control  time_doctor_account globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.time_doctor_accounts'), 'data-placeholder' => 'Select Account']); ?>
                      <label class="select-error error"></label>
                  </div>                  
                  <div class="form-group">
                     <button class="btn btn-secondary">ADD</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
<!--TIMEDOCTOR ACCOUNT ADD PROJECT SECTION END -->

<!--TIMEDOCTOR ACCOUNT EDIT PROJECT SECTION START -->
<div id="time_doctor_edit_program" class="modal fade" role="dialog">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Edit Time Doctor Program</span></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <div class="col-md-12">
               <form id="edit_time_doctor_project">
                  <?php echo csrf_field(); ?>
                  <div class="form-group">
                     <label for="edit_time_doctor_project_name">Project Name</label>
                     <input type="hidden" name="time_doctor_program_id" class="time_doctor_program_id">
                     <input type="text" class="form-control edit_time_doctor_project_name" name="edit_time_doctor_project_name">
                  </div>
                  <div class="form-group">
                     <label for="edit_time_doctor_project_description">Project Description</label>
                     <textarea name="edit_time_doctor_project_description" class="form-control edit_time_doctor_project_description"></textarea>
                  </div>
                  <div class="form-group">
                     <button class="btn btn-secondary">SAVE</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
<!--TIMEDOCTOR ACCOUNT EDIT PROJECT SECTION END -->

@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif


<div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Project List From TimeDocter</h2>
</div>


  @if(!empty($projects))
  <div class="row">
    <div class="col-md-12 pr-5 pl-5">
    <button type="button" class="btn btn-secondary float-right-addbtn" id="add_project">+ Add Project</button>
    <button type="button" class="btn btn-danger float-right-addbtn" id="refresh_projects"> Refresh Projects</button>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Project Id</th>
          <th>TimeDocter Project Id</th>
          <th>TimeDocter Project Name</th>
          <th>TimeDocter Company Id</th>
          <th>TimeDocter Account</th>
          <th>Action</th>
        </tr>
      </thead>
      @php  $no=1; @endphp
      @foreach($projects as $project)
      <tbody>
        <tr>
          <td style="vertical-align:middle;">{{ $no++ }}</td>
          <td style="vertical-align:middle;">{{ $project->id }}</td>
          <td style="vertical-align:middle;">{{ $project->time_doctor_project_id }}</td>
          <td style="vertical-align:middle;">{{ $project->time_doctor_project_name }}</td>
          <td style="vertical-align:middle;">{{ $project->time_doctor_company_id }}</td>
          <td style="vertical-align:middle;">{{ $project->account_detail->time_doctor_email }}</td>
          <td style="vertical-align:middle;"><button type="button" class="btn btn-secondary edit_project" data-id="{{ $project->id }}">Edit Project</button></
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
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $(document).on('click', '#refresh_projects', function(e){
      e.preventDefault();
      $("#time_doctor_account_select_modal").modal('show');
  });  

  $(document).on("click", "#add_project" , function(){
    $("#time_doctor_create_project").modal('show');
    $("#time_doctor_project_name").val('');
    $("#time_doctor_project_description").val('');
    $("#time_doctor_account").val('');
    $(".error").html('');
  });

  $(document).on("click", ".edit_project" , function(){
    var project_id = $(this).attr('data-id');
    $.ajax({
        type: "POST",
        url: "{{ route('time-doctor.get-project-detail') }}",
        data: {projectId:project_id},
        success: function(response) {
          console.log( response.data );
          $(".edit_time_doctor_project_name").val(response.data.name);
          $(".edit_time_doctor_project_description").val(response.data.description);
          $(".time_doctor_program_id").val( project_id );
          $(".error").html('');
          $("#time_doctor_edit_program").modal('show');
        }
    })
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
            url: "{{ route('time-doctor.refresh-project-by-id') }}",
            data: formdata,
            success: function(response) {
              toastr['success']('Time Doctor projects refreshed', 'success');
              window.location.reload();
            }
        })
    }
  });

  $("#create_time_doctor_project").validate({
    rules: {
      time_doctor_project_name: "required",
      time_doctor_project_description: "required",
      time_doctor_account: "required",
    },
    messages: {
      time_doctor_project_name: "Please enter project name",
      time_doctor_project_description: "Please enter project description",
      time_doctor_account:  "Please select account",
    },
    errorPlacement: function(error, element) {
      if (element.attr("name") == "time_doctor_account" ) {
          error.insertAfter( $('.select-error') );
      } else {
        error.insertAfter(element);
      }
    },  
    submitHandler: function (form) {
        var formdata = $('#create_time_doctor_project').serialize();
        $.ajax({
            type: "POST",
            url: "{{ route('time-doctor.addproject') }}",
            data: formdata,
            success: function(response) {
              $('#create_time_doctor_project').trigger("reset");
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

  $("#edit_time_doctor_project").validate({
    rules: {
      edit_time_doctor_project_name: "required",      
    },
    messages: {
      edit_time_doctor_project_name: "Please enter name",      
    },
    errorPlacement: function(error, element) {
      error.insertAfter(element);
    },  
    submitHandler: function (form) {
        var formdata = $('#edit_time_doctor_project').serialize();
        $.ajax({
            type: "POST",
            url: "{{ route('time-doctor.update-program-by-id') }}",
            data: formdata,
            success: function(response) {
              toastr['success']('Time Doctor project updated', 'success');
              window.location.reload();
            }
        })
    }
  });

</script>
@endsection