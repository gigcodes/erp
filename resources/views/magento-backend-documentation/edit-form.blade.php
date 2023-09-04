<div class="col-sm-12">
    <div class="form-group">
        <label>Feature</label>
        {!! Form::text('features', null, ['id'=>'features', 'placeholder' => 'Feature', 'class' => 'form-control features', 'required' => 'required']) !!}

    </div>
</div>
{{-- <div class="col-sm-12">
    <div class="form-group">
        <label>Bug</label>
        {!! Form::text('bug', null, ['id'=>'bug', 'placeholder' => 'Template Files', 'class' => 'form-control bug']) !!}
    </div>
</div> --}}
<div class="col-sm-12">
    <div class="form-group">
        <label>Teamplate File</label>
        {!! Form::text('template_file', null, ['id'=>'template_file', 'placeholder' => 'Bug Solutions', 'class' => 'form-control template_file']) !!}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Bug Details	</label>
        {!! Form::text('bug_details', null, ['id'=>'bug_details', 'placeholder' => 'Bug Details', 'class' => 'form-control bug_details']) !!}                 
    </div>
</div>
<div class="col-sm-12">
    <div class="form-group">
        <label>Bug Solutions</label>
        {!! Form::text('bug_resolution', null, ['id'=>'bug_resolution', 'placeholder' => 'Bug Solutions', 'class' => 'form-control bug_resolution']) !!}                 
    </div>
</div>