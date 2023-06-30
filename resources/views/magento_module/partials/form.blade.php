<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category :</strong>
            {!! Form::select('module_category_id', $module_categories, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('module_category_id'))
                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Website :</strong>
            {!! Form::select('store_website_id', $store_websites, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('module_category_id'))
                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Name:</strong>
            {!! Form::text('module', null, ['id'=>'module', 'placeholder' => 'Module Name', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('module'))
                <span style="color:red">{{ $errors->first('module') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Current Version:</strong>
            {!! Form::text('current_version', null, ['id'=>'current_version', 'placeholder' => 'Current Version', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('current_version'))
                <span style="color:red">{{ $errors->first('current_version') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type:</strong>
            {!! Form::select('module_type', $magento_module_types, null, ['id'=>'module_type', 'placeholder' => 'Select Module Type', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('module_type'))
                <span style="color:red">{{ $errors->first('module_type') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Payment Status:</strong>
            {!! Form::select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'], null, ['id'=>'payment_status', 'placeholder' => 'Select Payment Status', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('payment_status'))
                <span style="color:red">{{ $errors->first('payment_status') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>Status:</strong>
            {!! Form::select('status', ['Disabled', 'Enable'], null, ['id'=>'status', 'placeholder' => 'Select Status', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('status'))
                <span style="color:red">{{ $errors->first('status') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>API:</strong>
            {!! Form::select('api', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'api', 'placeholder' => 'Select API', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('api'))
                <span style="color:red">{{ $errors->first('api') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="form-group">
            <strong>Cron :</strong>
            {!! Form::select('cron_job', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'cron_job', 'placeholder' => 'Select Cron Job', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('cron_job'))
                <span style="color:red">{{ $errors->first('cron_job') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Javascript/css Require :</strong>
            {!! Form::select('is_js_css', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_js_css', 'placeholder' => 'Select Javascript/css Require', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('is_js_css'))
                <span style="color:red">{{ $errors->first('is_js_css') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party JS Require :</strong>
            {!! Form::select('is_third_party_js', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_js', 'placeholder' => 'Select Third Third Party JS Require ', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('is_third_party_js'))
                <span style="color:red">{{ $errors->first('is_third_party_js') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Sql Query :</strong>
            {!! Form::select('is_sql', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_sql', 'placeholder' => 'Select Sql Query Status', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('is_sql'))
                <span style="color:red">{{ $errors->first('is_sql') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Third Party Plugin :</strong>
            {!! Form::select('is_third_party_plugin', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_plugin', 'placeholder' => 'Select Third Party Plugin', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('is_third_party_plugin'))
                <span style="color:red">{{ $errors->first('is_third_party_plugin') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Developer Name :</strong>
            {!! Form::select('developer_name', $users, null, ['id'=>'developer_name', 'placeholder' => 'Select developer name', 'class' => 'form-control']) !!}
            @if ($errors->has('developer_name'))
                <span style="color:red">{{ $errors->first('developer_name') }}</span>
            @endif
        </div>
    </div>

    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Customized:</strong>
            {!! Form::select('is_customized', ['No', 'Yes'], null, ['id'=>'is_customized', 'placeholder' => 'Customized', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('is_customized'))
                <span style="color:red">{{ $errors->first('is_customized') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Site Impact:</strong>
            {!! Form::select('site_impact', ['No', 'Yes'], null, ['id'=>'site_impact', 'placeholder' => 'Site Impact', 'class' => 'form-control', 'required' => 'required']) !!}
            @if ($errors->has('site_impact'))
                <span style="color:red">{{ $errors->first('site_impact') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-3 col-sm-3">
        <div class="form-group">
            <strong>Review Standard :</strong>
            {!! Form::select('module_review_standard', ['No','Yes'], 'No', [ 'class' => 'form-control']) !!}
            @if ($errors->has('module_review_standard'))
                <span style="color:red">{{ $errors->first('module_review_standard') }}</span>
            @endif
        </div>
    </div>
</div>
<div class="row ml-2 mr-2">
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Description:</strong>
            {!! Form::textarea('module_description', null, ['id'=>'module_description', 'placeholder' => 'Module Description', 'class' => 'form-control', 'required' => 'required', 'rows' => 2, 'cols' => 40]) !!}
            @if ($errors->has('module_description'))
                <span style="color:red">{{ $errors->first('module_description') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module dependency:</strong>
            {!! Form::textarea('dependency', null, ['id'=>'dependency','placeholder' => 'Module Dependency', 'class' => 'form-control', 'rows' => 2, 'cols' => 40]) !!}
            @if ($errors->has('dependency'))
                <span style="color:red">{{ $errors->first('dependency') }}</span>
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="form-group">
            <strong>Module Composer.json File:</strong>
            {!! Form::textarea('composer', null, ['id'=>'composer','placeholder' => 'Module Composer', 'class' => 'form-control', 'rows' => 2, 'cols' => 40]) !!}
            @if ($errors->has('composer'))
                <span style="color:red">{{ $errors->first('composer') }}</span>
            @endif
        </div>
    </div>
    {{-- <div class="col-xs-12 col-sm-10 ml-5 text-right">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div> --}}
</div>
