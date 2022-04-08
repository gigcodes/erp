<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category :</strong>
            {!! Form::select('module_category_id', $module_categories, null, ['placeholder' => 'Select Module Category', 'class' => 'form-control']) !!}
            @if ($errors->has('module_category_id'))
                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Name:</strong>
            {!! Form::text('module', null, ['placeholder' => 'Module Name', 'class' => 'form-control']) !!}
            @if ($errors->has('module'))
                <span style="color:red">{{ $errors->first('module') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Current Version:</strong>
            {!! Form::text('current_version', null, ['placeholder' => 'Current Version', 'class' => 'form-control']) !!}
            @if ($errors->has('current_version'))
                <span style="color:red">{{ $errors->first('current_version') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type:</strong>
            {!! Form::select('module_type', ['3rd Party' => '3rd Party', 'Custom' => 'Custom'], null, ['placeholder' => 'Select Module Type', 'class' => 'form-control']) !!}
            @if ($errors->has('module_type'))
                <span style="color:red">{{ $errors->first('module_type') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Payment Status:</strong>
            {!! Form::select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'], null, ['placeholder' => 'Select Payment Status', 'class' => 'form-control']) !!}
            @if ($errors->has('payment_status'))
                <span style="color:red">{{ $errors->first('payment_status') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Status:</strong>
            {!! Form::select('status', ['Disabled', 'Enable'], null, ['placeholder' => 'Select Status', 'class' => 'form-control']) !!}
            @if ($errors->has('status'))
                <span style="color:red">{{ $errors->first('status') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Developer Name:</strong>
            {!! Form::text('developer_name', null, ['placeholder' => 'Developer Name', 'class' => 'form-control']) !!}
            @if ($errors->has('developer_name'))
                <span style="color:red">{{ $errors->first('developer_name') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Customized:</strong>
            {!! Form::select('is_customized', ['No', 'Yes'], null, ['placeholder' => 'Customized', 'class' => 'form-control']) !!}
            @if ($errors->has('is_customized'))
                <span style="color:red">{{ $errors->first('is_customized') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Description:</strong>
            {!! Form::textarea('module_description', null, ['placeholder' => 'Module Description', 'class' => 'form-control', 'rows' => 2, 'cols' => 40]) !!}
            @if ($errors->has('module_description'))
                <span style="color:red">{{ $errors->first('module_description') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-12 col-sm-10 ml-5 text-right">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
