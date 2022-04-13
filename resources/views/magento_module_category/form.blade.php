<div class="row ml-2 mr-2">
    <div class="col-xs-6 col-sm-6">
        <div class="form-group">
            <strong>Module Category :</strong>
            {!! Form::text('category_name', null, ['placeholder' => 'Module Category', 'class' => 'form-control']) !!}
            @if ($errors->has('category_name'))
                <span style="color:red">{{ $errors->first('category_name') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="col-xs-12 col-sm-10 ml-5 text-right">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
