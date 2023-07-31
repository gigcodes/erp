<div class="col-sm-12">
    <div class="form-group">
        <label>location</label>
        {!! Form::text('location', null, ['id'=>'location', 'placeholder' => 'Magento Frontend location', 'class' => 'form-control location', 'required' => 'required']) !!}

    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Admin Configuration</label>
        {!! Form::text('admin_configuration', null, ['id'=>'admin_configuration', 'placeholder' => 'Magento Admin Configuration', 'class' => 'form-control admin_configuration']) !!}
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Frontend configuration</label>
        {!! Form::text('frontend_configuration', null, ['id'=>'frontend_configuration', 'placeholder' => 'Magento Frontend configuration', 'class' => 'form-control frontend_configuration']) !!}                 
    </div>
</div>