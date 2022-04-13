<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Type :</strong>
            {!! Form::text('magento_module_type', null, ['placeholder' => 'Module Type', 'class' => 'form-control']) !!}
            @if ($errors->has('magento_module_type'))
                <span style="color:red">{{ $errors->first('magento_module_type') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="col-xs-12 col-sm-10 ml-5 text-right">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
