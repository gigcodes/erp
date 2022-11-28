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

                <div class="form-group {{ $errors->has('summary') ? 'has-error' : '' }}">
                    <label> Summary </label>
                    <input class="form-control summary" name="summary" type="text">
                    <input class="form-control id" name="id" type="hidden">
                    <span class="text-danger">{{ $errors->first('summary') }}</span>
                </div>

                <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                    <label> Step To Reproduce </label>
                    <input class="form-control step_to_reproduce" name="step_to_reproduce" type="text">
                    <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
                </div>

                <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                    <label> ScreenShot/ Video Url </label>
                    <input class="form-control url" name="url" type="text">
                    <span class="text-danger">{{ $errors->first('url') }}</span>
                </div>

                <div class="form-group" {{ $errors->has('bug_type_id') ? 'has-error' : '' }}>
                    <label> Type of Bug </label>
                    <select class="form-control bug_type_id" name="bug_type_id">
                        <option value="">Select Type of Bug</option>
                        @foreach($bugTypes as  $bugType)
                            <option value="{{$bugType->id}}">{{$bugType->name}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment </label>
                    <select class="form-control bug_environment_id" name="bug_environment_id">
                        <option value="">Select Environment</option>
                        @foreach($bugEnvironments as  $bugEnvironment)
                            <option value="{{$bugEnvironment->id}}">{{$bugEnvironment->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                    <label> Assign To </label>
                    <select class="form-control assign_to" name="assign_to">
                        <option value="">Select Assign To</option>
                        @foreach($users as  $user)
                            <option value="{{$user->id}}">{{$user->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" {{ $errors->has('bug_severity_id') ? 'has-error' : '' }}>
                    <label> Severity </label>
                    <select class="form-control bug_severity_id" name="bug_severity_id">
                        <option value="">Select Severity</option>
                        @foreach($bugSeveritys as  $bugSeverity)
                            <option value="{{$bugSeverity->id}}">{{$bugSeverity->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                    <label> Status </label>
                    <select class="form-control bug_status_id" name="bug_status_id">
                        <option value="">Select Status</option>
                        @foreach($bugStatuses as  $bugStatus)
                            <option value="{{$bugStatus->id}}">{{$bugStatus->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                    <label> Module/Feature </label>
                    <select class="form-control module_id" name="module_id">
                        <option value="">Select Module/Feature</option>
                        @foreach($filterCategories as  $filterCategory)
                            <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                    <label> Remark </label>
                    <textarea class="form-control remark"  name="remark"></textarea>
                    <span class="text-danger">{{ $errors->first('remark') }}</span>
                </div>
                <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                    <label> Website </label>
                    <select class="form-control website" name="website">
                        <option value="">Select Module/Feature</option>
                        @foreach($filterWebsites as  $filterWebsite)
                            <option value="{{$filterWebsite}}">{{$filterWebsite}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

