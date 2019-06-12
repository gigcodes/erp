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
            <button class="btn btn-default" data-toggle="collapse" data-target="#demo">Show Target hashtags</button>

            <div id="demo" class="collapse" style="padding: 15px;">
                <form action="{{action('AutoReplyHashtagsController@show', 'all')}}" method="get">
                        <div class="col-md-2">
                            <div class="form-group">
                                <select style="width: 100%" class="form-control" name="country" id="country">
                                    <option value="0">Country/Region</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->region}}">{{$country->region}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select style="width: 100%" name="hashtags[]" id="hashtags" multiple>
                                    @foreach($hashtags as $hashtag)
                                        <option value="{{$hashtag->text}}">{{$hashtag->text}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="form-group">
                                    <select style="width: 100%" name="keywords[]" id="keywords" multiple></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button>Add Post</button>
                        </div>
                    </form>
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
            <table id="table" class="table-striped table table-bordered">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>--}}
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>


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
        // new Chart(document.getElementById("pie"), {
        //     type: 'pie',
        //     data: {
        //         labels: labels,
        //         datasets: [{
        //             label: "Number Of Comments By #Tags",
        //             backgroundColor: backgroundColor,
        //             data: data
        //         }]
        //     },
        //     options: {
        //         title: {
        //             display: true,
        //             text: 'Comments Statistics'
        //         }
        //     }
        // });

        $(document).ready(function () {
            // $('#table2 thead tr').clone(true).appendTo( '#table2 thead' );
            // $('#table2 thead tr:eq(1) th').each( function (i) {
            //     var title = $(this).text();
            //     $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table2.column(i).search() !== this.value ) {
            //             table2
            //                 .column(i)
            //                 .search( this.value )
            //                 .draw();
            //         }
            //     } );
            // } );
            var table2 = $('#table2').DataTable({
                orderCellsTop: true,
                fixedHeader: true
            });
        });
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script>
        $("#hashtags, #keywords").select2({
            tags: true
        });
    </script>
@endsection
