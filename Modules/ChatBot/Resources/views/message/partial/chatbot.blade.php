@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Dialog | Chatbot')

@section('content')
    <link rel="stylesheet" type="text/css" href="/css/chat-bot.css">
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
    </div>
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-md-10 mx-auto">
                <div class="chat-bot">
                    <div class="chat-bot-body">
                        @if($message[0]['inout'] == 'out')
                            <div id="chat_message_{{ $message[0]['id'] }}" class="mb-3">
                                <div class="right-chat chat-message">
                                    <div class="d-flex align-items-end text-area">
                                        <textarea class="form-control" id="text_{{$message[0]['id']}}"
                                                  hidden>{{ $message[0]['message'] }}</textarea>
                                    </div>
                                    <p id="intent_{{ $message[0]['id'] }}">{{ $message[0]['message'] }}
                                        <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true"
                                           onclick="editIntent('{{ $message[0]['message'] }}', {{ $message[0]['id'] }}, {{ $chatQuestions ? $chatQuestions['id'] : ''}}, 1)"></i>
                                    </p>
                                    @if($type != 'Database')
                                        <div id="intent_add_{{ $message[0]['id'] }}">
                                            <p>Do you store this question in database?</p>
                                            <button class="btn btn-secondary save-btn mt-0" onclick="storeIntent({{ $message[0]['id'] }}, {{ $chatQuestions ? $chatQuestions['id'] : ''}})">
                                                Yes
                                            </button>
                                            <button class="btn btn-secondary save-btn mt-0" onclick="closeIntent({{ $message[0]['id'] }})">No</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex intent-details align-items-center justify-content-end mb-2">
                                    <p class="mr-2 mb-0">Intent:</p><select class="form-control">
                                        <option>{{$chatQuestions ? $chatQuestions['value'] : ''}}</option>
                                    </select>
                                    <p class="mb-0 ml-3">Get Type: {{ $type }}</p>
                                </div>
                            </div>
                        @else
                            <div id="chat_message_{{ $message[0]['id'] }}" class="mb-3">
                                <div class="right-chat chat-message">
                                    <div class="d-flex align-items-end text-area">
                                        <textarea class="form-control" id="text_{{$message[0]['id']}}"
                                                  hidden>{{ $message[0]['message'] }}</textarea>
                                    </div>
                                    <p id="intent_{{ $message[0]['id'] }}">{{ $message[0]['message'] }}
                                        <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true"
                                           onclick="editIntent('{{ $message[0]['message'] }}', {{ $message[0]['id'] }}, {{ $chatQuestions ? $chatQuestions['id'] : ''}}, 1)"></i>
                                    </p>
                                    @if($type != 'Database')
                                        <div id="intent_add_{{ $message[0]['id'] }}">
                                            <p>Do you store this question in database?</p>
                                            <button class="btn btn-secondary save-btn mt-0" onclick="storeIntent({{ $message[0]['id'] }}, {{ $chatQuestions ? $chatQuestions['id'] : ''}})">
                                                Yes
                                            </button>
                                            <button class="btn btn-secondary save-btn mt-0" onclick="closeIntent({{ $message[0]['id'] }})">No</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex intent-details align-items-center justify-content-end mb-2">
                                    <p class="mr-2 mb-0">Intent:</p><select class="form-control">
                                        <option>{{$chatQuestions ? $chatQuestions['value'] : ''}}</option>
                                    </select>
                                    <p class="mb-0 ml-3">Get Type: {{ $type }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="chat-bot-footer">
                        <div class="form-group position-relative text-right mt-4">
                            <button class="btn btn-secondary" onclick="getNewMessage()">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let pageNo = 1;
        var url = window.location.pathname;
        let intent = JSON.stringify({!! json_encode($allIntents) !!});
        let allIntents = JSON.parse(intent);
        // Define the regular expression pattern
        var regex = /\/simulator-messages\/([^\/]+)\/([^\/]+)/;

        // Execute the regular expression
        var matches = url.match(regex);

        if (matches && matches.length >= 3) {
            var object = matches[1];
            var object_id = matches[2];
        }

        function editIntent(message, messageId, questionId, isQuestion) {
            $(`#text_${messageId}`).removeAttr('hidden');
            $(`#intent_${messageId}`).remove();
            $(`#chat_message_${messageId} .chat-message .text-area`).append(isQuestion ? `<button class="save-btn btn ml-2 btn-secondary" onclick="storeIntent( ${messageId}, ${questionId})">save</button>` : `<button class="save-btn btn ml-2 btn-secondary mt-0" onclick="storeReplay( ${messageId})">save</button>`);
        }

        function storeIntent(messageId, QuestionId = null) {
            let message = $(`#text_${messageId}`).val();
            $.ajax({
                url: "{{ route('simulate.message.store.intent') }}",
                method: "POST",
                data: {
                    question_id: QuestionId,
                    _token: "{{ csrf_token() }}",
                    value: message,
                    object: object,
                    object_id: object_id,
                },
                success: function (response) {
                    $(`#intent_add_${messageId}`).remove();
                    $(`#text_${messageId}`).attr('hidden', true);
                    $(`#chat_message_${messageId} .chat-message .save-btn`).remove();
                    $(`#chat_message_${messageId} .chat-message`).append(`<p id="intent_${messageId}">${message}
                        <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true" onclick="editIntent('${message}', ${messageId}, ${QuestionId}, true)"></i>
                </p>`);
                    toastr["success"](response.message);
                },
                error: function (error) {
                    toastr["error"](error.message);
                    return;
                }
            })
        }

        function closeIntent(messageId) {
            $(`#intent_add_${messageId}`).remove();
        }

        function getNewMessage() {
            pageNo += 1;
            $.ajax({
                url: `{{ route('simulator.message.list') }}/${object}/${object_id}`,
                method: "GET",
                data: {
                    page_no: pageNo,
                    request_type: 'ajax',
                    _token: "{{ csrf_token() }}",
                    // question,
                    // googleAccount: googleAccountId
                },
                success: function (response) {
                    if (response.code === 400) {
                        toastr["error"](response.messages);
                        return;
                    }
                    if (!response.data) {
                        toastr["warning"](response.messages);
                        return;
                    }
                    let getStr = '';
                    if (response.data.message.inout === 'out') {
                        getStr = `<div id="chat_message_${response.data.message.id}" class="mb-3">
                            <div class="right-chat chat-message">
                                <div class="d-flex align-items-end text-area">
                                    <textarea class="form-control" id="text_${response.data.message.id}"
                                              hidden>${response.data.message.message}</textarea>
                                </div>
                                <p id="intent_${response.data.message.id}">${response.data.message.message}
                                    <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true"
                                       onclick="editIntent('${response.data.message.message}', ${response.data.message.id}, '${response.data.chatQuestion?.id || response.data.chatQuestion?.value}', true)"></i>
                                </p>`;

                        if (response.data.type != 'Database') {
                            getStr += `<div id="intent_add_${response.data.message.id}"><p>Do you save this question in database?</p>
                                <button class="btn btn-secondary save-btn mt-0" onclick="storeIntent(${response.data.message.id}, '${response.data.chatQuestion?.id || response.data.chatQuestion?.value}')">
                                    Yes
                                </button>
                                <button class="btn btn-secondary save-btn mt-0" onclick="closeIntent(${response.data.message.id})">No</button></div>`;
                        }

                        getStr += `</div>
                            <div class="d-flex intent-details align-items-center justify-content-end mb-2">
                                <p class="mr-2 mb-0">Intent:</p><select class="form-control">
                                <option>${response.data.chatQuestion?.value}</option>
                            </select>
                                <p class="mb-0 ml-3">Get Type: ${response.data.type}</p>
                            </div>
                        </div>`;
                    } else {
                        getStr = `<div id="chat_message_${response.data.message.id}">
                            <div class="left-chat chat-message">
                                <div class="d-flex align-items-end text-area">
                                    <textarea class="form-control" id="text_${response.data.message.id}"
                                          hidden>${response.data.message.message}</textarea>
                                </div>
                                <p id="intent_${response.data.message.id}">${response.data.message.message}
                                    <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true"
                                       onclick="editIntent('${response.data.message.message}', ${response.data.message.id}, '${response.data.chatQuestion?.id || response.data.chatQuestion?.value}', false)"></i>
                                </p>`;

                        if (response.data.type != 'Database') {
                            getStr += `<div id="intent_add_${response.data.message.id}"><p>Do you save this response in database?</p>
                                <button class="btn btn-secondary save-btn mt-0" onclick="storeReplay(${response.data.message.id})">
                                    Yes
                                </button>
                                <button class="btn btn-secondary save-btn mt-0" onclick="closeIntent(${response.data.message.id})">NO</button></div>`;
                        }

                        getStr += `</div>
                            <div class="d-flex intent-details align-items-center mb-2">
                                <p class="mr-2 mb-0">Intent:</p><select class="form-control" id="intent_selection_${response.data.message.id}"> <option value="">Please select intent</option>`;
                        Object.keys(allIntents).forEach(item => {
                            getStr += `<option ${item == response.data.chatQuestion?.id ? 'selected' : ''} value="${item}">${allIntents[item]}</option>`
                        });
                        getStr += `</select>
                                <p class="mb-0 ml-3">Get Type: ${response.data.type}</p>
                            </div>
                        </div>`;
                    }

                    $('.chat-bot-body').append(getStr);
                },
                error: function (error) {
                    $("#loading-image").hide();
                    toastr["error"](error.message);
                    return;
                }
            })

        }

        function storeReplay(messageId, QuestionId = null) {
            let message = $(`#text_${messageId}`).val();
            if (!QuestionId) {
                QuestionId = $(`#intent_selection_${messageId}`).val();
            }
            $.ajax({
                url: "{{ route('simulate.message.store.replay') }}",
                method: "POST",
                data: {
                    question_id: QuestionId,
                    _token: "{{ csrf_token() }}",
                    value: message,
                    object: object,
                    object_id: object_id,
                },
                success: function (response) {
                    if (response.code === 400) {
                        toastr["error"](response.message);
                        return;
                    }
                    $(`#intent_add_${messageId}`).remove();
                    $(`#text_${messageId}`).attr('hidden', true);
                    $(`#chat_message_${messageId} .chat-message .save-btn`).remove();
                    $(`#chat_message_${messageId} .chat-message `).append(`<p id="intent_${messageId}">${message}
                        <i class="fa fa-pencil-square-o cursor-pointer" aria-hidden="true" onclick="editIntent('${message}', '${messageId}', '${QuestionId}', false)"></i>
                </p>`);
                    $(`intent_add_${messageId}`).remove();
                    toastr["success"](response.message);
                },
                error: function (error) {
                    toastr["error"](error.message);
                    return;
                }
            })
        }
    </script>
@endsection
