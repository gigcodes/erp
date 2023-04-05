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
        table {
table-layout: fixed !important;
}

table tr td {
max-width: 100% !important;
overflow-x: auto !important;
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
    <input id="config-id" class="config-id" type="hidden" value="{{ $post->social_config_id ?? '' }}">
    <div class="mt-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th>Comment Original</th>
                    <th>Comment With Translation</th>
                    <th>Reply</th>
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
                                <!-- @if ($value->translation) {{ $value->translation }} @else <small class="text-secondary">(No caption added)</small> @endif -->
                            </div>
                            @if ($value->photo)
                                <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                        <td style="width:50%">
                            <div style="word-break: break-word;">
                                <!-- @if ($value->message) {{ $value->message }} @else <small class="text-secondary">(No caption added)</small> @endif -->
                                @if ($value->translation) {{ $value->translation }} @else <small class="text-secondary">(No caption added)</small> @endif
                            </div>
                            @if ($value->photo)
                                <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                     
                        <td class="message-input p-0 pt-2 pl-3">
                            <div class="cls_textarea_subbox">
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="w-75">
                                        <textarea rows="1"
                                            class="form-control quick-message-field cls_quick_message addToAutoComplete"
                                            name="message" placeholder="Message" id="textareaBox_{{ $value->comment_id }}"
                                            data-customer-id="{{ $value->comment_id }}"></textarea>
                                    </div>
                                    <div class="w-25 pl-2" role="group" aria-label="First group">
                                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1"
                                            data-id="textareaBox_{{ $value->comment_id }}">
                                            <img src="/images/filled-sent.png">
                                        </button>
                                    
                                    </div>
                                </div>
                            </div>
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

        $(document).on('click', '.send-message1', function() {
        const textareaId = $(this).data('id');
        const value = $(`#${textareaId}`).val();
        const configId = document.getElementById("config-id").value;  
        const contactId = $(`#${textareaId}`).data('customer-id');
        if (value.trim()) {
            $("#loading-image").show();
            $.ajax({
                url: "{{ route('social.dev.reply.comment') }}",
                method: 'POST',
                async: true,
                data: {
                    _token: '{{ csrf_token() }}',
                    input: value,
                    contactId: contactId,
                    configId: configId
                },
                success: function(res) {
                    $("#loading-image").hide();
                    document.getElementById("textareaBox_"+contactId).value = '';
                    toastr["success"]("Message successfully send!", "Message")
                },
                error: function(error) {
                    console.log(error.responseJSON);
                    alert("Counldn't send messages")
                    $("#loading-image").hide();
                }
            })
        } else {
            alert("Please enter a message")
        }
    })

    </script>
@endsection
