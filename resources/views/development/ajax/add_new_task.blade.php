<!-- Modal -->
<div id="newTaskModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div  style="padding: 10px;border-bottom: 1px solid #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Development Task</h4>
            </div>
            <form action="{{ route('development.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    @if(auth()->user()->checkPermission('development-list'))
                        <div class="form-group">
                            <strong>User:</strong>
                            <select class="form-control" name="user_id" required>
                                @foreach ($users as $key => $obj)
                                    <option value="{{ $obj->id }}" {{ old('user_id') == $obj->id ? 'selected' : '' }}>{{ $obj->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('user_id'))
                                <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
                            @endif
                        </div>
                    @endif

                    <div class="form-group">
                        <strong>Attach files:</strong>
                        <input type="file" name="images[]" class="form-control" multiple>
                        @if ($errors->has('images'))
                            <div class="alert alert-danger">{{$errors->first('images')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="module_id">Module:</label>
                        <br>
                        <select class="form-control select2" id="module_id" name="module_id">
                            <option value>Select a Module</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}" {{ $module->id == old('module_id') ? 'selected' : '' }}>{{ $module->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('module_id'))
                            <div class="alert alert-danger">{{$errors->first('module_id')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select class="form-control" name="priority" id="priority" required>
                            <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>Normal</option>
                            <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Urgent</option>
                            <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Critical</option>
                        </select>

                        @if ($errors->has('priority'))
                            <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="priority">Type:</label>
                        <select class="form-control" name="task_type_id" id="task_type_id" required>
                            @foreach($tasksTypes as $taskType)
                                <option value="{{$taskType->id}}">{{$taskType->name}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('priority'))
                            <div class="alert alert-danger">{{$errors->first('priority')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Subject:</strong>
                        <input type="text" class="form-control" name="subject" value="{{ old('subject') }}"/>
                        </select>

                        @if ($errors->has('subject'))
                            <div class="alert alert-danger">{{$errors->first('subject')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Task:</strong>
                        <textarea class="form-control" name="task" rows="8" cols="80" required>{{ old('task') }}</textarea>
                        </select>

                        @if ($errors->has('task'))
                            <div class="alert alert-danger">{{$errors->first('task')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Cost:</strong>
                        <input type="number" class="form-control" name="cost" value="{{ old('cost') }}"/>
                        </select>

                        @if ($errors->has('cost'))
                            <div class="alert alert-danger">{{$errors->first('cost')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Status:</strong>
                        <select class="form-control" name="status" required>
                            <option value="Planned" {{ old('status') == 'Planned' ? 'selected' : '' }}>Planned</option>
                            <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Done" {{ old('status') == 'Done' ? 'selected' : '' }}>Done</option>
                        </select>

                        @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
            </form>
        </div>

    </div>
</div>