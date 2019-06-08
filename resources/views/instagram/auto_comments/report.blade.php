@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Comments Report</h2>
        </div>


        <div class="col-md-12">
            <button class="btn btn-success btn-block" data-toggle="collapse" data-target="#demo">Show Target hashtags</button>

            <div id="demo" class="collapse" style="background: #cccccc; padding: 15px;">
                <form method="post" action="{{action('AutoReplyHashtagsController@store')}}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name of hashtag (without #)</label>
                        <input required class="form-control" type="text" id="hashtag" name="hashtag" placeholder="Hashtag... ">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info">Save</button>
                    </div>
                </form>

                <table class="table table-bordered">
                    <tr>
                        <th>SN</th>
                        <th>#Tag</th>
                        <th>Status</th>
                        <th>Comments Processed</th>
                        <th>Action</th>
                    </tr>
                    @foreach($hashtags as $key=>$hashtag)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $hashtag->text }}</td>
                            <td>{{ $hashtag->status ? 'On Progress' : 'Completed' }}</td>
                            <td>{{ $hashtag->comments()->count() ?? 0 }}</td>
                            <th style="width: 200px;">
                                <form action="{{action('AutoReplyHashtagsController@show', $hashtag->id)}}" method="get">
                                    <select class="form-control" name="country" id="country_{{$hashtag->id}}">
                                        <option value="0">Country/Region</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->region}}">{{$country->region}}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-block mt-1 btn-info btn-sm">Add Posts</button>
                                </form>
                            </th>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        @foreach($statsByCountry as $item)
                            <tr>
                                <th>{{$item->country ?? 'N/A'}}</th>
                                <th>{{$item->total}}</th>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        @foreach($statsByHashtag as $item)
                            <tr>
                                <th>{{$item->target ?? 'N/A'}}</th>
                                <th>{{$item->total}}</th>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <br>
            <br>
            <table class="table-striped table">
                <tr>
                    <th>SN</th>
                    <th>#tag</th>
                    <th>Gender</th>
                    <th>Country</th>
                    <th>Post</th>
                    <th>Commenter</th>
                    <th>Comment</th>
                    <th>Created At</th>
                </tr>
                @foreach($comments as $key=>$comment)
                    <tr>
                        <th>{{$key}}</th>
                        <td>#{{ $comment->hashtag->text }}</td>
                        <td>{{$comment->gender}}</td>
                        <td>{{$comment->country ?? 'N/A'}}</td>
                        <td>
                            {{ $comment->caption }}
                            <br>
                            <a target="_new" href="https://instagram.com/p/{{$comment->post_code}}">Visit post</a>
                        </td>
                        <td>{{$comment->account->last_name ?? 'N/A'}}</td>
                        <td>{{$comment->comment ?? 'N/A'}}</td>
                        <td>{{$comment->created_at->format('Y-m-d')}}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="text-center col-md-12">
            {!! $comments->links() !!}
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script>
        var labels = [];
        var data = [];
        var backgroundColor = [];

        @foreach ($hashtags as $hashtag)
            labels.push("{{$hashtag->text}}");
            data.push("{{$hashtag->comments()->count()}}");
            backgroundColor.push('#'+(Math.random()*0xFFFFFF<<0).toString(16));
        @endforeach
    </script>
    <script>
        new Chart(document.getElementById("pie"), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: "Number Of Comments By #Tags",
                    backgroundColor: backgroundColor,
                    data: data
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Comments Statistics'
                }
            }
        });
    </script>
@endsection
