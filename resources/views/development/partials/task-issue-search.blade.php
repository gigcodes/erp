<form action="{{ url("development/list/$title") }}" method="get">
    <div class="row">
        @if(auth()->user()->isReviwerLikeAdmin())
            <div class="col-md-1">
                <select class="form-control" name="assigned_to" id="assigned_to">
                    <option value="">Assigned To</option>
                    @foreach($users as $id=>$user)
                        <option {{$request->get('assigned_to')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        {{--
        <div class="col-md-1">
            <select class="form-control" name="responsible_user" id="responsible_user">
                <option value="">Responsible User...</option>
                @foreach($users as $id=>$user)
                    <option {{$request->get('responsible_user')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <select class="form-control" name="corrected_by" id="corrected_by">
                <option value="">Correction by</option>
                @foreach($users as $id=>$user)
                    <option {{$request->get('corrected_by')==$id ? 'selected' : ''}} value="{{$id}}">{{ $user }}</option>
                @endforeach
            </select>
        </div>
        --}}
        <div class="col-md-1">
            <select name="module" id="module_id" class="form-control">
                <option value="">Module</option>
                @foreach($modules as $module)
                    <option {{ $request->get('module') == $module->id ? 'selected' : '' }} value="{{ $module->id }}">{{ $module->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control" value="{{ (!empty(app('request')->input('subject'))  ? app('request')->input('subject') : '') }}">
        </div>
        <div class="col-md-2">
            <?php echo Form::select("language",["" => "N/A"] + $languages, app('request')->input('language') , ["class" => "form-control select2", "id" => "language_query"]) ?>
        </div>
        <div class="col-md-2">
            <?php echo Form::select("task_status[]",$statusList,request()->get('task_status', []),["class" => "form-control multiselect","multiple" => true]); ?>
        </div>
        <div class="col-md-1">
            <select name="order" id="order_query" class="form-control">
                <option {{$request->get('order')== "" ? 'selected' : ''}} value="create">Order by date descending</option>
                <option {{$request->get('order')== "priority" ? 'selected' : ''}} value="">Order by priority</option>
                <option {{$request->get('order')== "create_asc" ? 'selected' : ''}} value="create">Order by date</option>
                <option {{$request->get('order')== "communication_desc" ? 'selected' : ''}} value="communication_desc">Order by Communication</option>
            </select>
        </div>
        <div class="col-md-1">
            {{--
            @if ( isset($_REQUEST['show_resolved']) && $_REQUEST['show_resolved'] == 1 )
                <input type="checkbox" name="show_resolved" value="1" checked> incl.resolved
            @else
                <input type="checkbox" name="show_resolved" value="1"> incl.resolved
            @endif
             --}}
            <button class="btn btn-image">
                <img src="{{ asset('images/search.png') }}" alt="Search">
            </button>
        </div>
       
        <div class="col-md-1">
            <a class="btn btn-secondary d-inline priority_model_btn">Priority</a>
            <!-- <a class="btn btn-secondary d-inline priority_model_head_dev_btn">Priority Bjorn</a> -->
        </div>
    </div>
</form>