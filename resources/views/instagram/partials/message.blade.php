<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="8%">Platform Name</th>
                <th width="2%">Platform</th>
                <th width="8%">Website</th>
                <th width="8%">User input</th>
                <th width="30%">Message Box </th>
                <th width="4%">From</th>
                <th width="5%">Shortcuts</th>
                <th width="5%">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($socialContact as $contact)
                <tr class="customer-raw-line">
                    <td>{{ $contact->socialConfig->name }}</td>
                    <td>{{ ucfirst($contact->socialConfig->platform) }}</td>
                    <td>{{ $contact->socialConfig->storeWebsite->title }}</td>
                    @if($contact->getLatestSocialContactThread)
                    <td class="log-message-popup"
                        data-log_message="{{ $contact->getLatestSocialContactThread->text }}">
                        {{ substr($contact->getLatestSocialContactThread->text, 0, 15) }}...</td>
                    @else
                    <td> <small class="text-secondary"> <small>No conversations found</small> </td>
                    @endif
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
                    <td>
                        @php
                            $message = $contact->messages->first();
                            $page_id = $contact->socialConfig->page_id;
                            $from = $message->from['id'] != $page_id ? $message->from : $message->to[0];
                        @endphp
                        {{ $from['name'] }} : {{ $from['email'] }}
                    </td>
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
