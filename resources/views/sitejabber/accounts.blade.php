@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Sitejabber Accounts, Reviews & Q/A</h2>
        </div>
        <div class="col-md-12 mb-5">
            <div id="exTab2" class="container">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#one" data-toggle="tab" class="btn btn-image">Accounts & Reviews</a>
                    </li>
                    <li>
                        <a href="#two" data-toggle="tab" class="btn btn-image">Q&A</a>
                    </li>
                    <li>
                        <a href="#three" data-toggle="tab" class="btn btn-image">Settings</a>
                    </li>
                    <li>
                        <a href="#four" data-toggle="tab" class="btn btn-image">Review Templates</a>
                    </li>
                    <li>
                        <a href="#five" data-toggle="tab" class="btn btn-image">Negative Comments</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active mt-3" id="one">
                    <table id="table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Name</th>
                            <th>E-Mail Address</th>
                            <th>Password</th>
                            <th>Created On</th>
                            <th>Reviews Posted</th>
                            <th>Approval Status</th>
                            <th>Post Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounts as $key=>$sj)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $sj->first_name ?? 'N/A' }} {{ $sj->last_name ?? 'N/A' }}</td>
                                <td>{{ $sj->email }}</td>
                                <td>{{ $sj->password }}</td>
                                <td>{{ $sj->created_at->format('Y-m-d') }}</td>

                                <td>
                                    @if ($sj->reviews()->count())
                                        @foreach($sj->reviews as $answer)
                                            <div class="alert @if($answer->status=='posted_one') alert-danger @elseif($answer->status=='posted') alert-success @elseif($answer->is_approved) alert-warning @else alert-info @endif">
                                                <strong>{{ $answer->title }}</strong><br>{{ $answer->review }}
                                            </div>
                                            @if($answer->status!= 'posted' && $answer->status!= 'posted_one')
                                                <form method="post" action="{{ action('ReviewController@destroy', $answer->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ action('ReviewController@edit', $answer->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @if(!$answer->is_approved)
                                                        <a title="Approve" href="{{ action('ReviewController@updateStatus', $answer->id) }}?id_approved=1" class="btn btn-sm btn-info">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    @endif
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="accordion" id="accordionExample">
                                            <div class="card mt-0" style="width:400px;">
                                                <div class="card-header">
                                                    <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_am" aria-expanded="true" aria-controls="form_am">
                                                        Attach A New Review
                                                    </div>
                                                </div>
                                                <div id="form_am" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <form action="{{ action('ReviewController@store') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="account_id" value="{{ $sj->id }}">
                                                            <div class="form-group">
                                                                <input name="title" type="text" class="form-control" placeholder="Enter Title...">
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control review-editor-box" data-id="{{$key+1}}" name="review" id="review_{{$key+1}}" rows="3" placeholder="Enter Body..."></textarea>
                                                                <span class="letter_count_review_{{$key+1}}"></span>
                                                            </div>
                                                            <div class="text-right">
                                                                <button class="btn btn-success">Attach A Review</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">{!! (isset($answer) && $answer->is_approved) ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                                <td class="text-center"><a href="{{ action('SitejabberQAController@confirmReviewAsPosted', isset($answer) ? $answer->id : '') }}">{!! (isset($answer) && $answer->status =='posted') ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane mt-3" id="two">
                    <div class="accordion" id="accordionExample">
                        <div class="card mt-0">
                            <div class="card-header">
                                <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_amx" aria-expanded="true" aria-controls="form_amx">
                                    Attach A New Question
                                </div>
                            </div>
                            <div id="form_amx" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <form action="{{ action('SitejabberQAController@store') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="question">Question</label>
                                            <input name="question" type="text" id="question" class="form-control" placeholder="Type your question..">
                                        </div>
                                        <div class="form-group">
                                            <label for="account_id">Poster</label>
                                            <select name="account_id" type="text" id="account_id" class="form-control">
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->first_name }} {{ $account->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-right">
                                            <button class="btn btn-success">Add Question</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <table id="table2" class="table table-striped">
                        <thead>
                            <tr>
                                <th>I.D</th>
                                <th>Question</th>
                                <th>Answers</th>
                                <th>Status</th>
                                <th>Reply</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sjs as $kkk=>$sj)
                            <tr>
                                <th>{{$kkk+1}}</th>
                                <th>{{$sj->text}}</th>
                                <td>
                                    <table class="table table-striped">
                                        @foreach($sj->answers as $answer)
                                            <tr>
                                                <td>{{ $answer->author }} <span class="badge badge-success">{{$answer->type}}</span> @if ($answer->status == 1) <span class="badge badge-primary">Posted</span> @endif</td>
                                                <td>{{ $answer->text }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td class="text-center">{!! $sj->status==1 ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                                <td>
                                    <div class="form-group" style="width: 400px;">
                                        <form action="{{ action('SitejabberQAController@update', $sj->id) }}" method="post">
                                            @csrf
                                            @method('put')
                                            <textarea type="text" name="reply" class="form-control" placeholder="Type reply..."></textarea>
                                            <div class="form-group">
                                                <label for="account_id">Poster</label>
                                                <select name="account_id" type="text" id="account_id" class="form-control">
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->first_name }} {{ $account->last_name }}
                                                            @if ($account->reviews()->first())
                                                                (Review: {{ $account->reviews()->first()->title }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="text-right">
                                                <button class="btn btn-success mt-1">Reply To Thread</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane mt-3" id="three">
                    <div class="row">
                        <div class="col-md-12" style="font-size: 22px;">
                            1. Accounts Remaining: {{ $accountsRemaining }}<br>
                            2. Total Accounts: {{ $totalAccounts }}<br>
                            3. Reviews Remaining: {{ $remainingReviews }}<br><br>
                        </div>
                        <form method="get" action="{{action('SitejabberQAController@edit', 'routines')}}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="range">Post this number of reviews in a day</label>
                                    <input name="range" id="range" type="number" class="form-control" placeholder="Eg: 6" value="{{ $setting->times_a_day }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="range2">Create this number of SJ account in a day</label>
                                    <input name="range2" id="range2" type="number" class="form-control" placeholder="Eg: 6" value="{{ $setting2->times_a_day }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="range3">Post this number of question every week</label>
                                    <input name="range3" id="range3" type="number" class="form-control" placeholder="Eg: 6" value="{{ $setting3->times_a_week }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="mt-4 btn btn-primary">Ok</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-panel mt-3" id="five">
                    <table class="table table-striped">
                        <tr>
                            <th>S.N</th>
                            <th>Username</th>
                            <th>title</th>
                            <th>Body</th>
                            <th>Reply</th>
                        </tr>
                        @foreach($negativeReviews as $key=>$negativeReview)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $negativeReview->username }}</td>
                                <td>{{ $negativeReview->title }}</td>
                                <td>{{ $negativeReview->body }}</td>
                                <td>
                                    @if($negativeReview->reply != '')
                                        {{ $negativeReview->reply }}
                                    @else
                                        <div class="form-group">
                                            <input data-rid="{{$negativeReview->id}}" data-title="{{$negativeReview->title}}" style="width: 300px;" type="text" class="form-control reply-review" name="reply_{{ $negativeReview->id }}">
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div class="tab-panel mt-3" id="four">
                    <table id="table3" class="table table-striped">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Platform</th>
                                <th>brand</th>
                                <th>Title</th>
                                <th>Body</th>
                                <th>Created At</th>
                                <th>Attach For Approval</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($brandReviews as $key=>$brandReview)
                            <tr>
                                <th>{{ $key+1 }}</th>
                                <td>{{ $brandReview->website }}</td>
                                <td>{{ $brandReview->brand }}</td>
                                <td>{{ $brandReview->title }}</td>
                                <td>{{ $brandReview->body }}</td>
                                <td>{{ $brandReview->created_at->format('Y-m-d') }}</td>
                                <td width="100px;">
                                    <a class="btn btn-info btn-sm" href="{{ action('SitejabberQAController@attachBrandReviews', $brandReview->id) }}">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="{{ action('SitejabberQAController@detachBrandReviews', $brandReview->id) }}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style>
        thead input {
            width: 100%;
        }
    </style>
@endsection

@section('scripts')

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table thead tr').clone(true).appendTo( '#table thead' );
            $('#table thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );

            $('.review-editor-box').keyup(function() {
                let data = $(this).val();
                let length = data.length;
                let id = $(this).attr('data-id');
                $('.letter_count_review_'+id).html(length);
            });
            $('.reply-review').keyup(function(event) {
                let title = $(this).attr('data-title');
                let message = $(this).val();
                let rid = $(this).attr('data-rid');
                let self = this;
                if (event.keyCode==13) {

                    $(this).attr('disabled', true);
                    $.ajax({
                        url: '{{ action('SitejabberQAController@sendSitejabberQAReply') }}',
                        type: 'post',
                        data: {
                            comment: title,
                            reply: message,
                            rid: rid,
                            _token: "{{csrf_token()}}"
                        },
                        success: function(response) {
                            // $(self).removeAttr('disabled');
                            alert('Posted successfully!');
                        },

                    });
                }
            });
            var table = $('#table').DataTable({
                orderCellsTop: true,
                fixedHeader: true
            });

            $('#table2 thead tr').clone(true).appendTo( '#table2 thead' );
            $('#table2 thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table2.column(i).search() !== this.value ) {
                        table2
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table2 = $('#table2').DataTable({
                orderCellsTop: true,
                fixedHeader: true
            });

            $('#table3 thead tr').clone(true).appendTo( '#table3 thead' );
            $('#table3 thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table3.column(i).search() !== this.value ) {
                        table3
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table3 = $('#table3').dataTable({
                orderCellsTop: true,
                fixedHeader: true
            });
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection