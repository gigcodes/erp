@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2>Instagram Users</h2>
            
        </div>
        <div class="col-md-12">
            <table class="table table-striped" id="table" style="width: 100%">
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Username</th>
                        <th>Image</th>
                        <th>Bio</th>
                        <th>Following</th>
                        <th>Followers</th>
                        <th>Post</th>
                        <th>Location</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                         <tr>
                                <td>
                                    <a href="/instagram/users/{{ $user->user_id }}" target="_blank">{{ $user->user_id }}</a>
                                </td>
                                <td>
                                    {{ $user->username }}
                                </td>
                                <td>
                                    <img src="{{ $user->image_url }}" width="100" height="100">
                                </td>
                                <td>
                                    {{ $user->bio }}
                                </td>
                                <td>
                                    {{ $user->following }}
                                </td>
                                <td>
                                    {{ $user->followers }}
                                </td>
                                <td>
                                    {{ $user->posts }}
                                </td>
                                <td>
                                    {{ $user->location }}
                                </td>
                            </tr>
                    @endforeach
                </tbody>
                {!! $users->links() !!}
            </table>
        </div>
    </div>
@endsection



@section('scripts')
    
@endsection