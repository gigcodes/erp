@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container">
    <div class="page-header">
    <h4>Create Account</h4>
</div>
    <form method="POST" action="/googlecampaigns/adsaccount/update" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="hidden" id="account_id" name="account_id" placeholder="Account Id" value="{{$account->id}}">
        <div class="form-group row">
            <label for="account_name" class="col-sm-2 col-form-label">Account name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account Name" value="{{$account->account_name}}">
                @if ($errors->has('account_name'))
                <span class="text-danger">{{$errors->first('account_name')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="store_websites" class="col-sm-2 col-form-label">Store Website</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="store_websites" name="store_websites" placeholder="Store Website" value="{{$account->store_websites}}">
                @if ($errors->has('store_websites'))
                <span class="text-danger">{{$errors->first('store_websites')}}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="notes" class="col-sm-2 col-form-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" placeholder="Notes">{{$account->notes}}</textarea>
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
                    <option value="ENABLED" {{$account->status=="ENABLED"?'selected':''}}>ENABLED</option>
                    <option value="DISABLED" {{$account->status=="DISABLED"?'selected':''}}>DISABLED</option>
                </select>
                @if ($errors->has('status'))
                <span class="text-danger">{{$errors->first('status')}}</span>
                @endif
            </div>
        </div>
        <button type="submit" class="mb-2 float-right">Update</button>
    </form>
    </div>
@endsection