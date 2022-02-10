@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Comments</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Comment</th>
                    <th>User</th>
                    <th>Created At</th>
                    <th>Action</th>
            </thead>
            <tbody>
                @forelse($comments as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td style="width:50%">
                            <div style="word-break: break-word;">
                                @if ($value->message) {{ $value->message }} @else <small class="text-secondary">(No caption added)</small> @endif
                            </div>
                            @if ($value->photo)
                                <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                        <td>{{ $value->user->name }}</td>
                        <td>{{ $value->time }}</td>
                        <td>
                            <button id="showReplyButton" class="btn btn-light"
                                data-comment-id="{{ $value->comment_id }}">Show Reply</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center">No Comments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (isset($posts))
            {{ $posts->links() }}
        @endif
    </div>

    <div id="showReplyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reply</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>User</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody class="table-body"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#showReplyButton', function(e) {
            $("#loading-image").show();
            const commentId = $(this).data('comment-id')
            $.ajax({
                url: "{{ route('social.account.comments.reply') }}",
                method: 'POST',
                async: true,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: commentId
                },
                success: function(data) {
                    const comments = data.comments

                    $("#showReplyModal .modal-body .table-body").empty()
                    if (comments.length > 0) {

                        comments.forEach(element => {

                            let appendData = `<tr>
                                <td style="width:50%">
                                    <div>${element.message}</div>
                                    `;
                            if (element.photo) {
                                appendData +=
                                    `<img src="${element.photo}" width="100" alt="${element.message}" />`
                            }
                            appendData += `
                                </td>
                                <td style="white-space:nowrap">${element.user.name || ''}</td>
                                <td style="white-space:nowrap">${element.time}</td>
                            </tr> `
                            $("#showReplyModal .modal-body .table-body").append(appendData)

                        });
                    } else {
                        $("#showReplyModal .modal-body .table-body").append(`
                        <tr>
                            <td colspan="3" align="center">No reply found</td>
                        </tr>    
                        `)
                    }
                    $("#loading-image").hide();
                    $("#showReplyModal").modal("show")
                },
                error: function(error) {
                    alert("Couldn't load comment");
                    $("#loading-image").hide();
                    console.log(error);
                }
            })
        })
    </script>
@endsection