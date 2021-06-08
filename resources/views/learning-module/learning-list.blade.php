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
    <td class="communication-td">
        <!-- class="expand-row" -->
      
       
        <input type="text" class="form-control send-message-textbox" data-id="{{$learning->id}}" id="send_message_{{$learning->id}}" name="send_message_{{$learning->id}}" style="margin-bottom:5px;width:40%;display:inline;"/>
       
        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$learning->id}}" ><img src="/images/filled-sent.png"/></button>
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $learning->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
        {{-- <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? '' : '' }} justify-content-between expand-row-msg" style="word-break: break-all;" data-id="{{$learning->id}}">
        <span class="td-mini-container-{{$learning->id}}" style="margin:0px;">
                        {{  \Illuminate\Support\Str::limit($issue->message, 25, $end='...') }}
        </span> --}}
    </span>
    <div class="expand-row-msg" data-id="{{$learning->id}}">
        <span class="td-full-container-{{$learning->id}} hidden">
            {{-- {{ $issue->message }} --}}
            <br>
            <div class="td-full-container">
                <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $learning->id }})">Send Attachment</button>
                <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$learning->id}})">Send Images</button>
                <input id="file-input{{ $learning->id }}" type="file" name="files" style="display: none;" multiple/>
            </div> 
        </span>
    </div>
        </td>
    {{-- <td><div style="display: flex"><input type="text" class="form-control send-message-textbox"> <img src="/images/filled-sent.png" style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td> --}}
</tr>