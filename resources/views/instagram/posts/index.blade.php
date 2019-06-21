@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Posts</h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <form action="{{ action('InstagramPostsController@store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-3">
                        <label for="account">Account</label>
                        <select name="account_id" id="account_id" class="form-control">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="caption">Caption</label>
                        <textarea name="caption" id="caption" rows="2" class="form-control" name="caption"></textarea>
                    </div>
                    <div class="col-md-2">
                        <label for="Image">Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button class="btn-secondary btn">Post</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <table class="table-striped table table-bordered">
                <tr>
                    <th>S.N</th>
                    <th>Username</th>
                    <th>Caption</th>
                    <th>Image</th>
                    <th>Posted On</th>
                </tr>

                @foreach($posts as $key=>$post)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{ $post->username }}</td>
                        <td>{{ $post->caption }}</td>
                        <td>
                            <img style="width: 100px;" src="{{ $post->getMedia('gallery')->first()->getUrl() }}" alt="">
                        </td>
                        <td>{{ $post->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection