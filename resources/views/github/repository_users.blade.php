@extends('layouts.app')

@section('content')
<h2 class="text-center">{{ $repoName }} users</h2>
<div class="text-right">
    <a href="#" class="btn btn-primary">Add User</a>
</div>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Permission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->username}}</td>
                <td>
                    <select onchange="modifyAccess({{$user->username}}, this.value)">
                        <option value="admin" {{$user->pivot->rights == 'admin' ? 'selected' : ''}} >Admin</option>
                        <option value="push" {{$user->pivot->rights == 'push' ? 'selected' : ''}} >Push</option>
                        <option value="pull" {{$user->pivot->rights == 'pull' ? 'selected' : ''}}>Pull</option>
                    </select>
                </td>
                <td>
                    <a class="btn btn-sm btn-primary" href="{{ url('github/repos/'.$repoName.'/users/'.$user->username.'/remove')}}">Remove</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    function modifyAccess(username, access) {
        console.log(githubUserId);
        console.log(userId);
        if (userId) {
            var xhr = new XMLHttpRequest();
            var url = "modifyUserAccess";
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);
                }
            };

            var dataObj = {
                user_name: userId,
                access: access,
                repository_name : "{{ $repoName }}",
                _token: "{{csrf_token()}}"
            };

            console.log(dataObj);

            var data = JSON.stringify(dataObj);
            xhr.send(data);
        }
    }
</script>
@endsection