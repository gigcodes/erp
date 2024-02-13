@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', 'Message List | Inbox')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }

        form.chatbot .col {
            flex-grow: unset !important;
        }

    </style>
@endsection

@section('content')
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Message List | Inbox</h2>
        </div>
    </div>

    <div class="row m-0">
        <div class="col-md-12 pl-3 pr-3">
            <div class="table-responsive-lg" id="page-view-result">
                @include("instagram.partials.message")
            </div>
        </div>
    </div>

    <div id="contact-chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
      50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script src="/js/bootstrap-toggle.min.js"></script>
    <script type="text/javascript" src="/js/jsrender.min.js"></script>
    <script type="text/javascript" src="/js/common-helper.js"></script>

    <script>
      $(document).on("click", ".load-contact-communication-modal", function() {
        $("#loading-image").show();
        const data = $(this).data("id");

        $.ajax({
          url: "{{ route('social.message.list') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            id: data
          },
          success: function(response) {
            const res = response.messages;

            $("#loading-image").hide();
            $("#contact-chat-list-history .modal-body").empty();
            if (res.social_contact_thread.length > 0) {

              $(res.social_contact_thread).each(function(key, value) {
                let sentBy = "";
                if (value.type == 1) {
                  sentBy =
                    `From ${res.social_config.name} To ${res.name} On ${value.sending_at}`;
                } else {
                  sentBy =
                    `From ${res.name} To ${res.social_config.name} On ${value.sending_at}`;
                }

                $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td style="width:50%">${value.text}</td>
                                    <td style="width:50%">${sentBy}</td>
                                </tr>
                            </table>
                            `);
              });
            } else {
              $("#contact-chat-list-history .modal-body").append(`
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="2">No conversations found</td>
                                </tr>
                            </table>
                            `);
            }
            $("#contact-chat-list-history").modal("show");
          },
          error: function(error) {
            alert("Counldn't load messages");
            $("#loading-image").hide();
          }
        });
      });
    </script>
@endsection
