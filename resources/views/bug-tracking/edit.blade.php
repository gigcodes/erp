<div id="bugtrackingEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Edit Bug Tracking</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route'=> ['bug-tracking.update' ]  ]) !!}

                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif

                <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                    <label> Module/Feature </label>
                    <select class="form-control module_id" id="module_id_update"  name="module_id">
                        <option value="">Select Module/Feature</option>
                        @foreach($filterCategories as  $filterCategory)
                            <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>

                <div class="form-group" {{ $errors->has('test_case_id') ? 'has-error' : '' }}>
                    <label> Test Case </label>
                    <select class="form-control" id="test_case_bug_edit" name="test_case_id">
                        <option value="">Select Test Case</option>
                    </select>
                    <span class="text-danger"></span>
    
                </div>

                <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                    <label> Step To Reproduce </label>                    
					<textarea class="form-control step_to_reproduce" id="step_to_reproduce_update" name="step_to_reproduce"></textarea>
                    <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
                </div>

                <div class="form-group {{ $errors->has('expected_result') ? 'has-error' : '' }}">
                    <label> Expected Result </label>                
                    <textarea class="form-control" id="expected_result_update" name="expected_result"></textarea>
                    <span class="text-danger">{{ $errors->first('expected_result') }}</span>
                </div>


                <div class="form-group {{ $errors->has('summary') ? 'has-error' : '' }}">
                    <label> Summary </label>
					<input class="form-control id" name="id" type="hidden">
                    <textarea class="form-control summary" id="summary_update" name="summary"></textarea>                    
                    <span class="text-danger">{{ $errors->first('summary') }}</span>
                </div>


                <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                    <label> Website </label>
                    <select class="form-control website select-multiple" id="website_update" name="website[]" multiple>
                        <option value="">Select Website</option>
                        @foreach($filterWebsites as  $filterWebsite)
                            <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>

                

                {{-- <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                    <label> ScreenShot/ Video Url </label>
                    <input class="form-control url" id="url_update" name="url" type="text">
                    <span class="text-danger">{{ $errors->first('url') }}</span>
                </div> --}}

                <div class="form-group" {{ $errors->has('bug_type_id') ? 'has-error' : '' }}>
                    <label> Type of Bug </label>
                    <select class="form-control bug_type_id" id="bug_type_id_update" name="bug_type_id">
                        <option value="">Select Type of Bug</option>
                        @foreach($bugTypes as  $bugType)
                            <option value="{{$bugType->id}}">{{$bugType->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>
				 <div class="form-group" style="padding-bottom: 58px !important;">
                    <div class="col-md-6" style="padding-left: 0px !important;" {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                        <label> Environment </label>
                        <select class="form-control bug_environment_id" id="bug_environment_id_update" name="bug_environment_id">
                            <option value="">Select Environment</option>
                            @foreach($bugEnvironments as  $bugEnvironment)
                                <option value="{{$bugEnvironment->id}}">{{$bugEnvironment->name}} </option>
                            @endforeach
                        </select>
						<span class="text-danger"></span>
                    </div>

                    <div class="col-md-6"  style="padding-right: 0px !important;"  {{ $errors->has('bug_environment_ver') ? 'has-error' : '' }}>
                        <label> Environment Version </label>
                        <input class="form-control bug_environment_ver" name="bug_environment_ver" type="text">
                        <span class="text-danger">{{ $errors->first('bug_environment_ver') }}</span>
																	  
																									   
								   
                    </div>
                    
                </div>

                <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                    <label> Assign To </label>
                    <select class="form-control assign_to" id="assign_to_update" name="assign_to">
                        <option value="">Select Assign To</option>
                        @foreach($users as  $user)
                            <option value="{{$user->id}}">{{$user->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>
                <div class="form-group" {{ $errors->has('bug_severity_id') ? 'has-error' : '' }}>
                    <label> Severity </label>
                    <select class="form-control bug_severity_id" id="bug_severity_id_update"  name="bug_severity_id">
                        <option value="">Select Severity</option>
                        @foreach($bugSeveritys as  $bugSeverity)
                            <option value="{{$bugSeverity->id}}">{{$bugSeverity->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>
                <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                    <label> Status </label>
                    <select class="form-control bug_status_id" id="bug_status_id_update" name="bug_status_id">
                        <option value="">Select Status</option>
                        @foreach($bugStatuses as  $bugStatus)
                            <option value="{{$bugStatus->id}}">{{$bugStatus->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>
                
                <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                    <label> Remark </label>
                    <textarea class="form-control remark" id="remark_update" name="remark"></textarea>
                    <span class="text-danger">{{ $errors->first('remark') }}</span>
					<span class="text-danger"></span>
                </div>
                
				<div class="form-group  {{ $errors->has('parent_id') ? 'has-error' : '' }}">
					<label> Reference Bug ID </label>
					 <input class="form-control parent_id" name="parent_id" id="parent_id_update" type="text">
					<span class="text-danger">{{ $errors->first('parent_id') }}</span>
				</div>


                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-update-bug">Store</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){

    

$("#module_id_update").on('change',function(){
    let module_id = $(this).val();
    let test_case_url = "{{route('test-cases.bymodule',':id')}}";
    let ajax_url = test_case_url.replace(':id',module_id);

    $('#test_case_bug_edit').html('<option value="">Select Test Case</option>');
    


    if(module_id) {
        $.ajax({
            url: ajax_url,
            beforeSend: function() {
                $("#loading-image").show();
            },
            datatype: "json"
        }).done(function(response) {
            $("#loading-image").hide();
            //test_case_bug
            let test_cases = response.testCases;
            
            if(test_cases.length) {
                
                $.each(test_cases, function(key, test_case) {   
                    $('#test_case_bug_edit')
                        .append($("<option></option>")
                                    .attr("value", test_case.id)
                                    .text(test_case.name)); 
                });
            } else {
                
            }
            //$this.siblings('input').val("");				
            
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });

    }
});

});

$("#test_case_bug_edit").on('change',function(){

let test_case_id = $(this).val();
let test_case_url = "{{route('test-cases.show',':id')}}";
let ajax_url = test_case_url.replace(':id',test_case_id);

$('#step_to_reproduce_update').val('');
$('#expected_result_update').val('');
// $('#step_to_reproduce').attr('readonly',false);


if(test_case_id) {
    $.ajax({
        url: ajax_url,
        beforeSend: function() {
            $("#loading-image").show();
        },
        datatype: "json"
    }).done(function(response) {
        $("#loading-image").hide();
        //test_case_bug
        let test_cases = response.testCase;
        if(test_cases) {
            // $('#step_to_reproduce').attr('readonly',true);
            $('#step_to_reproduce_update').val(test_cases.step_to_reproduce);

            
            $('#expected_result_update').val(test_cases.expected_result);
            
            
        } else {
            // $('#step_to_reproduce').attr('readonly',false);
        }
        //$this.siblings('input').val("");				
        
    }).fail(function(jqXHR, ajaxOptions, thrownError) {
        toastr["error"]("Oops,something went wrong");
        $("#loading-image").hide();
    });

}


});
$(document).on('click', '.btn-update-bug', function() {
	$('.text-danger').html('');
	
	if($('#summary_update').val() == '') {
		$('#summary_update').next().text("Please enter the summary");
		return false;
	}
	if($('#step_to_reproduce_update').val() == '') {
		$('#step_to_reproduce_update').next().text("Please enter the steps");
		return false;
	}
	
	if($('#url_update').val() == '') {
		$('#url_update').next().text("Please enter the url");
		
		return false;
	}
	if($('#bug_type_id_update').val() == ''  || $('#bug_type_id_update').val() == null  || $('#bug_type_id_update').val() == 'null') {
		$('#bug_type_id_update').next().text("Please enter the type of bug");
		return false;
	}
	
	if($('#bug_environment_id_update').val() == ''  || $('#bug_environment_id_update').val() == null  || $('#bug_environment_id_update').val() == 'null') {
		$('#bug_environment_id_update').next().text("Please enter the environment");
		return false;
	}
	
	if($('#assign_to_update').val() == ''  || $('#assign_to_update').val() == null   || $('#assign_to_update').val() == 'null') {
		
		$('#assign_to_update').next().text("Please enter the assign to");
		return false;
	}
	if($('#bug_severity_id_update').val() == '') {
		$('#bug_severity_id_update').next().text("Please enter the severity");
		return false;
	}
	
	if($('#bug_status_id_update').val() == ''  || $('#bug_status_id_update').val() == null  || $('#bug_status_id_update').val() == 'null') {
		$('#bug_status_id_update').next().text("Please enter the status");
		return false;
	}
	
	if($('#module_id_update').val() == ''  || $('#bug_status_id_update').val() == null  || $('#bug_status_id_update').val() == 'null') {
		$('#module_id_update').next().text("Please enter the module");
		return false;
	}
	if($('#remark_update').val() == '') {
		$('#remark_update').next().text("Please enter the remark");
		return false;
	}
	if($('#website_update').val() == ''  || $('#website_update').val() == null  || $('#website_update').val() == 'null') {
		$('#website_update').next().text("Please enter the website");
		return false;
	}
	return true;

});

</script>