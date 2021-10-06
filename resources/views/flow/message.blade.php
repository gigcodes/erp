{{ Form::model($flowMessage,array('url'=>route('flow-action-message'), 'class'=>'ajax-submit')) }}
	<div class="form-group {{ $errors->has('sender_name') ? 'has-error' : '' }}">
        {!! Form::label('Sender Name:') !!}
        {!! Form::text('sender_name', null, ['class'=>'form-control', 'placeholder'=>'Enter Sender Name', 'required']) !!}
    </div>

    <div class="form-group {{ $errors->has('sender_email_address') ? 'has-error' : '' }}">
        {!! Form::label('Sender Email Address:') !!}
        {!! Form::text('sender_email_address', null, ['class'=>'form-control', 'placeholder'=>'Enter Sender Email Address', 'required']) !!}
    </div>

    <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
        {!! Form::label('Subject:') !!}
        {!! Form::text('subject', null, ['class'=>'form-control', 'placeholder'=>'Enter Subject', 'required']) !!}
    </div>

    <div class="form-group {{ $errors->has('html_content') ? 'has-error' : '' }}">
        {!! Form::label('Content:') !!}
        {!! Form::textarea('html_content', null, ['class'=>'form-control', 'placeholder'=>'Enter Content', 'id'=>'html_content', 'required']) !!}
    </div>
    <div class="form-group">
        {{Form::hidden('action_id', null, array('id'=>'flow_message_action_id'))}}
        {{Form::hidden('id', null)}}
		<button type="submit" class="btn btn-secondary">Create</button>
    </div>
</form>