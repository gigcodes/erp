<div id="createVendorCvModal" class="modal fade" role="dialog">
  <div class="modal-dialog  modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" id="vandor-cv-form" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Create CV</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body row">
          <input type="hidden" class="hidden-vendor-id" name="vendor_id">
          <div class="form-group col-3">
            <select class="form-control validate" name="pre_name" style="width: 37% !important;float: left;">
              <option>Mr.</option>
              <option>Miss.</option>
              <option>Mrs.</option>
            </select>
            <input type="text" id="first_name" name="first_name" class="form-control validate" placeholder="First name" style="width: 63%;float: left;">
          </div>
          <div class="form-group col-3">
            <input type="text" id="second_name" name="second_name" class="form-control validate" placeholder="Second name">
          </div>

          <div class="form-group  col-3">
            <input type="email" id="email" name="email" class="form-control validate" placeholder="Your email">
          </div>

          <div class="form-group col-3">
            <input type="text" id="mobile" name="mobile" class="form-control validate" placeholder="Mobile No. Ex. 987654321">
          </div>
          <div class="form-group col-12">
            <textarea type="text" id="career_objective" name="career_objective" class="md-textarea form-control" rows="3" placeholder="Career Objective Description"></textarea>
          </div>

          <div class="form-group col-3">
            <input type="text" id="salary_in_usd" name="salary_in_usd" class="form-control validate" placeholder="Salary IN USD">
          </div>
          <div class="form-group col-3">
            <input type="text" id="expected_salary_in_usd" name="expected_salary_in_usd" class="form-control validate" placeholder="Expected Salary IN USD">
          </div>

          <div class="form-group  col-3">
            <input type="text" id="email" name="preferred_working_hours" class="form-control validate" placeholder="Preferred working hours">
          </div>
          <div class="form-group col-3">
            <input type="text" id="start_time" name="start_time" class="form-control validate" placeholder="Start Time">
          </div>
          <div class="form-group col-3">
            <input type="text" id="end_time" name="end_time" class="form-control validate" placeholder="End Time">
          </div>
          <div class="form-group col-3">
            <input type="text" id="time_zone" name="time_zone" class="form-control validate" placeholder="Time Zone">
          </div>
          
          <div class="form-group  col-3">
            <input type="text" id="email" name="preferred_working_days" class="form-control validate" placeholder="Preferred working hours">
          </div>
          <div class="form-group col-3">
            <input type="text" id="start_day" name="start_day" class="form-control validate" placeholder="Start day Ex. Monday">
          </div>
          <div class="form-group col-3">
            <input type="text" id="end_time" name="end_time" class="form-control validate" placeholder="End day Ex. Friday, Saturday">
          </div>
          <div class="form-group col-3">
            <input type="checkbox" id="part_time" name="part_time" class="form-input-check validate" style="height: unset"> <b>Part Time</b>
            <input type="checkbox" id="part_time" name="full_time" class="form-input-check validate" style="height: unset"> <b>Full Time</b>
          </div>
          <div class="form-group col-6">
            <input type="text" id="end_time" name="job_responsibilities" class="form-control validate" placeholder="Job Responsibilities">
          </div>
          <div class="form-group col-12">
            <input type="text" id="end_time" name="projects_worked" class="form-control validate" placeholder="Projects Worked">
          </div>
          <div class="form-group col-12">
            <input type="text" id="end_time" name="tool_used" class="form-control validate" placeholder="Tool Used">
          </div>
          <div class="form-group col-12">
            <label>Full Time Freelancer</label>
            <input type="checkbox" id="fulltime_freelancer" name="fulltime_freelancer[]" value="Yes" class="" style="height: unset;"> Yes
            <input type="checkbox" id="fulltime_freelancer" name="fulltime_freelancer[]" value="No" class="" style="height: unset;"> No
          </div>
          <div class="row col-12 current_assigment_div">
              <div class="form-group col-4">
                <input type="text" id="current_assignments" name="current_assignments[]" class="form-control validate" placeholder="Current Assignments">
              </div>
                <div class="form-group col-4">
                <input type="text" id="current_assignments_description" name="current_assignments_description[]" class="form-control validate" placeholder="Description">
              </div>
              <div class="form-group col-4">
                <input type="text" id="current_assignments_hours_utilisted" name="current_assignments_hours_utilisted[]" class="form-control validate" placeholder="Hours Utilisted">
              </div>
          </div>
          <div class="form-group col-12">
            <button type="button" style="cursor:pointer" class="btn btn-image add-current-assignments" title="Add Current Assignments"><i class="fa fa-plus" aria-hidden="true"></i>Add Current Assignments</button>
          </div>
          

          <div class="col-12 work_experiance_model">
            <label>Work Experience</label>( Please add details of your work experience with the latest records first )
            <hr>
            <div class="row">
                <div class="form-group col-2">
                  <input type="text" name="work_experiance[]" class="form-control validate work_experiance" placeholder="Work Experiance Ex. 1 Year or 2 year">
                </div>
                <div class="form-group col-2">
                  <input type="text" id="date_from" name="date_from[]" class="form-control validate date_from datepicker_monthYear" placeholder="From">
                </div>
                <div class="form-group col-2">
                  <input type="text" id="date_to" name="date_to[]" class="form-control validate date_to datepicker_monthYear" placeholder="To">
                </div>
                <div class="form-group col-2">
                  <input type="text" name="reason_for_leaving[]" class="form-control validate reason_for_leaving" placeholder="Reason for Leaving">
                </div>

                <div class="form-group col-2">
                  <input type="text"  name="designation[]" class="form-control validate designation" placeholder="Designation">
                </div>
                <div class="form-group col-2">
                  <input type="text"  name="organization[]" class="form-control validate organization" placeholder="Organization">
                </div>
            </div>
            <div class="project_detail">
              <div  class="row">
                <div class="form-group col-2">
                  <input type="text" name="project0[]" class="form-control validate project" placeholder="Project">
                </div>
                <div class="form-group col-2">
                  <input type="text"  name="dev_role0[]" class="form-control validate dev_role" placeholder="Role">
                </div>
                <div class="form-group col-2">
                  <input type="text"name="tools0[]" class="form-control validate tools" placeholder="Tools">
                </div>
              </div>
              <button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button>
            </div>
          </div>
          <div class="col-12">
            <input type="hidden" id="work_expariance" value="0" /> 
            <button type="button" style="cursor:pointer" class="btn btn-image add-work-expariance" title="Add Work Expirenace" data-work_expariance="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Work Expirenace</button>
          </div>
          {{-- <div class="form-group row"> --}}
            
            <div class="form-group col-12">
              <hr>
              <label data-error="wrong" data-success="right" for="name">Professional Expertise</label>
            </div>
            <div class="form-group col-4">
              <input type="text" id="soft_framework" name="soft_framework" class="form-control validate" placeholder="Frameworks" >
            </div>
            <div class="form-group col-4">
              <select id="soft_proficiency" name="soft_proficiency" class="form-control validate" aria-label="Default select example">
                  <option value="">Proficiency</option>
                  <option value="0">0</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                  <option value="8">8</option>
                  <option value="9">9</option>
                  <option value="10">10</option>
              </select>
            </div>
            
            <div class="form-group col-4">
              <input type="text" id="soft_experience" name="soft_experience" class="form-control validate" placeholder="Experience">
            </div>
            <div class="form-group col-6">
              <textarea id="soft_remark" name="soft_remark" class="form-control validate" placeholder="Remark"></textarea>
            </div>
            <div class="form-group col-6">
              <textarea id="soft_description" name="soft_description" class="form-control" placeholder="Description"></textarea>
            </div>
            
          {{-- </div> --}}
          <div  class="form-group col-12">
            <hr>
            <label data-error="wrong" data-success="right" for="name">Personal details</label>
          </div>
          <div  class="form-group row mx-auto">
              <div class="form-group col-3">
                <input type="text" id="father_name" name="father_name" class="form-control validate" placeholder="Fathers Name">
              </div>
    
              <div class="form-group  col-3">
                <input type="text" id="dob" name="dob" class="form-control validate" placeholder="Date of Birth">
              </div>
    
              <div class="form-group col-3">
                <select class="form-control validate" name="gender">
                  <option value="" >Gender</option>
                  <option value="Male" >Male</option>
                  <option value="Female">Female</option>
                </select>
              </table>
                
              </div>
    
              <div class="form-group col-3">
                <select class="form-control validate" name="marital_status">
                  <option value="">Marital Status</option>
                  <option value="Married" >Married</option>
                  <option value="Unmarried">Unmarried</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
              <div class="form-group col-3">
                <input type="text" id="langauge_know" name="langauge_know" class="form-control" placeholder="Languages Known Ex. English etc"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="hobbies" name="hobbies" class="form-control" placeholder="Hobbies Ex. Reading, Singging, Learning"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="city" name="city" class="form-control" placeholder="City "/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="state" name="state" class="form-control" placeholder="State"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="country" name="country" class="form-control" placeholder="Country"/>
              </div>
              <div class="form-group col-6">
                <input type="text" id="pin_code" name="pin_code" class="form-control" placeholder="Pin Code"/>
              </div>
              
              <div class="form-group col-12 address_div">
                <textarea type="text" name="address[]" class="md-textarea form-control" rows="3" placeholder="Address"></textarea>
              </div>
              <div class="col-12">
                <input type="hidden" id="address_counter" value="0" /> 
                <button type="button" style="cursor:pointer" class="btn btn-image add-address" title="Add Address"><i class="fa fa-plus" aria-hidden="true"></i>Add Address</button>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Submit</button>
        </div>
      </form>
    </div>

  </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<script>
  var datemonth = $(".datepicker_monthYear").datepicker( {
      format: "mm-yyyy",
      startView: "months", 
      minViewMode: "months"
  });
  $(document).on('click', '.add-work-expariance', function (event) {
    var work_expariance = $('#work_expariance').val();//$(this).data('project_counter');
    var work_expariance = parseInt(work_expariance) + 1;
    $('#work_expariance').val(work_expariance)
    var project_counter = work_expariance;
    var addWork = ' <hr><div class="row"><div class="form-group col-2"><input type="text" name="work_experiance[]" class="form-control validate work_experiance" placeholder="Work Experiance Ex. 1 Year or 2 year"></div><div class="form-group col-2"><input type="text" id="date_from" name="date_from[]" class="form-control validate date_from datepicker_monthYear" placeholder="From"></div><div class="form-group col-2"><input type="text" id="date_to" name="date_to[]" class="form-control validate date_to datepicker_monthYear" placeholder="To"></div><div class="form-group col-2"><input type="text" name="reason_for_leaving[]" class="form-control validate reason_for_leaving" placeholder="Reason for Leaving"></div><div class="form-group col-2"><input type="text" name="designation[]" class="form-control validate designation" placeholder="Designation"></div><div class="form-group col-2"><input type="text" name="organization[]" class="form-control validate organization" placeholder="Organization"></div></div><div class="project_detail"><div class="row"><div class="form-group col-2"><input type="text" name="project'+project_counter+'[]" class="form-control validate project" placeholder="Project"></div><div class="form-group col-2"><input type="text" name="dev_role'+project_counter+'[]" class="form-control validate dev_role" placeholder="Role"></div><div class="form-group col-2"><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools" placeholder="Tools"></div></div><button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="'+project_counter+'"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button></div>';
    $(".work_experiance_model").append(addWork);
    
    
  });
  $(document).on('click', '.add-project', function () {
    var project_counter = $(this).data('project_counter');
    var addProject = '<div class="form-group col-2"><input type="text" name="project'+project_counter+'[]" class="form-control validate project" placeholder="Project"></div><div class="form-group col-2"><input type="text" name="dev_role'+project_counter+'[]" class="form-control validate dev_role" placeholder="Role"></div><div class="form-group col-2"><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools" placeholder="Tools"></div>';
    $(this).parent().find(".row").append(addProject)
  });
  $(document).on('click', '.add-current-assignments', function () {
    //debugger;
    var current_assignments = '<div class="form-group col-4"><input type="text" id="current_assignments" name="current_assignments[]" class="form-control validate" placeholder="Current Assignments"></div><div class="form-group col-4"><input type="text" id="current_assignments_description" name="current_assignments_description[]" class="form-control validate" placeholder="Description"></div><div class="form-group col-4"><input type="text" id="current_assignments_hours_utilisted" name="current_assignments_hours_utilisted[]" class="form-control validate" placeholder="Hours Utilisted"></div>';
    //$(this).parent().find("#current_assigment_div").append(current_assignments)
    $(".current_assigment_div").append(current_assignments)
  });
  
  $(document).on('click', '.add-address', function () {
    var address_counter = $('#address_counter').val();//$(this).data('project_counter');
    var address_counter = parseInt(address_counter) + 1;
    $('#address_counter').val(address_counter)
    var address = '<br/><textarea type="text" name="address[]" class="md-textarea form-control" rows="3" placeholder="Address '+address_counter+'"></textarea>';
    $(".address_div").append(address)
  });
  
/*
  $(document).on('focus',".datepicker_monthYear", function(){ //bind to all instances of class "date". 
    $(".datepicker_monthYear").datepicker( {
        format: "mm-yyyy",
        startView: "months", 
        minViewMode: "months"
    });
  });
  */
  $("#dob").datepicker( {
        format: "dd-MM-yyyy",
        /*startView: "months", 
        minViewMode: "months"*/
    });

</script>