<div id="bugtrackingCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Add Bug Tracking</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {!! Form::open(['route'=> ['bug-tracking.store' ]  ]) !!}

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="form-group {{ $errors->has('summary') ? 'has-error' : '' }}">
                <label> Summary </label>
                  <textarea class="form-control" id="summary" name="summary"></textarea>
                <span class="text-danger">{{ $errors->first('summary') }}</span>
            </div>

            <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                <label> Step To Reproduce </label>                
				<textarea class="form-control" id="step_to_reproduce" name="step_to_reproduce"></textarea>
                <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
            </div>

            {{-- <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                <label> ScreenShot/ Video Url </label>
                <input class="form-control" id="url_bug" name="url" type="text">
                <span class="text-danger"></span>
            </div> --}}

            <div class="form-group" {{ $errors->has('bug_type_id') ? 'has-error' : '' }}>
                <label> Type of Bug </label>
                <select class="form-control" id="bug_type_id_bug" name="bug_type_id">
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
                    <select class="form-control" id="bug_environment_id_bug" name="bug_environment_id">
                        <option value="">Select Environment</option>
                        @foreach($bugEnvironments as  $bugEnvironment)
                            <option value="{{$bugEnvironment->id}}">{{$bugEnvironment->name}} </option>
                        @endforeach
                    </select>
					<span class="text-danger"></span>
                </div>

                <div class="col-md-6"  style="padding-right: 0px !important;"  {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment Version </label>
                    <input class="form-control" id="bug_environment_ver_bug" name="bug_environment_ver" type="text">
                    <span class="text-danger">{{ $errors->first('bug_environment_ver') }}</span>
                </div>
                
            </div>
           
            <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                <label> Assign To </label>
                <select class="form-control" id="assign_to_bug" name="assign_to">
                    <option value="">Select Assign To</option>
                    @foreach($users as  $user)
                        <option value="{{$user->id}}">{{$user->name}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('bug_severity_id') ? 'has-error' : '' }}>
                <label> Severity </label>
                <select class="form-control" id="bug_severity_id_bug" name="bug_severity_id">
                    <option value="">Select Severity</option>
                    @foreach($bugSeveritys as  $bugSeverity)
                        <option value="{{$bugSeverity->id}}">{{$bugSeverity->name}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                <label> Status </label>
                <select class="form-control" id="bug_status_id_bug" name="bug_status_id">
                    <option value="">Select Status</option>
                    @foreach($bugStatuses as  $bugStatus)
                        <option value="{{$bugStatus->id}}">{{$bugStatus->name}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                <label> Module/Feature </label>
                <select class="form-control" id="module_id_bug" name="module_id">
                    <option value="">Select Module/Feature</option>
                    @foreach($filterCategories as  $filterCategory)
                        <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>

            <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                <label> Remark </label>
                <textarea class="form-control" id="remark_bug" name="remark"></textarea>
                <span class="text-danger">{{ $errors->first('remark') }}</span>
				<span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                <label> Website </label>
                <select class="form-control" id="website_bug" name="website">
                    <option value="">Select Website</option>
                    @foreach($filterWebsites as  $filterWebsite)
                        <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                    @endforeach
                </select>
				<span class="text-danger"></span>
            </div>
			<div class="form-group  {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                <label> Reference Bug ID </label>
                 <input class="form-control" name="parent_id" id="parent_id_bug" type="text">
                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div> 
            <div class="form-group">
                <button type="submit" class="btn btn-secondary btn-save-bug">Store</button>
            </div>
            {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-save-bug', function() {
	$('.text-danger').html('');
	if($('#summary').val() == '') {
		$('#summary').next().text("Please enter the summary");
		return false;
	}
	if($('#step_to_reproduce').val() == '') {
		$('#step_to_reproduce').next().text("Please enter the steps");
		return false;
	}
	
	if($('#url_bug').val() == '') {
		$('#url_bug').next().text("Please enter the url");
		
		return false;
	}
	if($('#bug_type_id_bug').val() == ''  || $('#bug_type_id_bug').val() == null  || $('#bug_type_id_bug').val() == 'null') {
		$('#bug_type_id_bug').next().text("Please enter the type of bug");
		return false;
	}
	
	if($('#bug_environment_id_bug').val() == ''  || $('#bug_environment_id_bug').val() == null || $('#bug_environment_id_bug').val() == 'null') {
		$('#bug_environment_id_bug').next().text("Please enter the environment");
		return false;
	}
	
	if($('#assign_to_bug').val() == ''  || $('#assign_to_bug').val() == null || $('#assign_to_bug').val() == 'null') {
		$('#assign_to_bug').next().text("Please enter the assign to");
		return false;
	}
	if($('#bug_severity_id_bug').val() == ''  || $('#bug_severity_id_bug').val() == null  || $('#bug_severity_id_bug').val() == 'null') {
		$('#bug_severity_id_bug').next().text("Please enter the severity");
		return false;
	}
	if($('#bug_status_id_bug').val() == ''  || $('#bug_status_id_bug').val() == null || $('#bug_status_id_bug').val() == 'null') {
		$('#bug_status_id_bug').next().text("Please enter the status");
		return false;
	}
	if($('#module_id_bug').val() == ''  || $('#module_id_bug').val() == null || $('#module_id_bug').val() == 'null') {
		$('#module_id_bug').next().text("Please enter the module");
		return false;
	}
	if($('#remark_bug').val() == '') {
		$('#remark_bug').next().text("Please enter the remark");
		return false;
	}
	if($('#website_bug').val() == ''   || $('#website_bug').val() == null || $('#website_bug').val() == 'null') {
		$('#website_bug').next().text("Please enter the website");
		return false;
	}
	return true;

});

</script>