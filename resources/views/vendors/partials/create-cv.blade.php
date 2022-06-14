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
            <label data-error="wrong" data-success="right" for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" class="form-control validate">
          </div>
          <div class="form-group col-3">
            <label data-error="wrong" data-success="right" for="second_name">Second name</label>
            <input type="text" id="second_name" name="second_name" class="form-control validate">
          </div>

          <div class="form-group  col-3">
            <label data-error="wrong" data-success="right" for="email">Your email</label>
            <input type="email" id="email" name="email" class="form-control validate">
          </div>

          <div class="form-group col-3">
            <label data-error="wrong" data-success="right" for="mobile">Mobile No.</label>
            <input type="text" id="mobile" name="mobile" class="form-control validate">
          </div>

          <div class="form-group col-12">
            <label data-error="wrong" data-success="right" for="career_objective">Career Objective</label>
            <textarea type="text" id="career_objective" name="career_objective" class="md-textarea form-control" rows="4"></textarea>
          </div>
          <div class="col work_experiance_model">
            <hr>
            <div class="row">
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="work_experiance">Work Experiance </label>
                  <input type="text" name="work_experiance[]" class="form-control validate work_experiance">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="date_from">From </label>
                  <input type="date" name="date_from0[]" class="form-control validate date_from">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="date_to">To </label>
                  <input type="date" name="date_to0[]" class="form-control validate date_to">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="reason_for_leaving">Reason for Leaving </label>
                  <input type="text" name="reason_for_leaving0[]" class="form-control validate reason_for_leaving">
                </div>

                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="designation">Designation </label>
                  <input type="text"  name="designation0[]" class="form-control validate designation">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="organization">Organization </label>
                  <input type="text"  name="organization0[]" class="form-control validate organization">
                </div>
                
            </div>
            <div class="project_detail">
              <div  class=" col-6">
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="project">Project </label>
                  <input type="text" name="project0[]" class="form-control validate project">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="dev_role">Role </label>
                  <input type="text"  name="dev_role0[]" class="form-control validate dev_role">
                </div>
                <div class="form-group col-2">
                  <label data-error="wrong" data-success="right" for="tools">Tools </label>
                  <input type="text"name="tools0[]" class="form-control validate tools">
                </div>
                <button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button>
              </div>
            </div>
          </div>
          <div class="form-group col-12">
            <input type="hidden" id="work_expariance" value="0" /> 
            <button type="button" style="cursor:pointer" class="btn btn-image add-work-expariance" title="Add Work Expirenace" data-work_expariance="0"><i class="fa fa-plus" aria-hidden="true"></i>Add Work Expirenace</button>
          </div>
          <div class="form-group col-12">
            <label data-error="wrong" data-success="right" for="name">Software experts</label>
            <div class="form-group col-sm">
              <label data-error="wrong" data-success="right" for="soft_framework">Framework</label>
              <input type="text" id="soft_framework" name="soft_framework" class="form-control validate">
            </div>
            <div class="form-group col-sm">
              <label data-error="wrong" data-success="right" for="soft_description">Description</label>
              <textarea id="soft_description" name="soft_description" class="form-control"></textarea>
            </div>
            <div class="form-group col-sm">
              <label data-error="wrong" data-success="right" for="soft_experience">Experience</label>
              <input type="text" id="soft_experience" name="soft_experience" class="form-control validate">
            </div>
            <div class="form-group col-sm">
              <label data-error="wrong" data-success="right" for="soft_remark">Remark</label>
              <input type="text" id="soft_remark" name="soft_remark" class="form-control validate">
            </div>
          </div>
          <div  class="form-group row mx-auto">
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="father_name">Father Name</label>
                <input type="text" id="father_name" name="father_name" class="form-control validate">
              </div>
    
              <div class="form-group  col-6">
                <label data-error="wrong" data-success="right" for="dob">Date of Birth</label>
                <input type="text" id="dob" name="dob" class="form-control validate">
              </div>
    
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="gender">Gender</label>
                <table>
                  <tr>
                    <td><input type="radio"  name="gender" class=""></td><td>&nbsp;Male</td> 
                    <td>&nbsp;</td><td>&nbsp;</td> 
                    <td><input type="radio"  name="gender" class="validate"></td><td>&nbsp;Female</td>
                  </tr>
              </table>
                
              </div>
    
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="marital_status">Marital Status</label>
                <input type="text" id="marital_status" name="marital_status" class="md-textarea form-control" />
              </div>
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="langauge_know">Langauge Know</label>
                <input type="text" id="langauge_know" name="langauge_know" class="form-control" />
              </div>
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="hobbies">Hobbies</label>
                <input type="text" id="hobbies" name="hobbies" class="form-control" />
              </div>
              <div class="form-group col-6">
                <label data-error="wrong" data-success="right" for="address">Address</label>
                <textarea type="text" id="address" class="md-textarea form-control" rows="4"></textarea>
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


<script>
  $(document).on('click', '.add-work-expariance', function () {
    var work_expariance = $('#work_expariance').val();//$(this).data('project_counter');
    var work_expariance = parseInt(work_expariance) + 1;
    $('#work_expariance').val(work_expariance)
    var project_counter = work_expariance;
    var addWork = ' <hr><div class="row"><div class="form-group col-3"><label data-error="wrong" data-success="right" for="work_experiance">Work Experiance </label><input type="text" name="work_experiance[]" class="form-control validate work_experiance"></div><div class="form-group col-3"><label data-error="wrong" data-success="right" for="data_from">From </label><input type="date" name="data_from'+project_counter+'[]" class="form-control validate data_from"></div><div class="form-group col-3"><label data-error="wrong" data-success="right" for="date_to">To </label><input type="date" name="date_to'+project_counter+'[]" class="form-control validate date_to"></div> <div class="form-group col-3"><label data-error="wrong" data-success="right" for="reason_for_leaving">Reason for Leaving</label><input type="text" name="reason_for_leaving'+project_counter+'[]" class="form-control validate reason_for_leaving"></div> <div class="form-group col-3"> <label data-error="wrong" data-success="right" for="destination">Designation </label> <input type="text"  name="designation'+project_counter+'[]" class="form-control validate designation"></div><div class="form-group col-3"><label data-error="wrong" data-success="right" for="organization">Organization </label> <input type="text"  name="organization'+project_counter+'[]" class="form-control validate organization"></div> <div class="form-group project_detail"> <div class="form-group col-3"><label data-error="wrong" data-success="right" for="project">Project </label><input type="text" name="project'+project_counter+'[]" class="form-control validate project"></div> <div class="form-group col-3"><label data-error="wrong" data-success="right" for="dev_role">Role </label><input type="text"  name="dev_role'+project_counter+'[]" class="form-control validate dev_role"></div> <div class="form-group col-3"><label data-error="wrong" data-success="right" for="tools">Tools </label><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools"></div></div><div class="form-group col-3"><div class="project_detail"></div><button type="button" style="cursor:pointer" class="btn btn-image add-project" title="Add Project" data-project_counter="'+project_counter+'"><i class="fa fa-plus" aria-hidden="true"></i>Add Project</button></div></div>';
    $(".work_experiance_model").append(addWork);
  });
  $(document).on('click', '.add-project', function () {
    var project_counter = $(this).data('project_counter');
    var addProject = '<div class="form-group "><div class="form-group col-12"><label data-error="wrong" data-success="right" for="project">Project </label><input type="text" name="project'+project_counter+'[]" class="form-control validate project"></div><div class="form-group col-12"><label data-error="wrong" data-success="right" for="dev_role">Role </label><input type="text" name="dev_role'+project_counter+'[]" class="form-control validate dev_role"></div><div class="form-group col-12"><label data-error="wrong" data-success="right" for="tools">Tools </label><input type="text"name="tools'+project_counter+'[]" class="form-control validate tools"></div></div>';
    $(this).find("project_detail").append(addProject)
    console.log($(this).find("project_detail").append(addProject));
    //$(this).parent().find(".project_detail").append(addProject);
      //$(this).find(".project_detail").append(addProject);
  });
</script>