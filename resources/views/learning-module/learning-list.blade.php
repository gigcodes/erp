{{-- @php
    $user = App\User::find($learning->learning_user);
    $provider = App\User::find($learning->learning_vendor);
    $module = App\LearningModule::find($learning->learning_module);
    $submodule = App\LearningModule::find($learning->learning_submodule);
    $assignment = App\Contact::find($learning->learning_assignment);
    $status = App\TaskStatus::find($learning->learning_status);
@endphp --}}
<tr class="learning_and_activity" data-id="{{ $learning->id }}">
    <td>{{ $learning->id }}</td>
    <td>{{ $learning->created_at->format('m/d/Y') }}</td>
    <td>
        <select class="form-control updateUser" name="user">
            @foreach(App\User::all() as $user)
                <option value="{{ $user->id }}" {{ $learning->learning_user == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-control updateProvider" name="provider">
            @foreach(App\User::all() as $provider)
                <option value="{{ $provider->id }}" {{ $learning->learning_vendor == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
            @endforeach
        </select>
    </td>
    <td><div style="display: flex"><input type="text" class="form-control send-message-textbox" name="learning_subject" value="{{ $learning->learning_subject }}"> <img src="/images/filled-sent.png" class="updateSubject"style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td>
    <td>
        <select class="form-control updateModule" name="module">
            @foreach(App\LearningModule::where('parent_id',0)->get() as $module)
                <option value="{{ $module->id }}" {{ $learning->learning_module == $module->id ? 'selected' : '' }}>{{ $module->title }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-control updateSubmodule" name="submodule">
            <option value="">Select</option>
            @foreach(App\LearningModule::where('parent_id',$learning->learning_module)->get() as $submodule)
                <option class="submodule" value="{{ $submodule->id }}" {{ $learning->learning_submodule == $submodule->id ? 'selected' : '' }}>{{ $submodule->title }}</option>
            @endforeach
        </select>
    </td>
    <td><div style="display: flex"><input type="text" class="form-control send-message-textbox" name="learning_assignment" value="{{ $learning->learning_assignment }}" maxlength="15"> <img src="/images/filled-sent.png" class="updateAssignment" style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td>
    <td>{{ $learning->learning_duedate }}</td>
    <td>
        <select class="form-control updateStatus" name="status">
            @foreach(App\TaskStatus::all() as $status)
                <option value="{{ $status->id }}" {{ $learning->learning_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
            @endforeach
        </select>
    </td>
    <td><div style="display: flex"><input type="text" class="form-control send-message-textbox"> <img src="/images/filled-sent.png" style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td>
</tr>