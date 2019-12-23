@extends('layouts.app')

@section('title', 'Auto Replies - ERP Sololuxury')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }
        .fixed_header{
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody{
          display:block;
          width: 100%;
          overflow: auto;
          height: 250px;
        }

        .fixed_header thead tr {
           display: block;
        }

        .fixed_header thead {
          background: black;
          color:#fff;
        }

        .fixed_header th, .fixed_header td {
          padding: 5px;
          text-align: left;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Auto Replies</h2>
            <div class="pull-left">
                <div class="form-inline">
                    <input type="checkbox" id="turn_off_automated" name="show_automated_messages" value="" {{ $show_automated_messages == 1 ? 'checked' : '' }}>
                    <label for="#turn_off_automated">Show Automated Messages</label>

                    <span class="text-success change_status_message" style="display: none;">Successfully saved</span>
                </div>
                {{-- <form action="{{ route('review.index') }}" method="GET">
                  <div class="row">
                    <div class="col">
                      <div class="form-group">
                        <select class="form-control" name="platform">
                          <option value="">Select Platform</option>
                          <option value="instagram" {{ 'instagram' == $filter_platform ? 'selected' : '' }}>Instagram</option>
                          <option value="facebook" {{ 'facebook' == $filter_platform ? 'selected' : '' }}>Facebook</option>
                          <option value="sitejabber" {{ 'sitejabber' == $filter_platform ? 'selected' : '' }}>Sitejabber</option>
                          <option value="google" {{ 'google' == $filter_platform ? 'selected' : '' }}>Google</option>
                          <option value="trustpilot" {{ 'trustpilot' == $filter_platform ? 'selected' : '' }}>Trustpilot</option>
                        </select>
                      </div>
                    </div>

                    <div class="col">
                      <div class="form-group ml-3">
                        <div class='input-group date' id='filter_posted_date'>
                          <input type='text' class="form-control" name="posted_date" value="{{ $filter_posted_date }}" />

                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                    </div>
                  </div>
                </form> --}}
            </div>
            <div class="pull-right">
               <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#autoReplyCreateModal">Create</a>
                <button type="button" class="btn btn-secondary ml-3" onclick="addGroup()">Keyword Group</a>
                <button type="button" class="btn btn-secondary ml-3" onclick="addGroupPhrase()">Phrase Group</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div id="exTab2" class="container">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#text-autoreplies" data-toggle="tab">Text Auto Replies</a>
            </li>
            <li>
                <a href="#priority-customers" data-toggle="tab">Priority Customers</a>
            </li>
            <li>
                <a href="#auto-replies" data-toggle="tab">Auto Replies</a>
            </li>
            <li>
                <a href="#most-used-words" data-toggle="tab">Most Used Words</a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane active mt-3" id="text-autoreplies">
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Keyword</th>
                        <th>Reply</th>
                        {{-- <th>Actions</th> --}}
                    </tr>
                    </thead>

                    <tbody>
                    @php
                        $count = 0;
                    @endphp
                    @foreach ($simple_auto_replies as $reply => $data)
                        <tr>
                            <td>{{ $count + 1 }}</td>
                            <td>{{ $reply }}</td>
                            <td>
                                <ul>
                                    @foreach ($data as $key => $auto_reply)
                                        <li>
                                            {{ $auto_reply['keyword'] }}

                                            <button type="button" class="btn btn-image edit-auto-reply" data-toggle="modal" data-target="#autoReplyEditModal" data-reply="{{ json_encode($auto_reply) }}"><img src="/images/edit.png"/></button>

                                            {!! Form::open(['method' => 'DELETE','route' => ['autoreply.destroy', $auto_reply['id']],'style'=>'display:inline']) !!}
                                            <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                                            {!! Form::close() !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            @php
                                $count++;
                            @endphp
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $simple_auto_replies->appends(Request::except('page'))->links() !!}
        </div>

        <div class="tab-pane mt-3" id="priority-customers">
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Keyword</th>
                        <th>Sending Time</th>
                        <th>Repeat</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($priority_customers_replies as $key => $reply)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $reply->reply }}</td>
                            <td>{{ $reply->sending_time ? \Carbon\Carbon::parse($reply->sending_time)->format('H:i d-m') : '' }}</td>
                            <td>{{ $reply->repeat }}</td>
                            <td>
                                <button type="button" class="btn btn-image edit-auto-reply" data-toggle="modal" data-target="#autoReplyEditModal" data-reply="{{ $reply }}"><img src="/images/edit.png"/></button>

                                {!! Form::open(['method' => 'DELETE','route' => ['autoreply.destroy', $reply->id],'style'=>'display:inline']) !!}
                                <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $priority_customers_replies->appends(Request::except('priority-page'))->links() !!}
        </div>

        <div class="tab-pane mt-3" id="auto-replies">
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Used For</th>
                        <th width="60%">Reply</th>
                        <th width="10%">Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($auto_replies as $key => $reply)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $reply->keyword }}</td>
                            <td>
                    <span class="auto-reply-reply">
                      @php
                          preg_match_all('/({\w*})/', $reply->reply, $match);

                          $new_reply = $reply->reply;
                          foreach ($match[0] as $variable) {
                            $exploded_reply = explode($variable, $new_reply);
                            $new_variable = '<strong>' . $variable . '</strong>';
                            $new_reply = implode($new_variable, $exploded_reply);
                          }

                          // $new_reply = preg_replace('/\[/', '<strong>[</strong>', $new_reply);
                          // $new_reply = preg_replace('/\//', '<strong>/</strong>', $new_reply);
                          // $new_reply = preg_replace('/\]/', '<strong>]</strong>', $new_reply);
                      @endphp

                        {!! $new_reply !!}
                    </span>

                                <textarea name="reply" class="form-control auto-reply-textarea hidden" rows="4" cols="80" data-id="{{ $reply->id }}">{{ strip_tags($reply->reply) }}</textarea>
                            </td>
                            <td>
                                <button type="button" class="btn btn-image edit-auto-reply-button"><img src="/images/edit.png"/></button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $auto_replies->appends(Request::except('autoreply-page'))->links() !!}
        </div>

        <div class="tab-pane mt-3" id="most-used-words">
            <div class="table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="25%">Words</th>
                        <th width="25%">Total</th>
                        <th width="25%">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($mostUsedWords as $key => $words)
                        <tr>
                            <td><input type="checkbox" name="keyword" value="{{ $words->id }}">  {{ $words->word }}</td>
                            <td>{{ $words->total }}</td>
                            <td>
                                <button data-id="{{ $words->id }}" class="btn btn-image expand-row-btn"><img src="/images/forward.png"></button>
                                <button data-id="{{ $words->id }}" class="btn btn-image delete-row-btn"><img src="/images/delete.png"></button>
                            </td>
                        </tr>
                        <tr class="dis-none" id="phrases_{{ $words->id }}">
                            <td colspan="3">
                                <table class="fixed_header">
                                    <tbody>
                                        @foreach($words->pharases as $phrase)
                                            <tr colspan="3">
                                                <td><input type="checkbox" name="phrase" value="{{ $phrase->id }}" data-keyword="{{ $words->id }}">  {{ $phrase->phrase }}</td>
                                                <td>
                                                    <button data-id="{{ $phrase->chat_id }}" class="btn btn-image get-chat-details"><img src="/images/chat.png"></button>
                                                </td>
                                            </tr>
                                         @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $auto_replies->appends(Request::except('autoreply-page'))->links() !!}
        </div>
    </div>

    @include('autoreplies.partials.autoreply-modals')
    @include('partials.chat-history')
    @include('autoreplies.partials.group')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $('#sending-datetime, #edit-sending-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $(document).on('click', '.edit-auto-reply', function () {
            var autoreply = $(this).data('reply');
            var url = "{{ url('autoreply') }}/" + autoreply.id;
            $('#autoReplyEditModal form').attr('action', url);
            $('#autoreply_type').val(autoreply.type);
            $('#autoreply_keyword').val(autoreply.keyword);
            $('#autoreply_reply').val(autoreply.reply);
            $('#autoreply_sending_time').val(autoreply.sending_time);
            $('#autoreply_repeat').val(autoreply.repeat);
            if (autoreply.is_active == 1) {
                $('#autoreply_is_active').prop("checked", true);
            }
        });

        $('#turn_off_automated').on('click', function () {
            var checked = $(this).prop('checked');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('settings.update.automessages') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    value: checked ? 1 : 0
                }
            }).done(function () {
                $(thiss).siblings('.change_status_message').fadeIn(400);

                setTimeout(function () {
                    $(thiss).siblings('.change_status_message').fadeOut(400);
                }, 2000);
            }).fail(function (response) {
                console.log(response);

                alert('Could not saved the changes');
            })
        });

        $('.edit-auto-reply-button').on('click', function () {
            $(this).closest('tr').find('textarea[name="reply"]').toggleClass('hidden');
            $(this).closest('tr').find('textarea[name="reply"]').siblings('.auto-reply-reply').toggleClass('hidden');
        });

        $('.auto-reply-textarea').keypress(function (e) {
            var key = e.which;
            var thiss = $(this);
            var id = $(this).data('id');

            if (key == 13) {
                e.preventDefault();
                var reply = $(thiss).val();

                $.ajax({
                    type: 'POST',
                    url: "{{ url('autoreply') }}/" + id + '/updateReply',
                    data: {
                        _token: "{{ csrf_token() }}",
                        reply: reply,
                    }
                }).done(function () {
                    $(thiss).addClass('hidden');
                    $(thiss).siblings('.auto-reply-reply').text(reply);
                    $(thiss).siblings('.auto-reply-reply').removeClass('hidden');
                }).fail(function (response) {
                    console.log(response);

                    alert('Could not update reply');
                });
            }
        });

        $(document).on("click",".expand-row-btn",function() {
            var dataId = $(this).data("id");
            $("#phrases_"+dataId).toggleClass("dis-none");
        });

        $(document).on("click",".delete-row-btn",function() {
            var $this = $(this);
            var dataId = $(this).data("id");
            $.ajax({
                type: 'POST',
                url: "autoreply/delete-chat-word",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: dataId,
                }
            }).done(function () {
                $this.closest("tr").remove();
                $("#phrases_"+dataId).remove();
            }).fail(function (response) {
            });
        });

        $(document).on("click",".get-chat-details",function() {
            var chatId = $(this).data("id");
            $.ajax({
                type: 'GET',
                url: "autoreply/replied-chat/"+chatId,
                dataType : "json"
            }).done(function (response) {
                var html = "";
                $.each(response.data,function(k,v) {
                    html += '<div class="bubble">';
                    html += '<div class="txt">';
                    html += '<p class="name"></p>';
                    html += '<p class="message" data-message="'+v.message+'">'+v.message+'</p><br>';
                    html += '<span class="timestamp">'+v.created_at+'</span><span>';
                    html += '<a href="javascript:;" class="btn btn-xs btn-default ml-1 set-autoreply" data-q="'+response.question+'" data-a="'+v.message+'">+ Auto Reply</a></span>';
                    html += '</div>';
                    html += '</div>';
                });

                $("#chat-list-history").find(".modal-body").find(".speech-wrapper").html(html);
                $("#chat-list-history").find(".modal-title").html(response.question);
                $("#chat-list-history").modal("show");
                

            }).fail(function (response) {
            });
        });

        $(document).on("click",".set-autoreply",function() {
            $.ajax({
                type: 'POST',
                url: "autoreply/save-by-question",
                data: {
                    _token: "{{ csrf_token() }}",
                    q: $(this).data("q"),
                    a: $(this).data("a")
                }
            }).done(function () {
                toastr['success']('Auto Reply added successfully', 'success');
            }).fail(function (response) {
            });
        });


        function addGroup(){
            var id = [];
            $.each($("input[name='keyword']:checked"), function(){
                id.push($(this).val());
            });
            if(id.length == 0){
                alert('Please Select Keyword');
            }else{
                $('#groupCreateModal').modal('show');
            }
        }

        function addGroupPhrase(){
             var phraseId = [];
            $.each($("input[name='phrase']:checked"), function(){
                phraseId.push($(this).val());
            });
           if(phraseId.length == 0){
                alert('Please Select Phrase From Keywords');
            }else{
                $('#groupPhraseCreateModal').modal('show');
            }
        }

        function createGroup(){
             var id = [];
             name = $('#keywordname').val();
            $.each($("input[name='keyword']:checked"), function(){
                id.push($(this).val());
            });
            if(id.length == 0){
                alert('Please Select Keyword');
            }else{
                $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('autoreply.save.group') }}',
                data: {
                    id: id,
                    name : name,
                },
                }).done(response => {
                    alert('Added Group');
                }).fail(function (response) {
                    alert('Could not add group!');
                });
            }
        }


        function createGroupPhrase() {
            var phraseId = [];
            name = $('#phrasename').val();
            $.each($("input[name='phrase']:checked"), function(){
                phraseId.push($(this).val());
                keyword = $(this).attr("data-keyword")
            });
            
            if(phraseId.length == 0){
                alert('Please Select Phrase From Keywords');
            }else{
                $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('autoreply.save.group.phrases') }}',
                data: {
                    phraseId: phraseId,
                    keyword : keyword,
                    name : name,
                },
                }).done(response => {
                    alert('Added Phrase Group');
                }).fail(function (response) {
                    alert('Could not add Phrase group!');
                });
            }
        }
    </script>
@endsection
