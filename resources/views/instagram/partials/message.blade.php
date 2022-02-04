<style type="text/css">
    .cls_remove_rightpadding {
        padding-right: 0px !important;
    }

    .cls_remove_allpadding {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }

    #chat-list-history tr {
        word-break: break-word;
    }

    .reviewed_msg {
        word-break: break-word;
    }

    .chatbot .communication {}

    .background-grey {
        color: grey;
    }

    @media(max-width:1400px) {
        .btns {
            padding: 3px 2px;
        }
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ddd !important;
    }

    .d-inline.form-inline .select2-container {
        max-width: 100% !important;
        /*width: unset !important;*/
    }

    .actions {
        display: flex !important;
        align-items: center;
    }

    .actions a {
        padding: 0 3px !important;
        display: flex !important;
        align-items: center;
    }

    .actions .btn-image img {
        width: 13px !important;
    }

    .read-message {
        float: right;
    }

</style>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="2%">Name</th>
                <th width="2%">Platform</th>
                <th width="2%">Website</th>
                <th width="8%">User input</th>
                <th width="30%">Message Box </th>
                <th width="2%">From</th>
                <th width="2%">Shortcuts</th>
                <th width="2%">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($socialContact as $contact)
                <tr class="customer-raw-line">
                    <td>{{ $contact->name }}</td>
                    <td>{{ ucfirst($contact->socialConfig->platform) }}</td>
                    <td>{{ $contact->socialConfig->name }}</td>
                    <td class="log-message-popup"
                        data-log_message="{{ $contact->getLatestSocialContactThread->text }}">
                        {{ substr($contact->getLatestSocialContactThread->text, 0, 15) }}...</td>
                    <td class="message-input p-0 pt-2 pl-3">
                        <div class="cls_textarea_subbox">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="w-75">
                                    <textarea rows="1"
                                        class="form-control quick-message-field cls_quick_message addToAutoComplete"
                                        name="message" placeholder="Message" id="textareaBox_{{ $contact->id }}"
                                        data-customer-id="{{ $contact->id }}"></textarea>
                                </div>
                                <div class="w-25 pl-2" role="group" aria-label="First group">
                                    <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1"
                                        data-id="textareaBox_{{ $contact->id }}">
                                        <img src="/images/filled-sent.png">
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm m-0 p-0 mr-1 btn-image load-contact-communication-modal"
                                        data-object="social-contact" data-id="{{ $contact->id }}" data-load-type="text"
                                        data-all="1" title="Load messages"><img src="{{ asset('images/chat.png') }}"
                                            alt=""></button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!--Log Messages Modal -->
<div id="logMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User Input</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="word-break: break-word;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).on('click', '.log-message-popup', function() {
        $('#logMessageModel p').text($(this).data('log_message'));
        $('#logMessageModel').modal('show');
    })

    $(document).on('click', '.send-message1', function() {
        const textareaId = $(this).data('id');
        const value = $(`#${textareaId}`).val();
        const contactId = $(`#${textareaId}`).data('customer-id');
        if (value.trim()) {
            $("#loading-image").show();
            $.ajax({
                url: "{{ route('social.message.send') }}",
                method: 'POST',
                async: true,
                data: {
                    _token: '{{ csrf_token() }}',
                    input: value,
                    contactId: contactId
                },
                success: function(res) {
                    alert(res.message)
                    $(`#${textareaId}`).val("")
                    $("#loading-image").hide();
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