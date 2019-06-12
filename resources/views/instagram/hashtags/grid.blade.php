@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Hashtag monitoring: #{{ $hashtag }} ({{ $media_count }} Posts)</h2>
            <form action="{{ action('HashtagController@showGrid', 'x') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="All Hashtags">Hashtags</label>
                            <select class="form-control" name="hashtags" id="hashtags">
                                @foreach($hashtagList as $list)
                                    <option {{ $list->hashtag==$hashtag ? 'selected' : '' }} value="{{ $list->hashtag }}">{{ $list->hashtag }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input value="{{$hashtag}}" type="text" name="name" id="name" class="form-control">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-2">
            <p>
                <a href="{{ action('HashtagController@index') }}">Show All Targeted Hashtags</a>
            </p>
        </div>

        <div class="col-md-12 mt-2 mb-3">
            <div class="accordion" id="accordionExample">
                <div class="card mt-0" style="width:100%;">
                    <div class="card-header">
                        <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_am" aria-expanded="true" aria-controls="form_am">
                          Stats
                        </div>
                    </div>
                    <div id="form_am" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Narrative</th>
                                    <th>Count</th>
                                </tr>
                                @foreach($stats as $stat)
                                    <tr>
                                        <th>{{ $stat->narrative }}</th>
{{--                                        <th>{{ $stat->year }} - {{ $stat->month }}</th>--}}
                                        <th>{{ $stat->total }}</th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-default mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-default mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{ Session::get('message') }}
                </div>
            @endif
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table table-bordered">
                    <tr>
                        <th>SN</th>
                        <th style="width:50px">User</th>
                        <th>Post URL</th>
                        <th style="width: 50px;">Image</th>
                        <th>Caption</th>
{{--                        <th style="width:20px;">Likes</th>--}}
                        <th style="width:20px;"># Comments</th>
                        <th>Location</th>
                        <th>Created At</th>
                        <th>Comments</th>
                    </tr>
                    @php $count = 1; @endphp
                    @foreach($medias as $key=>$post)
                        @if (\App\FlaggedInstagramPosts::where('media_id', $post['media_id'])->first())
                        @else
                            <tr id="media_{{$post['media_id']}}">
                                <td>
                                    {{ $count }}
                                    @php $count++ @endphp
                                    <br>
                                    <a class="btn btn-sm btn-image hide-media" data-id="{{$post['media_id']}}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                                <td><a href="https://instagram.com/{{$post['username']}}">{{$post['username']}}</a></td>
                                <td><a href="https://instagram.com/p/{{$post['code']}}">Visit Post</a></td>
                                <td>
                                    @if ($post['media_type'] === 1)
                                        <a href="{{$post['media']}}"><img src="{{ $post['media'] }}" style="width: 100px;"></a>
                                    @elseif ($post['media_type'] === 2)
                                        <video controls src="{{ $post['media'] }}" style="width: 100px"></video>
                                    @elseif ($post['media_type'] === 8)
                                        @foreach($post['media'] as $m)
                                            @if ($m['media_type'] === 1)
                                                <a href="{{$m['url']}}"><img src="{{ $m['url'] }}" style="width: 100px;"></a>
                                            @elseif($m['media_type'] === 2)
                                                <video controls src="{{ $m['url'] }}" style="width: 100px"></video>
                                            @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td style="word-wrap: break-word;text-align: justify;">
                                    <div class="expand-row" style="width:150px;text-align: justify">
                                        <span class="td-mini-container">
                                            {{ strlen($post['caption']) > 20 ? substr($post['caption'], 0, 20).'...' : $post['caption'] }}
                                          </span>

                                        <span class="td-full-container hidden">
                                            {{ $post['caption'] }}
                                        </span>
                                    </div>
                                </td>
{{--                                <td>{{ $post['like_count'] }}</td>--}}
                                <td>
                                    <div style="width: 20px;">
                                        {{ $post['comment_count'] }}
                                    </div>
                                </td>
                                <td>
                                    <div style="width: 150px;">
                                        {!! ($post['location']['name'] ?? 'N/A') . '<br>' . ($post['location']['city'] ?? 'N/A')  !!}
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($key)->toDateString() }}</td>
                                <td style="width: 600px;">
                                    @if ($post['comments'])
                                        <table class="table table-striped table-bordered">
                                            <tr>
                                                <th>Username</th>
                                                <th style="width: 350px">Comment</th>
                                                <th>Commented On</th>
                                                <th>Action</th>
                                            </tr>
                                            <tbody class="comment-list" id="comments-{{$post['media_id']}}">
                                            @foreach($post['comments'] as $keyy=>$comment)
                                                <tr>
                                                    <td><a href="https://instagram.com/{{$comment['user']['username']}}">{{$comment['user']['username']}}</a></td>
                                                    <td class="expand-row" style="word-wrap: break-word;word-break: break-all">
                                                        <span class="td-mini-container">
                                                            {{ strlen($comment['text']) > 20 ? substr($comment['text'], 0, 20).'...' : $comment['text'] }}
                                                          </span>

                                                                        <span class="td-full-container hidden">
                                                            {{ $comment['text'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{\Carbon\Carbon::createFromTimestamp($comment['created_at'])->diffForHumans()}}</td>
                                                    <td>
                                                        <form action="{{ action('ReviewController@createFromInstagramHashtag') }}" method="post">@csrf<input type="hidden" name="code" value="{{$post['code']}}"> <input type="hidden" name="date" value="{{ \Carbon\Carbon::createFromTimestamp($comment['created_at'])->toDateTimeString() }}"> <input type="hidden" name="post" value="{{ $post['caption'] }}"><input type="hidden" name="comment" value="{{ $comment['text'] }}"><input type="hidden" name="poster" value="{{ $post['username'] }}"><input type="hidden" name="commenter" value="{{ $comment['user']['username'] }}"><input type="hidden" name="media_id" value="{{ $post['media_id'] }}"><button class="btn btn-sm btn-image"><i class="fa fa-check"></i></button></form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tr>
                                                <td colspan="5" class="text-center" class="load-more-{{$post['media_id']}}">
                                                    <a data-post-code="{{ $post['code'] }}" class="load-comment" id="load-more-{{$post['media_id']}}" data-media-id="{{$post['media_id']}}"> Load More...</a>
                                                </td>
                                            </tr>
                                        </table>
                                    @else
                                        <strong>No Comments yet!</strong>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control" name="account_id" id="account_id_{{$post['media_id']}}">
                                                @foreach($accs as $cc)
                                                    <option value="{{ $cc->id }}">{{ $cc->last_name }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control" name="narrative_{{$post['media_id']}}" id="narrative_{{$post['media_id']}}">
                                                <option value="common">Common</option>
                                                <option value="promotion">Promotion</option>
                                                <option value="victim">Victim</option>
                                                <option value="troll">Troll</option>
                                            </select>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea type="text" rows="4" class="comment-it form-control" data-author="{{$post['username']}}" data-code="{{$post['code']}}" data-mediaId="{{$post['media_id']}}" placeholder="Type comment..."></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>

        <div class="col-md-12 text-center">
            @if ($maxId !== '' || $maxId = 'END')
                <a class="btn btn-default mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}">FIRST PAGE</a>
                <a class="btn btn-default mb-4" href="{{ action('HashtagController@showGrid', $hashtag) }}?maxId={{($maxId && $maxId != 'END') ? $maxId : ''}}">NEXT</a>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .comment-list tr:last-child td {
            color: #e74c3c;
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
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

        $(document).on('click', '.hide-media', function() {
            let mid = $(this).attr('data-id');
            $.ajax({
                url: '{{ action('HashtagController@flagMedia', '') }}'+'/'+mid,
                success: function() {
                    $("#media_"+mid).hide();
                }
            });
        });

        $(document).on('click', '.load-comment', function() {
            let mediaId = $(this).attr('data-media-id');
            let postCode = $(this).attr('data-post-code');
            $.ajax({
                url: '{{ action('HashtagController@loadComments', '') }}'+'/'+mediaId,
                success: function(response) {
                    let comments = response.comments;
                    if (response.has_more_comments==false) {
                        $('.load-more-'+mediaId).hide();
                    }
                    $('#comments-'+mediaId).html('');
                    comments.forEach(function(comment) {
                        let form = '<form action="{{ action('ReviewController@createFromInstagramHashtag') }}" method="post">@csrf<input type="hidden" name="date" value="'+comment.created_at_time+'"><input type="hidden" name="code" value="'+postCode+'"><input type="hidden" name="post" value="'+response.caption.text+'"><input type="hidden" name="comment" value="'+comment.text+'"><input type="hidden" name="poster" value="'+response.caption.user.username+'"><input type="hidden" name="commenter" value="'+comment.user.username+'"><input type="hidden" name="media_id" value="'+mediaId+'"><button class="btn btn-sm btn-success"><i class="fa fa-check"></i></button></form>';
                        let data = '<tr><td>'+comment.user.username+'</td><td>'+comment.text+'</td><td>'+comment.created_at+'</td><td>'+form+'</td></tr>';
                        $('#comments-'+mediaId).append(data);
                    });
                }
            });
        });

        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                // if ($(this).data('switch') == 0) {
                //   $(this).text($(this).data('details'));
                //   $(this).data('switch', 1);
                // } else {
                //   $(this).text($(this).data('subject'));
                //   $(this).data('switch', 0);
                // }
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $('#hashtags').change(function() {
            let hashtag = $(this).val();
            $('#name').val(hashtag);
        });

        $('.comment-it').keyup(function(event) {
            if (event.keyCode == 13) {
                let message = $(this).val();
                let mediaId = $(this).attr('data-mediaId');
                let author = $(this).attr('data-author');
                let code = $(this).attr('data-code');
                let accountId = $('#account_id_'+mediaId).val();
                let narrative = $('#narrative_'+mediaId).val();
                let self = this;

                $(this).attr('disabled', true);

                $.ajax({
                    url: '{{action('HashtagController@commentOnHashtag')}}',
                    type: 'POST',
                    data: {
                        message: message,
                        post_id: mediaId,
                        account_id: accountId,
                        code: code,
                        author: author,
                        narrative: narrative,
                        hashtag: "{{$hashtag}}",
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        alert('Comment added successfully!');
                        location.reload();
                        $(self).removeAttr('disabled');
                    }
                });
            }
        });
    </script>
@endsection