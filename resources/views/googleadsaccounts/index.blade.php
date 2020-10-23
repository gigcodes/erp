@extends('layouts.app')
@section('favicon' , 'task.png')

@section('content')
    <div class="container" style="margin-top: 10px">
    <h4>Google AdWords Account ({{$totalentries}})</h4>
        <form method="get" action="/googlecampaigns/adsaccount/create">
            <button type="submit" class="float-right mb-3">New Account</button>
        </form>
    
        <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#ID</th>
                <th>Account Name</th>
                <th>Store Website</th>
                <th>Config-File</th>
                <th>Notes</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($googleadsaccount as $googleadsac)
                <tr>
                    <td>{{$googleadsac->id}}</td>
                    <td>{{$googleadsac->account_name}}</td>
                    <td>{{$googleadsac->store_websites}}</td>
                    <td>{{$googleadsac->config_file_path}}</td>
                    <td>{{$googleadsac->notes}}</td>
                    <td>{{$googleadsac->status}}</td>
                    <td>{{$googleadsac->created_at}}</td>
                    <td>
                    <a href="/googlecampaigns/adsaccount/update/{{$googleadsac->id}}" class="btn-image"><img src="/images/edit.png"></a>
                    <a href="/googlecampaigns?account_id={{$googleadsac->id}}" class="btn btn-sm">create campaign</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        {{ $googleadsaccount->links() }}
</div>
@endsection