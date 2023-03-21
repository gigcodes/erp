@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
    <div class="page-header">
    <h4>Create Account</h4>
</div>
    <form method="POST" action="/google-campaigns/ads-account/create" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group row">
            <label for="account_name" class="col-sm-2 col-form-label">Account name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account Name">
                @if ($errors->has('account_name'))
                <span class="text-danger">{{$errors->first('account_name')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="google_customer_id" class="col-sm-2 col-form-label">Google Customer Id</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="google_customer_id" name="google_customer_id" placeholder="Google Customer Id">
                @if ($errors->has('google_customer_id'))
                <span class="text-danger">{{$errors->first('google_customer_id')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="store_websites" class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="store_websites" name="store_websites" style="height: auto">
                <option value="" selected>---Selecty store websites---</option>
                @foreach($store_website as $sw)     
                <option value="{{$sw->website}}">{{$sw->website}}</option>
                @endforeach
                </select>
                @if ($errors->has('store_websites'))
                    <span class="text-danger">{{$errors->first('store_websites')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="notes" class="col-sm-2 col-form-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" placeholder="Notes"></textarea>
                @if ($errors->has('notes'))
                <span class="text-danger">{{$errors->first('notes')}}</span>
                @endif
            </div>
        </div>
        
        <div class="form-group row">
            <label for="config_file_path" class="col-sm-2 col-form-label">Config File</label>
            <div class="col-sm-10">
                <input type="file" class="form-control" id="config_file_path" name="config_file_path">
                @if ($errors->has('config_file_path'))
                <span class="text-danger">{{$errors->first('config_file_path')}}</span>
                @endif
            </div>
        </div>
        
        <div class="form-group row">
            <label for="status" class="col-sm-2 col-form-label">Status</label>
            <div class="col-sm-10">
                <select class="browser-default custom-select" id="status" name="status" style="height: auto">
                    <option value="ENABLED" selected>ENABLED</option>
                    <option value="DISABLED">DISABLED</option>
                </select>
                @if ($errors->has('status'))
                <span class="text-danger">{{$errors->first('status')}}</span>
                @endif
            </div>
        </div>
        <button type="submit" class="mb-2 float-right">Create</button>
    </form>
    </div>
@endsection