@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Influencers</h2>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <form method="post" action="{{ action('InfluencersController@store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Username (without @)</label>
                            <input type="text" name="name" id="name" placeholder="sololuxuryindia (without @)" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Add?</label>
                            <button class="btn-block btn btn-success">Add Influencer</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table-striped table table-sm">
                <tr>
                    <th>S.N</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
                @foreach($hashtags as $key=>$hashtag)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            <a href="https://instagram.com/{{$hashtag->username}}">
                                {{ $hashtag->username }}
                            </a>
                        </td>
{{--                        <td>{{ $hashtag->rating }}</td>--}}
                        <td>
                            <form method="post" action="{{ action('InfluencersController@destroy', $hashtag->id) }}">
{{--                                <a class="btn btn-info" href="{{ action('HashtagController@showGrid', $hashtag->id) }}">--}}
{{--                                    <i class="fa fa-eye"></i>--}}
{{--                                </a>--}}
{{--                                <a class="btn btn-info" href="{{ action('HashtagController@edit', $hashtag->hashtag) }}">--}}
{{--                                    <i class="fa fa-info"></i>--}}
{{--                                </a>--}}
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>
        var cid = null;
        $(function(){
            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });

        });
    </script>
@endsection