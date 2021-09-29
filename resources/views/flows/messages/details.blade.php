
@extends('layouts.app')

@section('content')
    <div class="panel panel-primary">
        {{--<div class="panel-heading">Edit Category</div>--}}
        <div class="panel-body">
            <div class="row">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3><?php echo isset($flowmeaage) ?'Edit Flow Message' : 'Add Flow Message' ?></h3>

                @if(isset($flowmeaage))
                  {{ Form::model($flowmeaage, ['route' => ['admin.flowmeaages.update', $flowmeaage->id], 'method' => 'patch','files'=> true,'class'=>'ajax-submit','id'=>'AdminUser']) }}
                @else
                    {{ Form::open(['route' => 'admin.flowmeaages.store','files'=> true,'class'=>'ajax-submit','id'=>'categoryform']) }}
                @endif

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    <div class="form-group {{ $errors->has('sender_name') ? 'has-error' : '' }}">
                        {!! Form::label('Sender Name:') !!}
                        {!! Form::text('sender_name', old('sender_name'), ['class'=>'form-control', 'placeholder'=>'Enter Sender Name']) !!}
                        <span class="text-danger">{{ $errors->first('sender_name') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('sender_email_address') ? 'has-error' : '' }}">
                        {!! Form::label('Sender Email Address:') !!}
                        {!! Form::text('sender_email_address', old('sender_email_address'), ['class'=>'form-control', 'placeholder'=>'Enter Sender Email Address']) !!}
                        <span class="text-danger">{{ $errors->first('sender_email_address') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
                        {!! Form::label('Subject:') !!}
                        {!! Form::text('subject', old('subject'), ['class'=>'form-control', 'placeholder'=>'Enter Subject']) !!}
                        <span class="text-danger">{{ $errors->first('subject') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('html_content') ? 'has-error' : '' }}">
                        {!! Form::label('Content:') !!}
                        {!! Form::textarea('html_content', old('html_content'), ['class'=>'form-control', 'placeholder'=>'Enter Content']) !!}
                        <span class="text-danger">{{ $errors->first('html_content') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('reply_to_email') ? 'has-error' : '' }}">
                        {!! Form::label('Reply To Email:') !!}
                        {!! Form::text('reply_to_email', old('reply_to_email'), ['class'=>'form-control', 'placeholder'=>'Enter Reply To Email']) !!}
                        <span class="text-danger">{{ $errors->first('reply_to_email') }}</span>
                    </div>
                    <div class="form-group">
                        {{Form::hidden('id', null)}}
                        {!! Form::submit('Submit', ['class' => 'btn btn-secondary']) !!}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
@endsection
